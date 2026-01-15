<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'highlights',
        'specs',
        'price',
        'stock',
        'is_digital',
        'has_variants',
        'is_active',
        'image'
    ];

    protected $casts = [
        'specs' => 'array',
        'highlights' => 'array',
    ];


    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\ProductReview::class)
            ->where('is_visible', true);
    }

    public function avgRating(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float)$avg, 1) : 0.0;
    }

    public function reviewCount(): int
    {
        return (int)$this->reviews()->count();
    }
}
