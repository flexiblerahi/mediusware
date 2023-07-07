<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    public function variantOne()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_one', 'id');
    }

    public function variantTwo()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_two', 'id');
    }

    public function variantThree()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_three', 'id');
    }
}
