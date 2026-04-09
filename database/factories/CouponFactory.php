<?php

namespace Database\Factories;

use App\Enums\DiscountType;
use App\Models\Coupon;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
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
            'code' => strtoupper(Str::random(8)),
            'discount_type' => DiscountType::Percentage,
            'discount_value' => fake()->randomElement([5, 10, 15, 20, 25]),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'is_active' => true,
            'usage_limit' => fake()->optional()->numberBetween(10, 100),
            'used_count' => 0,
            'usage_limit_per_user' => fake()->optional()->numberBetween(1, 3),
            'minimum_amount' => fake()->optional()->randomFloat(2, 50, 200),
        ];
    }

    /**
     * Indicate the coupon gives a fixed discount.
     */
    public function fixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_type' => DiscountType::Fixed,
            'discount_value' => fake()->randomElement([20, 30, 50, 100]),
        ]);
    }

    /**
     * Indicate the coupon is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'end_date' => now()->subDay(),
        ]);
    }

    /**
     * Indicate the coupon is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
