<?php

namespace Database\Factories;

use App\Models\FavoriteProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteProductFactory extends Factory
{
    protected $model = FavoriteProduct::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'product_id' => $this->faker->numberBetween(1, 1000),
            'title' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'image' => $this->faker->imageUrl(),
            'review' => $this->faker->randomFloat(1, 1, 5),
        ];
    }
}
