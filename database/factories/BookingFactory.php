<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Enums\CancelledBy;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 50, 500);

        return [
            'uuid' => (string) Str::uuid(),
            'booking_code' => strtoupper(Str::random(6)),
            'shop_id' => Shop::factory(),
            'client_id' => User::factory(),
            'barber_id' => null,
            'service_id' => Service::factory(),
            'coupon_id' => null,
            'scheduled_at' => fake()->dateTimeBetween('now', '+7 days'),
            'status' => BookingStatus::Pending,
            'service_price' => $price,
            'discount_amount' => 0,
            'paid_amount' => 0,
            'final_amount' => $price,
            'notes' => fake()->optional()->sentence(),
            'policy_accepted' => true,
        ];
    }

    /**
     * Indicate the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Confirmed,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Indicate the booking is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Completed,
            'confirmed_at' => now()->subHours(3),
            'arrived_at' => now()->subHours(2),
            'completed_at' => now()->subHour(),
            'scheduled_at' => now()->subHours(2),
        ]);
    }

    /**
     * Indicate the booking is cancelled by the client.
     */
    public function cancelledByClient(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Cancelled,
            'cancelled_at' => now(),
            'cancelled_by' => CancelledBy::Client,
        ]);
    }

    /**
     * Indicate the booking is a no-show.
     */
    public function noShow(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::NoShow,
        ]);
    }
}
