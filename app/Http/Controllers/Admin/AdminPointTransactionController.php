<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use Illuminate\Http\Request;

class AdminPointTransactionController extends Controller
{
    public function index(Request $request)
    {
        $q = PointTransaction::query()
            ->with([
                'user:id,name,email',
                'order:id,order_no',
                'referralLog.referrer:id,name,email',
                'referralLog.referredUser:id,name,email',
            ])
            ->latest();


        if ($request->filled('keyword')) {
            $kw = trim($request->input('keyword'));
            $q->where(function ($qq) use ($kw) {
                $qq->whereHas('user', function ($u) use ($kw) {
                    $u->where('name', 'like', "%{$kw}%")
                        ->orWhere('email', 'like', "%{$kw}%");
                })->orWhereHas('order', function ($o) use ($kw) {
                    $o->where('order_no', 'like', "%{$kw}%");
                })->orWhere('note', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('source')) {
            $q->where('source', $request->input('source'));
        }

        if ($request->filled('type')) {
            $q->where('type', $request->input('type'));
        }

        if ($request->filled('from')) {
            $q->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $q->whereDate('created_at', '<=', $request->input('to'));
        }

        // ✅ 这里才是分页结果
        $pointTransactions = $q->paginate(20)->withQueryString();

        $sources = PointTransaction::select('source')
            ->whereNotNull('source')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        return view('admin.points.index', compact('pointTransactions', 'sources'));
    }

    public function show(PointTransaction $pointTransaction)
    {
        $tx = $pointTransaction->load([
            'user:id,name,email',
            'order:id,order_no,total,status',
            'referralLog.referrer:id,name,email',
            'referralLog.referredUser:id,name,email',
        ]);

        return view('admin.points.show', compact('tx'));
    }
}
