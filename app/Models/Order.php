<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'shipping_address_id', 'order_code', 'subtotal', 
        'shipping_cost', 'discount', 'total', 'status', 
        'biteship_order_id', 'tracking_id', 'waybill_id'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'order_coupons')
                    ->withPivot('discount')
                    ->withTimestamps();
    }
}
