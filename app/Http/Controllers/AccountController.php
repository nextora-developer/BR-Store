<?php

namespace App\Http\Controllers;

use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index(Request $request)
    {

        $user = auth()->user();

        // 真实统计
        $stats = [
            'orders'    => $user->orders()->count() ?? 0,
            'favorites' => $user->favorites()->count() ?? 0,
            'addresses' => $user->addresses()->count() ?? 0,
        ];

        $stats['points'] = (int) ($user->points_balance ?? 0);

        $pointTransactions = PointTransaction::where('user_id', $user->id)
            ->whereIn('source', ['purchase', 'redeem'])
            ->latest()
            ->limit(5)
            ->get();


        $latestOrders = $user->orders()
            ->latest()
            ->take(3)
            ->get();

        return view('account.index', compact('user', 'stats', 'latestOrders', 'pointTransactions'));
    }
}
