<?php

use App\Enums\ShopStatus;
use App\Livewire\Search;
use App\Models\Area;
use App\Models\Shop;
use Livewire\Livewire;

it('returns shops matching query', function () {
    $shop = Shop::factory()->create(['name' => 'Barber King', 'status' => ShopStatus::Active]);

    Livewire::test(Search::class)
        ->set('query', 'King')
        ->assertSee('Barber King');
});

it('search by area name works', function () {
    $area = Area::factory()->create(['name' => 'Maadi']);
    $shop = Shop::factory()->create(['area_id' => $area->id, 'status' => ShopStatus::Active]);

    Livewire::test(Search::class)
        ->set('query', 'Maadi')
        ->assertSee($shop->name);
});

it('empty query returns no results', function () {
    Shop::factory()->create(['name' => 'Barber King', 'status' => ShopStatus::Active]);

    Livewire::test(Search::class)
        ->set('query', '')
        ->assertDontSee('Barber King');
});
