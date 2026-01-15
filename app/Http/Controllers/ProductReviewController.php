<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReviewController extends Controller
{
    public function store(Request $request, OrderItem $item)
    {
        $user = $request->user();

        // ✅ 只能 review 自己的 item
        abort_unless($item->order->user_id === $user->id, 403);

        // ✅ 订单必须 completed
        abort_unless($item->order->status === 'completed', 403);

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // ✅ 先挡一次（DB unique 也会挡）
        if ($item->review()->exists()) {
            return back()->with('status', 'You already reviewed this item.');
        }

        // ✅ points 规则：固定 20（你要改就改这里）
        $points = 20;

        DB::transaction(function () use ($user, $item, $data, $points) {
            $item->review()->create([
                'user_id'        => $user->id,
                'product_id'     => $item->product_id,
                'rating'         => $data['rating'],
                'comment'        => $data['comment'] ?? null,
                'points_awarded' => $points,
                'is_verified'    => true,
                'is_visible'     => true,
            ]);

            // ✅ 直接加到 balance（你如果有 points log，可以在这里顺便写）
            $user->increment('points_balance', $points);
        });

        return back()->with('status', "Review submitted! +{$points} pts");
    }
}
