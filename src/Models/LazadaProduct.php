<?php

namespace Laraditz\Lazada\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LazadaProduct extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'seller_id',
        'name',
        'brand',
        'model',
        'status',
        'primary_category',
        'description',
        'images',
        'attributes',
    ];

    protected $casts = [
        'images' => 'array',
        'attributes' => 'array',
    ];

    public function skus(): HasMany
    {
        return $this->hasMany(LazadaProductSku::class, 'product_id');
    }
}