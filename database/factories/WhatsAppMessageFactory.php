<?php

namespace Database\Factories;

use App\Enums\WhatsAppQueueType;
use App\Enums\WhatsAppStatus;
use App\Models\User;
use App\Models\WhatsAppMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WhatsAppMessage>
 */
class WhatsAppMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'shop_id' => null,
            'phone' => '010'.fake()->numerify('########'),
            'template' => fake()->randomElement(['otp_code', 'booking_confirmed', 'booking_reminder', 'booking_cancelled']),
            'queue_type' => WhatsAppQueueType::Default,
            'data' => ['otp' => '123456'],
            'status' => WhatsAppStatus::Queued,
            'attempts' => 0,
        ];
    }

    /**
     * Indicate the message has been sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WhatsAppStatus::Sent,
            'sent_at' => now(),
        ]);
    }

    /**
     * Indicate the message has failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WhatsAppStatus::Failed,
            'attempts' => 3,
            'error_message' => 'API request failed after 3 retries',
        ]);
    }

    /**
     * Indicate the message is instant/urgent priority.
     */
    public function instant(): static
    {
        return $this->state(fn (array $attributes) => [
            'queue_type' => WhatsAppQueueType::Instant,
        ]);
    }
}
