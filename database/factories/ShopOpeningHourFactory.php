<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\ShopOpeningHour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShopOpeningHour>
 */
class ShopOpeningHourFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'day_of_week' => fake()->numberBetween(0, 6),
            'is_closed' => false,
            'open_time' => '10:00:00',
            'close_time' => '22:00:00',
        ];
    }
}
