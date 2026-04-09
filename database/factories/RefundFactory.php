<?php

namespace Database\Factories;

use App\Enums\RefundReason;
use App\Enums\RefundStatus;
use App\Models\Booking;
use App\Models\Refund;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Refund>
 */
class RefundFactory extends Factory
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
            'booking_id' => Booking::factory(),
            'amount' => fake()->randomFloat(2, 50, 500),
            'reason' => RefundReason::ClientCancelEarly,
            'status' => RefundStatus::Pending,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate the refund has been processed.
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RefundStatus::Processed,
            'processed_at' => now(),
        ]);
    }

    /**
     * Indicate the refund has failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RefundStatus::Failed,
            'error_message' => 'Gateway connection failed',
        ]);
    }
}
