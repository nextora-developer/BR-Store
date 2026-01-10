<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Cart;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:50']]);

        $code = strtoupper(trim($request->input('code')));

        $voucher = Voucher::where('code', $code)->first();
        if (!$voucher || !$voucher->isAvailable()) {
            return response()->json(['message' => 'Voucher is invalid or unavailable.'], 422);
        }

        $cart = Cart::with('items')->where('user_id', auth()->id())->first();
        $subtotal = $cart ? $cart->items->sum(fn($i) => $i->unit_price * $i->qty) : 0;

        if ($voucher->min_spend !== null && $subtotal < (float)$voucher->min_spend) {
            return response()->json(['message' => 'Minimum spend not reached for this voucher.'], 422);
        }

        // 每用户一次（pivot voucher_user）
        $alreadyUsed = $voucher->users()->where('user_id', auth()->id())->exists();
        if ($alreadyUsed) {
            return response()->json(['message' => 'You have already used this voucher.'], 422);
        }

        // ✅ 新增：benefit（order / free_shipping）
        $benefit = $voucher->benefit ?? 'order';

        // ✅ order 才在这里算 discount；free_shipping 在 checkout 才算 shippingDiscount
        $discount = 0;
        if ($benefit === 'order') {
            $discount = $voucher->calculateDiscount((float)$subtotal);
        }

        session(['applied_voucher' => [
            'voucher_id' => $voucher->id,
            'code'       => $voucher->code,
            'benefit'    => $benefit,     // ✅ 关键
            'discount'   => $discount,    // order: 有值；free_shipping: 0
        ]]);

        return response()->json([
            'ok'       => true,
            'code'     => $voucher->code,
            'benefit' => $voucher->benefit ?? 'order',
            'discount' => $discount,
        ]);
    }

    public function remove()
    {
        session()->forget('applied_voucher');
        return response()->json(['ok' => true]);
    }
}
