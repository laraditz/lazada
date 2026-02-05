<?php

namespace Laraditz\Lazada\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LazadaReverseOrder extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['id', 'order_id', 'seller_id', 'buyer_id', 'status'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(LazadaOrder::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(LazadaSeller::class);
    }
}
