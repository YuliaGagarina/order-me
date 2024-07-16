<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'vendor_id',
        'quantity',
        'image_url',
        'upc',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductVariationAttribute::class, 'variation_id');
    }
}
