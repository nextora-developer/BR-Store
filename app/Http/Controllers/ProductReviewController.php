<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReviewController extends Controller
{
    public function store(Request $request, OrderItem $item)
    {
        $user = $request->user();

        abort_unless($item->order->user_id === $user->id, 403);
        abort_unless($item->order->status === 'completed', 403);

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($item->review()->exists()) {
            return back()->with('status', 'You already reviewed this item.');
        }

        $points = 20;

        DB::transaction(function () use ($user, $item, $data, $points) {

            $review = $item->review()->create([
                'user_id'        => $user->id,
                'product_id'     => $item->product_id,
                'rating'         => $data['rating'],
                'comment'        => $data['comment'] ?? null,
                'points_awarded' => $points,
                'is_verified'    => true,
                'is_visible'     => true,
            ]);

            // ✅ 现在唯一规则是 (source, order_item_id, user_id)
            $exists = PointTransaction::where('user_id', $user->id)
                ->where('type', 'earn')
                ->where('source', 'review')
                ->where('order_item_id', $item->id)
                ->exists();

            if (!$exists) {
                PointTransaction::create([
                    'user_id'        => $user->id,
                    'type'           => 'earn',
                    'source'         => 'review',
                    'referral_log_id' => null,
                    'order_id'       => $item->order_id,  // ✅ 保留，方便统计按订单
                    'order_item_id'  => $item->id,        // ✅ 新增
                    'points'         => $points,
                    'note'           => "Product review +{$points} pts (OrderItem #{$item->id})",
                ]);

                if (isset($user->points_balance)) {
                    $user->increment('points_balance', $points);
                }
            }
        });


        return back()->with('review_success', [
            'points' => $points,
            'title'  => 'Review submitted',
            'text'   => 'Thanks for your feedback!',
        ]);
    }
}
