<?php

namespace Laraditz\Lazada\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LazadaAccessToken extends Model
{
    use HasFactory;

    protected static function newFactory(): \Laraditz\Lazada\Database\Factories\LazadaAccessTokenFactory
    {
        return \Laraditz\Lazada\Database\Factories\LazadaAccessTokenFactory::new();
    }

    protected $fillable = [
        'subjectable_type', 'subjectable_id', 'access_token', 'refresh_token', 'expires_at', 'refresh_expires_at',
        'user_info', 'country_code', 'account_id', 'account', 'account_platform', 'code'
    ];

    protected $casts = [
        'user_info' => 'json',
        'expires_at' => 'datetime',
        'refresh_expires_at' => 'datetime',
    ];

    public function subjectable()
    {
        return $this->morphTo();
    }
}
