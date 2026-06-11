<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class Shipment extends Model
{
    protected $fillable = [
        'order_id', 'courier', 'service', 'tracking_number', 
        'biteship_order_id', 'biteship_waybill_id', 'shipping_cost', 
        'status', 'shipped_at', 'delivered_at'
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
