<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        return [
            'content' => $this->faker->sentence(),
            'score' => $this->faker->numberBetween(1, 5),
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
        ];
    }
}
