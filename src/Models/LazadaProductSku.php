<?php

namespace Laraditz\Lazada\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LazadaProductSku extends Model
{
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'product_id',
        'seller_sku',
        'shop_sku',
        'status',
        'price',
        'quantity',
        'available',
        'variation',
        'color_family',
        'images',
        'multi_warehouse_inventories',
        'sale_prop',
        'package_width',
        'package_height',
        'package_length',
        'package_weight',
    ];

    protected $casts = [
        'images' => 'array',
        'multi_warehouse_inventories' => 'array',
        'sale_prop' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(LazadaProduct::class, 'product_id');
    }
}