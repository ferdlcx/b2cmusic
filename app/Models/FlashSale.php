<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'start_time', 'end_time', 'status'])]
class FlashSale extends Model
{
    protected $casts = [
        'status' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now());
    }
}
