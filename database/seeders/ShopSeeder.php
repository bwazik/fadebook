<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Barber;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->isLocal()) {
            return;
        }

        $areas = Area::all();

        if ($areas->isEmpty()) {
            return;
        }

        Shop::factory()
            ->count(5)
            ->create([
                'area_id' => fn () => $areas->random()->id,
                'owner_id' => function () {
                    return User::factory()->create([
                        'role' => UserRole::BarberOwner,
                    ])->id;
                },
            ])
            ->each(function (Shop $shop) {
                // Create service categories
                $categories = ServiceCategory::factory()
                    ->count(3)
                    ->create([
                        'shop_id' => $shop->id,
                        'name' => fn () => fake()->randomElement(['حلاقة شعر', 'العناية بالذقن', 'عناية بالبشرة', 'عروض']),
                    ]);

                // Create services for each shop and assign to categories
                $services = Service::factory()
                    ->count(7)
                    ->create([
                        'shop_id' => $shop->id,
                        'service_category_id' => fn () => $categories->random()->id,
                    ]);

                // Create barbers for each shop and attach services
                Barber::factory()
                    ->count(6)
                    ->create([
                        'shop_id' => $shop->id,
                    ])->each(function (Barber $barber) use ($services) {
                        $barber->services()->attach(
                            $services->random(rand(2, 4))->pluck('id')->toArray()
                        );
                    });
            });
    }
}
