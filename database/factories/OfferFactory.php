<?php

namespace Database\Factories;

use App\Enums\OfferType;
use App\Models\Offer;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Offer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'type' => OfferType::Discount,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'is_active' => true,
        ];
    }
}
