<?php

namespace Database\Factories;

use App\Enums\OtpType;
use App\Models\PhoneVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<PhoneVerification>
 */
class PhoneVerificationFactory extends Factory
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
            'phone' => '010'.fake()->numerify('########'),
            'otp_code' => Hash::make('123456'),
            'type' => OtpType::BookingConfirmation,
            'expires_at' => now()->addMinutes(5),
            'verified_at' => null,
            'attempts' => 0,
            'is_used' => false,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    /**
     * Indicate the OTP is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subMinutes(10),
        ]);
    }

    /**
     * Indicate the OTP has been used.
     */
    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_used' => true,
            'verified_at' => now(),
        ]);
    }
}
