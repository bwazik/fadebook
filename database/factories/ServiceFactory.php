<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $services = ['حلاقة شعر', 'حلاقة لحية', 'صبغة شعر', 'علاج فروة الرأس', 'تصفيف شعر', 'ماسك لحية', 'نظافة الأذن'];

        return [
            'uuid' => (string) Str::uuid(),
            'shop_id' => Shop::factory(),
            'name' => fake()->randomElement($services),
            'description' => fake()->optional()->sentence(),
            'price' => fake()->randomFloat(2, 50, 500),
            'duration_minutes' => fake()->randomElement([15, 30, 45, 60, 90]),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Indicate the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
