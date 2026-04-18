<?php

namespace Database\Factories;

use App\Enums\PaymentMethodType;
use App\Models\Shop;
use App\Models\ShopPaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShopPaymentMethod>
 */
class ShopPaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'type' => fake()->randomElement(PaymentMethodType::cases()),
            'account_name' => fake()->name(),
            'phone_number' => '010'.fake()->numerify('########'),
            'pay_link' => fake()->optional()->url(),
            'is_active' => true,
        ];
    }
}
