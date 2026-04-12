<?php

use App\Enums\ShopStatus;
use App\Jobs\TrackShopView;
use App\Livewire\Shop\ShopPage;
use App\Models\Area;
use App\Models\Service;
use App\Models\Shop;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

it('renders the shop page successfully', function () {
    $area = Area::factory()->create(['slug' => 'nasr-city']);
    $shop = Shop::factory()->create([
        'area_id' => $area->id,
        'slug' => 'king',
        'status' => ShopStatus::Active,
    ]);

    // Create some services for the shop
    Service::factory()->count(3)->create(['shop_id' => $shop->id]);

    Livewire::test(ShopPage::class, [
        'areaSlug' => $area->slug,
        'shopSlug' => $shop->slug,
    ])
        ->assertStatus(200)
        ->assertSee($shop->name);
});

it('dispatches the track view job on mount', function () {
    Queue::fake();

    $area = Area::factory()->create(['slug' => 'nasr-city']);
    $shop = Shop::factory()->create([
        'area_id' => $area->id,
        'slug' => 'king',
        'status' => ShopStatus::Active,
    ]);

    Livewire::test(ShopPage::class, [
        'areaSlug' => $area->slug,
        'shopSlug' => $shop->slug,
    ]);

    Queue::assertPushed(TrackShopView::class);
});

it('returns 404 if shop slug or area slug is invalid', function () {
    $area = Area::factory()->create(['slug' => 'nasr-city']);
    $shop = Shop::factory()->create([
        'area_id' => $area->id,
        'slug' => 'king',
        'status' => ShopStatus::Active,
    ]);

    Livewire::test(ShopPage::class, [
        'areaSlug' => $area->slug,
        'shopSlug' => 'invalid-shop',
    ])->assertStatus(404);

    Livewire::test(ShopPage::class, [
        'areaSlug' => 'invalid-area',
        'shopSlug' => $shop->slug,
    ])->assertStatus(404);
});
