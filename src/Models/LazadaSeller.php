<?php

namespace Laraditz\Lazada\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laraditz\Lazada\Enums\ActiveStatus;
use Laraditz\Lazada\Enums\Affirmative;

class LazadaSeller extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected static function newFactory(): \Laraditz\Lazada\Database\Factories\LazadaSellerFactory
    {
        return \Laraditz\Lazada\Database\Factories\LazadaSellerFactory::new();
    }

    protected $fillable = [
        'id', 'user_id', 'name', 'email', 'logo_url', 'company_name', 'short_code',
        'location', 'country_code', 'verified', 'status', 'cross_border'
    ];

    protected $casts = [
        'verified' => Affirmative::class,
        'cross_border' => Affirmative::class,
        'status' => ActiveStatus::class,
    ];

    public function accessToken()
    {
        return $this->morphOne(LazadaAccessToken::class, 'subjectable');
    }
}
