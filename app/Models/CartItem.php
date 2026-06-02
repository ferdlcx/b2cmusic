<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['cart_id', 'product_id', 'price', 'quantity'])]
class CartItem extends Model
{
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper to calculate subtotal
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
