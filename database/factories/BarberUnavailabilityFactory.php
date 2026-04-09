<?php

namespace Database\Factories;

use App\Models\Barber;
use App\Models\BarberUnavailability;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BarberUnavailability>
 */
class BarberUnavailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'barber_id' => Barber::factory(),
            'unavailable_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
        ];
    }
}
