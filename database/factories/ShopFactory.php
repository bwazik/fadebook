<?php

namespace Database\Factories;

use App\Enums\BarberSelectionMode;
use App\Enums\PaymentMode;
use App\Enums\ShopStatus;
use App\Models\Area;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company().' Barbershop';

        return [
            'uuid' => (string) Str::uuid(),
            'owner_id' => User::factory()->shopOwner(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'description' => fake()->paragraph(),
            'phone' => '010'.fake()->numerify('########'),
            'address' => fake()->address(),
            'area_id' => Area::factory(),
            'opening_hours' => [
                'monday' => ['open' => '09:00', 'close' => '21:00'],
                'tuesday' => ['open' => '09:00', 'close' => '21:00'],
                'wednesday' => ['open' => '09:00', 'close' => '21:00'],
                'thursday' => ['open' => '09:00', 'close' => '21:00'],
                'friday' => ['open' => '14:00', 'close' => '23:00'],
                'saturday' => ['open' => '09:00', 'close' => '23:00'],
                'sunday' => null,
            ],
            'average_rating' => fake()->randomFloat(2, 3, 5),
            'total_reviews' => fake()->numberBetween(0, 500),
            'total_views' => fake()->numberBetween(0, 5000),
            'total_bookings' => fake()->numberBetween(0, 1000),
            'status' => ShopStatus::Active,
            'is_online' => true,
            'advance_booking_days' => 7,
            'barber_selection_mode' => BarberSelectionMode::ClientPicks,
            'payment_mode' => PaymentMode::NoPayment,
            'commission_rate' => 10.00,
        ];
    }

    /**
     * Indicate the shop is pending review.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShopStatus::Pending,
        ]);
    }

    /**
     * Indicate the shop is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ShopStatus::Suspended,
        ]);
    }

    /**
     * Indicate the shop is offline.
     */
    public function offline(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_online' => false,
        ]);
    }
}
