<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Area;
use App\Models\Barber;
use App\Models\Service;
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
                // Create barbers for each shop
                Barber::factory()
                    ->count(3)
                    ->create([
                        'shop_id' => $shop->id,
                    ]);

                // Create services for each shop
                Service::factory()
                    ->count(4)
                    ->create([
                        'shop_id' => $shop->id,
                    ]);
            });
    }
}
