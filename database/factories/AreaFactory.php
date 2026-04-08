<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Area>
 */
class AreaFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'مدينة نصر',
            'المعادي',
            'مصر الجديدة',
            'الشيخ زايد',
            'الدقي',
            'المهندسين',
        ]).' '.fake()->unique()->numberBetween(1, 99);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(100, 999),
            'is_active' => true,
        ];
    }
}
