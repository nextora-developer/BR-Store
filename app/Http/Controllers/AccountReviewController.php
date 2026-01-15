<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class AccountReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $items = OrderItem::query()
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('status', 'completed');
            })
            ->whereDoesntHave('review')
            ->with(['order:id,order_no,status,created_at', 'product:id,name,image'])
            ->latest('id')
            ->paginate(10);

        return view('account.reviews.index', compact('items'));
    }
}
