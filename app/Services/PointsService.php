<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\ReferralLog;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PointsService
{
    public function creditReferral(
        User $referrer,
        ReferralLog $log,
        Order $order,
        int $points,
        string $note
    ): bool {
        return DB::transaction(function () use ($referrer, $log, $order, $points, $note) {

            if ($log->rewarded) {
                return false;
            }

            $lockedUser = User::whereKey($referrer->id)
                ->lockForUpdate()
                ->first();

            PointTransaction::create([
                'user_id'         => $lockedUser->id,
                'type'            => 'earn',
                'source'          => 'referral',
                'referral_log_id' => $log->id,
                'order_id'        => $order->id,
                'points'          => $points,
                'note'            => $note,
            ]);

            $lockedUser->increment('points_balance', $points);

            $log->update([
                'rewarded'      => true,
                'reward_type'   => 'points',
                'reward_amount' => $points,
                'order_id'      => $order->id,
            ]);

            return true;
        });
    }

    public function creditPurchase(
        User $buyer,
        Order $order,
        int $points,
        string $note = 'Purchase cashback (RM 1 = 1 point)'
    ): bool {
        return DB::transaction(function () use ($buyer, $order, $points, $note) {

            $exists = PointTransaction::where('source', 'purchase')
                ->where('order_id', $order->id)
                ->where('user_id', $buyer->id)
                ->exists();

            if ($exists) return false;

            $lockedBuyer = User::whereKey($buyer->id)->lockForUpdate()->first();

            PointTransaction::create([
                'user_id'  => $lockedBuyer->id,
                'type'     => 'earn',
                'source'   => 'purchase',
                'order_id' => $order->id,
                'points'   => $points,
                'note'     => $note,
            ]);

            $lockedBuyer->increment('points_balance', $points);

            return true;
        });
    }

    public static function grantBirthdayPointsIfEligible($user, int $points = 50): bool
    {
        if (!$user || empty($user->birth_date)) return false;

        $today = Carbon::today();
        $bday  = Carbon::parse($user->birth_date);

        // ✅ 生日当天（只比月日）
        if ($today->format('m-d') !== $bday->format('m-d')) {
            return false;
        }

        $year = $today->year;
        $note = "Birthday reward {$year} (+{$points} pts)";

        // ✅ 同一年只能领一次
        $exists = PointTransaction::where('user_id', $user->id)
            ->where('type', 'earn')
            ->where('source', 'birthday')
            ->where('note', $note)
            ->exists();

        if ($exists) return false;

        DB::transaction(function () use ($user, $points, $note) {
            PointTransaction::create([
                'user_id'         => $user->id,
                'type'            => 'earn',
                'source'              => 'birthday',
                'referral_log_id' => null,
                'order_id'        => null,
                'points'          => $points,
                'note'            => $note,
            ]);

            // 如果你有 points_balance 缓存
            if (isset($user->points_balance)) {
                $user->increment('points_balance', $points);
            }
        });

        return true;
    }
}
