<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'name', 'phone', 'address', 'city', 'city_id', 
        'area_id', 'province', 'province_id', 'district', 'village', 
        'postal_code', 'latitude', 'longitude', 'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
