<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'slug', 'logo', 'description', 'status'])]
class Brand extends Model
{
    protected $casts = [
        'status' => 'boolean',
    ];

    
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
