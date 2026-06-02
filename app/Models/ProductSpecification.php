<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['product_id', 'spec_name', 'spec_value'])]
class ProductSpecification extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
