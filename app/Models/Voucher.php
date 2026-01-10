<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_spend',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
        'min_spend' => 'decimal:2',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'voucher_user')
            ->withPivot('used_at');
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) return 0;

        if ($this->type === 'fixed') {
            return (float) min($this->value, $subtotal);
        }

        // percent
        $discount = $subtotal * ((float)$this->value / 100);
        return (float) round($discount, 2);
    }
}
