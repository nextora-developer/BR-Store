<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherPageController extends Controller
{
    public function index(Request $request)
    {
        $q = Voucher::query()->where('is_active', true);

        // 时间有效期
        $q->where(function ($qq) {
            $qq->whereNull('starts_at')->orWhere('starts_at', '<=', now());
        })->where(function ($qq) {
            $qq->whereNull('expires_at')->orWhere('expires_at', '>=', now());
        });

        // 搜索（可选）
        if ($request->filled('q')) {
            $kw = trim($request->string('q'));
            $q->where(function ($qq) use ($kw) {
                $qq->where('code', 'like', "%{$kw}%")
                    ->orWhere('name', 'like', "%{$kw}%");
            });
        }

        $vouchers = $q->latest()->paginate(12)->withQueryString();

        // ✅ 可选：登录用户显示“已使用”
        $usedIds = [];
        if (auth()->check()) {
            // 假设你有 voucher_user pivot: $voucher->users()
            // 如果你不是这个关系，告诉我你的结构我改
            $usedIds = auth()->user()
                ->vouchers()
                ->pluck('vouchers.id')
                ->toArray();
        }

        return view('pages.vouchers.index', compact('vouchers', 'usedIds'));
    }
}
