<?php

namespace Laraditz\Lazada\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LazadaMessage extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['action', 'url', 'request_id', 'request', 'response', 'error', 'seller_id'];

    protected $casts = [
        'request' => 'json',
        'response' => 'json',
    ];
}
