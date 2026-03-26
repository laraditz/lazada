<?php

namespace Laraditz\Lazada\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Laraditz\Lazada\Models\LazadaSeller;

class LazadaSellerFactory extends Factory
{
    protected $model = LazadaSeller::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->randomNumber(6, true),
            'name' => $this->faker->company(),
            'short_code' => strtoupper($this->faker->unique()->lexify('SHOP????')),
            'email' => $this->faker->safeEmail(),
        ];
    }
}
