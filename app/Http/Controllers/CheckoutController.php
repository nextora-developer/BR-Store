<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\ShippingRate;
use App\Models\Voucher;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\OrderPlacedMail;
use App\Mail\AdminOrderNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::with(['items.product'])
            ->where('user_id', auth()->id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $items    = $cart->items;
        $subtotal = $items->sum(fn($i) => $i->unit_price * $i->qty);

        $user           = auth()->user();
        $defaultAddress = $user?->defaultAddress;
        $addresses      = $user?->addresses ?? collect();

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderByDesc('is_default')
            ->get();

        // ✅ 有没有实体商品
        $hasPhysical = $items->contains(function ($item) {
            return !$item->product->is_digital;   // 没勾 digital = 实体
        });

        // ✅ 先给 shippingFee = null，表示“待计算”
        $shippingFee = null;

        // ✅ 把 rate 丢给前端，用 JS 算（west_my / east_my）
        $shippingRates = $hasPhysical
            ? ShippingRate::pluck('rate', 'code')   // ['west_my' => 8, 'east_my' => 15, ...]
            : collect();                             // 全部 digital 就不用运费了

        $states = [
            // West Malaysia
            ['name' => 'Johor',           'zone' => 'west_my'],
            ['name' => 'Kedah',           'zone' => 'west_my'],
            ['name' => 'Kelantan',        'zone' => 'west_my'],
            ['name' => 'Melaka',          'zone' => 'west_my'],
            ['name' => 'Negeri Sembilan', 'zone' => 'west_my'],
            ['name' => 'Pahang',          'zone' => 'west_my'],
            ['name' => 'Perak',           'zone' => 'west_my'],
            ['name' => 'Perlis',          'zone' => 'west_my'],
            ['name' => 'Penang',          'zone' => 'west_my'],
            ['name' => 'Selangor',        'zone' => 'west_my'],
            ['name' => 'Terengganu',      'zone' => 'west_my'],
            ['name' => 'Kuala Lumpur',    'zone' => 'west_my'],
            ['name' => 'Putrajaya',       'zone' => 'west_my'],

            // East Malaysia
            ['name' => 'Sabah',           'zone' => 'east_my'],
            ['name' => 'Sarawak',         'zone' => 'east_my'],
            ['name' => 'Labuan',          'zone' => 'east_my'],
        ];

        return view('checkout.index', compact(
            'items',
            'subtotal',
            'defaultAddress',
            'addresses',
            'paymentMethods',
            'shippingFee',
            'shippingRates',
            'hasPhysical',
            'states',
        ));
    }



    public function store(Request $request)
    {
        /**
         * 1️⃣ 验证规则（HitPay 不需要收据，Bank Transfer 才强制收据）
         */
        $rules = [
            'name'           => 'required',
            'phone'          => 'required',
            'email'          => 'required|email',
            'address_line1'  => 'required',
            'postcode'       => 'required',
            'city'           => 'required',
            'state'          => 'required',
            'country'        => 'required',
            'payment_method' => 'required|exists:payment_methods,code',
            'remark'         => 'nullable|string|max:500',
            'points_redeem'  => 'nullable|integer|min:0',
        ];

        // 默认：收据可空（给 HitPay 用）
        $rules['payment_receipt'] = 'nullable|image|max:4096';

        // Bank Transfer（online_transfer）才强制上传收据
        if ($request->input('payment_method') === 'online_transfer') {
            $rules['payment_receipt'] = 'required|image|max:4096';
        }

        $request->validate($rules);


        /**
         * 2️⃣ 读取 Payment Method
         */
        $paymentMethod = PaymentMethod::where('code', $request->payment_method)
            ->where('is_active', true)
            ->firstOrFail();


        /**
         * 3️⃣ 读取购物车 + 计算金额
         */
        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $items    = $cart->items;
        $subtotal = (float) $items->sum(fn($i) => $i->unit_price * $i->qty);

        // 是否包含实体产品
        $hasPhysical = $items->contains(fn($item) => !$item->product->is_digital);

        $shippingFee = 0;

        if ($hasPhysical) {
            $eastStates = ['Sabah', 'Sarawak', 'Labuan'];

            $zoneCode = in_array($request->state, $eastStates)
                ? 'east_my'
                : 'west_my';

            $shippingFee = (float) (ShippingRate::where('code', $zoneCode)->value('rate') ?? 0);
        } else {
            $shippingFee = (float) (ShippingRate::where('code', 'digital')->value('rate') ?? 0);
        }

        /**
         * ✅ 3.5️⃣ Voucher（从 session 读，但必须在后端“重算 + 重验”）
         */
        /**
         * ✅ 3.5️⃣ Voucher（从 session 读，但必须在后端“重算 + 重验”）
         */
        $applied   = session('applied_voucher'); // ['voucher_id','code','benefit','discount']
        $voucherId = $applied['voucher_id'] ?? null;

        $voucherCode = null;
        $voucherDiscount = 0.0;
        $shippingDiscount = 0.0;   // ✅ 新增：运费折扣
        $voucher = null;

        if ($voucherId) {
            $voucher = Voucher::find($voucherId);

            if (
                $voucher
                && $voucher->isAvailable()
                && ($voucher->min_spend === null || $subtotal >= (float)$voucher->min_spend)
                && !($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit)
            ) {
                $alreadyUsed = $voucher->users()->where('user_id', auth()->id())->exists();

                if (! $alreadyUsed) {
                    $voucherCode = $voucher->code;

                    $benefit = $voucher->benefit ?? 'order'; // order | free_shipping

                    if ($benefit === 'free_shipping') {
                        // ✅ 免运费：把 shipping 抵掉（只在有实体商品时才有意义）
                        if ($hasPhysical && $shippingFee > 0) {
                            $shippingDiscount = $shippingFee;
                        }
                    } else {
                        // ✅ 默认：扣 subtotal
                        $voucherDiscount = (float) $voucher->calculateDiscount($subtotal);
                    }
                }
            }
        }

        $payableSubtotal = max(0, $subtotal - $voucherDiscount);
        $payableShipping = max(0, $shippingFee - $shippingDiscount);

        $pointsRedeem = (int) floor($request->input('points_redeem', 0));

        $user = $request->user();
        $availablePoints = (int) ($user->points_balance ?? 0);

        $maxPointsBySubtotal = (int) floor($payableSubtotal * 100);

        // ✅ 只要输入 > 0 就当作要用
        if ($pointsRedeem > 0) {
            $pointsRedeem = min($pointsRedeem, $availablePoints, $maxPointsBySubtotal);
        } else {
            $pointsRedeem = 0;
        }

        $pointsDiscount = min($pointsRedeem / 100, $payableSubtotal);
        $payableSubtotal = max(0, $payableSubtotal - $pointsDiscount);

        $total = $payableSubtotal + $payableShipping;




        /**
         * 4️⃣ 处理收据文件（HitPay 通常不会有）
         */
        $receiptPath = null;

        if ($request->hasFile('payment_receipt')) {
            $receiptPath = $request->file('payment_receipt')
                ->store('payment_receipts', 'public');
        }


        /**
         * 5️⃣ 生成订单编号
         */
        do {
            $orderNo = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_no', $orderNo)->exists());


        /**
         * 6️⃣ 建立订单（事务）
         */
        $order = null;

        DB::transaction(function () use (
            $request,
            $items,
            $subtotal,
            $shippingFee,
            $paymentMethod,
            $receiptPath,
            $cart,
            $orderNo,
            $total,
            $voucher,
            $voucherCode,
            $voucherDiscount,
            $shippingDiscount,
            $pointsRedeem,
            $pointsDiscount,
            &$order
        ) {
            $order = Order::create([
                'order_no'            => $orderNo,
                'user_id'             => auth()->id(),
                'customer_name'       => $request->name,
                'customer_phone'      => $request->phone,
                'customer_email'      => $request->email,
                'address_line1'       => $request->address_line1,
                'address_line2'       => $request->address_line2,
                'postcode'            => $request->postcode,
                'city'                => $request->city,
                'state'               => $request->state,
                'country'             => $request->country,
                'subtotal'            => $subtotal,
                'shipping_fee'        => $shippingFee,
                'voucher_id'           => $voucher?->id,
                'voucher_code'         => $voucherCode,
                'voucher_discount'     => $voucherDiscount,
                'shipping_discount'    => $shippingDiscount,
                'total'               => $total,
                'status'              => 'pending',
                'payment_method_code' => $paymentMethod->code,
                'payment_method_name' => $paymentMethod->name,
                'payment_receipt_path' => $receiptPath,
                'remark'               => $request->input('remark'),
                'points_redeem'   => $pointsRedeem,
                'points_discount' => $pointsDiscount,
            ]);

            if ($pointsRedeem > 0) {
                $lockedUser = User::whereKey(auth()->id())->lockForUpdate()->first();

                // ✅ 防重复：同一订单同一用户只扣一次
                $already = PointTransaction::where('source', 'redeem')
                    ->where('order_id', $order->id)
                    ->where('user_id', $lockedUser->id)
                    ->exists();

                if ($already) {
                    throw new \Exception('Points already redeemed for this order.');
                }

                // ✅ 再检查余额
                $safeBalance = (int) ($lockedUser->points_balance ?? 0);
                if ($safeBalance < $pointsRedeem) {
                    throw new \Exception('Insufficient points balance.');
                }

                PointTransaction::create([
                    'user_id'  => $lockedUser->id,
                    'type'     => 'spend',
                    'source'   => 'redeem',
                    'order_id' => $order->id,
                    'points'   => $pointsRedeem,
                    'note'     => 'Redeem points on checkout (1 pt = RM 0.01)',
                ]);

                $lockedUser->decrement('points_balance', $pointsRedeem);
            }



            foreach ($items as $item) {
                $order->items()->create([
                    'product_id'         => $item->product_id,
                    'product_name'       => $item->product->name ?? '',
                    'qty'                => $item->qty,
                    'unit_price'         => $item->unit_price,
                    'product_variant_id' => $item->product_variant_id ?? null,
                    'variant_label'      => $item->variant_label ?? null,
                ]);
            }

            if ($voucher && (($voucherDiscount > 0) || ($shippingDiscount > 0))) {
                $voucher->increment('used_count');

                $voucher->users()->syncWithoutDetaching([
                    auth()->id() => ['used_at' => now()],
                ]);
            }


            $cart->items()->delete();
        });

        // ✅ 清掉已用 voucher session（避免下一单还带着）
        session()->forget('applied_voucher');



        // 7️⃣ 发邮件
        $isRM = $paymentMethod->code === 'revenue_monster';

        if ($order) {
            Log::info('Checkout order created: ' . $order->order_no);
            Log::info('Config admin_address is: ' . (config('mail.admin_address') ?? 'NULL'));

            // ❌ Revenue Monster：不在这里发邮件（等 webhook）
            if (! $isRM) {

                // ✅ Customer mail
                if ($order->customer_email) {
                    try {
                        Log::info('Sending customer email for order: ' . $order->order_no . ' -> ' . $order->customer_email);
                        Mail::to($order->customer_email)->send(new OrderPlacedMail($order));
                        Log::info('✅ Customer email sent OK: ' . $order->order_no);
                    } catch (\Throwable $e) {
                        Log::error('❌ Customer email failed ' . $order->order_no . ' : ' . $e->getMessage());
                    }
                }

                // ✅ Admin mail
                $admin = config('mail.admin_address');
                if ($admin) {
                    try {
                        Log::info('Sending admin email for order: ' . $order->order_no . ' -> ' . $admin);
                        Mail::to($admin)->send(new AdminOrderNotificationMail($order));
                        Log::info('✅ Admin email sent OK: ' . $order->order_no);
                    } catch (\Throwable $e) {
                        Log::error('❌ Admin email failed ' . $order->order_no . ' : ' . $e->getMessage());
                    }
                } else {
                    Log::warning('⚠️ Admin email skipped, admin_address is empty for ' . $order->order_no);
                }
            }
        }

        /**
         * 8️⃣ Revenue Monster 付款方式：下单完成后直接跳 RM
         */
        if ($isRM) {
            return redirect()->route('pay.rm', $order);
        }


        /**
         * 9️⃣ 其他付款方式 → Checkout Success Page
         */
        return redirect()
            ->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        abort_if($order->user_id !== auth()->id(), 403);

        return view('checkout.success', compact('order'));
    }
}
