<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['category_id', 'brand_id', 'name', 'slug', 'brand', 'short_description', 'description', 'price', 'discount_price', 'discount_start', 'discount_end', 'weight', 'stock', 'sku', 'condition', 'sold_count', 'status'])]
class Product extends Model
{
    use SoftDeletes;
    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'weight' => 'integer',
        'stock' => 'integer',
        'status' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function isDiscountActive(): bool
    {
        if (is_null($this->discount_price)) {
            return false;
        }

        $now = now();
        
        $startOk = is_null($this->discount_start) || $this->discount_start <= $now;
        $endOk = is_null($this->discount_end) || $this->discount_end >= $now;

        return $startOk && $endOk;
    }

    public function getActivePriceAttribute()
    {
        return $this->isDiscountActive() ? $this->discount_price : $this->price;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function videos()
    {
        return $this->hasMany(ProductVideo::class);
    }

    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
