<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariantPrice::class, 'product_id', 'id');
    }

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }
}
