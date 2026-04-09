<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => 'images/'.fake()->uuid().'.jpg',
            'disk' => 'public',
            'collection' => fake()->randomElement(['logo', 'banner', 'gallery', 'photo']),
            'sort_order' => fake()->numberBetween(0, 10),
            'meta' => null,
        ];
    }
}
