<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopupBanner extends Model
{
    protected $fillable = [
        'title',
        'image',
        'link',
        'is_active',
        'cooldown_days'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cooldown_days' => 'integer',
    ];
}
