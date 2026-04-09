<?php

namespace Database\Factories;

use App\Enums\OtpPurpose;
use App\Models\OtpCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OtpCode>
 */
class OtpCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'phone' => fake()->numerify('01#########'),
            'code' => str_pad((string) fake()->numberBetween(0, 999999), 6, '0', STR_PAD_LEFT),
            'purpose' => OtpPurpose::PasswordReset,
            'attempts' => 0,
            'is_used' => false,
            'expires_at' => now()->addMinutes(10),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subMinute(),
        ]);
    }
}
