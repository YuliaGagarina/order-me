<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'vendor_id',
        'title',
        'description',
        'image_url',
        'upc',
        'quantity',
        'quantity_updated_at',
        'fmv',
        'nominated',
        'nominated_date_start',
        'nominated_date_end',
    ];

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id', 'product_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }
}
