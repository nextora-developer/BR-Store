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

        // ✅ 用真实 cart subtotal（不要用 session 假数据）
        $cart = Cart::with('items')->where('user_id', auth()->id())->first();
        $subtotal = $cart ? $cart->items->sum(fn($i) => $i->unit_price * $i->qty) : 0;

        if ($voucher->min_spend !== null && $subtotal < (float)$voucher->min_spend) {
            return response()->json(['message' => 'Minimum spend not reached for this voucher.'], 422);
        }

        // ✅ 如果你要限制每用户一次（你的 pivot 是 voucher_user）
        $alreadyUsed = $voucher->users()->where('user_id', auth()->id())->exists();
        if ($alreadyUsed) {
            return response()->json(['message' => 'You have already used this voucher.'], 422);
        }

        $discount = $voucher->calculateDiscount((float)$subtotal);

        session(['applied_voucher' => [
            'voucher_id' => $voucher->id,
            'code' => $voucher->code,
            'discount' => $discount,
        ]]);

        return response()->json([
            'ok' => true,
            'code' => $voucher->code,
            'discount' => $discount,
        ]);
    }

    public function remove()
    {
        session()->forget('applied_voucher');
        return response()->json(['ok' => true]);
    }
}
