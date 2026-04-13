<?php

namespace Database\Factories;

use App\Models\Barber;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Barber>
 */
class BarberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'shop_id' => Shop::factory(),
            'user_id' => null,
            'name' => fake()->name('male'),
            'phone' => fake()->optional()->numerify('010########'),
            'average_rating' => fake()->randomFloat(2, 3, 5),
            'total_reviews' => fake()->numberBetween(0, 200),
            'is_active' => true,
        ];
    }

    /**
     * Indicate the barber is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
