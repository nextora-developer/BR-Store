<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralLog extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_user_id',
        'order_id',
        'rewarded',
        'reward_type',
        'reward_amount',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
