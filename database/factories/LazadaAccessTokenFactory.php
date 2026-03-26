<?php

namespace Laraditz\Lazada\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laraditz\Lazada\Models\LazadaAccessToken;
use Laraditz\Lazada\Models\LazadaSeller;

class LazadaAccessTokenFactory extends Factory
{
    protected $model = LazadaAccessToken::class;

    public function definition(): array
    {
        return [
            'subjectable_type' => LazadaSeller::class,
            'subjectable_id'   => null,
            'access_token'     => $this->faker->sha256(),
            'refresh_token'    => $this->faker->sha256(),
            'expires_at'       => now()->addDays(30),
            'refresh_expires_at' => now()->addDays(180),
        ];
    }
}
