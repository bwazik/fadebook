<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ServiceCategory>
 */
class ServiceCategoryFactory extends Factory
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
            'name' => fake()->randomElement(['حلاقة شعر', 'العناية بالذقن', 'عناية بالبشرة', 'عروض', 'أخرى']),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
