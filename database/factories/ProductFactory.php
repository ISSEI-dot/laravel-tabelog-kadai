<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->realText(),
            'price' => fake()->numberBetween(580, 4980),
            'category_id' => fake()->numberBetween(1, 15),
            'postal_code' => $this->faker->postcode, // 新しいカラム
            'address' => $this->faker->address, // 新しいカラム
            'phone_number' => $this->faker->phoneNumber,
        ];
    }

}
