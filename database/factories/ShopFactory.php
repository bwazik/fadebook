<?php

namespace Database\Factories;

use App\Enums\ShopStatus;
use App\Models\Area;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory()->owner(),
            'area_id' => Area::factory(),
            'name' => 'صالون '.fake()->unique()->firstName(),
            'address' => fake()->streetAddress(),
            'phone' => fake()->unique()->numerify('01#########'),
            'logo_path' => null,
            'status' => ShopStatus::Pending,
            'rejection_reason' => null,
            'basic_services' => ['حلاقة', 'دقن'],
            'barbers_count' => fake()->numberBetween(1, 8),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShopStatus::Active,
        ]);
    }

    public function rejected(string $reason = 'البيانات ناقصة'): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShopStatus::Rejected,
            'rejection_reason' => $reason,
        ]);
    }
}
