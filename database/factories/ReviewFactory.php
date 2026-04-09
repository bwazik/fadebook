<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
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
            'user_id' => User::factory(),
            'booking_id' => Booking::factory()->completed(),
            'rating' => fake()->randomFloat(2, 1, 5),
            'comment' => fake()->optional()->paragraph(),
            'is_flagged' => false,
        ];
    }

    /**
     * Indicate the review has been flagged.
     */
    public function flagged(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_flagged' => true,
            'flag_reason' => fake()->sentence(),
        ]);
    }
}
