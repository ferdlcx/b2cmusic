<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class OrderCoupon extends Model
{
    protected $table = 'order_coupons';
    protected $fillable = ['order_id', 'coupon_id', 'discount'];

    protected $casts = [
        'discount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
