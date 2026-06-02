<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['code', 'type', 'value', 'min_purchase', 'max_discount', 'start_date', 'end_date', 'status'])]
class Coupon extends Model
{
    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'status' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_coupons')
                    ->withPivot('discount')
                    ->withTimestamps();
    }
}
