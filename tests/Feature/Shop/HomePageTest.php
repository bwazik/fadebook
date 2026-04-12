<?php

use App\Enums\ShopStatus;
use App\Livewire\Home;
use App\Models\Area;
use App\Models\Shop;
use Livewire\Livewire;

it('loads the homepage and shows active shops', function () {
    $shop = Shop::factory()->create(['status' => ShopStatus::Active]);

    Livewire::test(Home::class)
        ->assertStatus(200)
        ->assertSee($shop->name);
});

it('shows offline shops with a badge but they appear in the list', function () {
    $shop = Shop::factory()->create(['status' => ShopStatus::Active, 'is_online' => false]);

    Livewire::test(Home::class)
        ->assertStatus(200)
        ->assertSee($shop->name)
        ->assertSee('مش متاح دلوقتي');
});

it('can filter shops by area', function () {
    $area1 = Area::factory()->create();
    $area2 = Area::factory()->create();

    $shop1 = Shop::factory()->create(['status' => ShopStatus::Active, 'area_id' => $area1->id]);
    $shop2 = Shop::factory()->create(['status' => ShopStatus::Active, 'area_id' => $area2->id]);

    Livewire::test(Home::class)
        ->call('filterByArea', $area1->id)
        ->assertSee($shop1->name)
        ->assertDontSee($shop2->name);
});

it('allows unauthenticated users to browse', function () {
    $this->get('/')
        ->assertStatus(200);
});
