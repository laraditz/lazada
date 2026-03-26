<?php

namespace Laraditz\Lazada\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LazadaFinanceDetail extends Model
{
    protected $fillable = ['order_id', 'data'];

    protected function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(LazadaOrder::class);
    }
}
