<?php

namespace Laraditz\Lazada\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class LazadaMessage extends Model
{
    use HasFactory, HasUlids, MassPrunable;

    protected $fillable = ['action', 'url', 'request_id', 'request', 'response', 'error', 'seller_id'];

    protected $casts = [
        'request' => 'json',
        'response' => 'json',
    ];

    public function prunable(): Builder
    {
        $days = config('lazada.logging.prune_after_days');

        if (is_numeric($days) && (int) $days > 0) {
            return static::where('created_at', '<=', now()->subDays((int) $days));
        }

        return static::query()->whereRaw('1 = 0');
    }
}
