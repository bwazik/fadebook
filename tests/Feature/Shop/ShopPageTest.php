<?php

use App\Enums\ShopStatus;
use App\Jobs\IncrementShopView;
use App\Livewire\Shop\ShopPage;
use App\Models\Area;
use App\Models\Service;
use App\Models\Shop;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

it('loads with correct data', function () {
    $area = Area::factory()->create(['slug' => 'maadi']);
    $shop = Shop::factory()->create(['area_id' => $area->id, 'slug' => 'king', 'status' => ShopStatus::Active]);

    Livewire::test(ShopPage::class, ['areaSlug' => $area->slug, 'shopSlug' => $shop->slug])
        ->assertStatus(200)
        ->assertSee($shop->name);
});

it('shows dimmed container for inactive services', function () {
    $area = Area::factory()->create(['slug' => 'maadi']);
    $shop = Shop::factory()->create(['area_id' => $area->id, 'slug' => 'king', 'status' => ShopStatus::Active]);
    $service = Service::factory()->create(['shop_id' => $shop->id, 'is_active' => false]);

    Livewire::test(ShopPage::class, ['areaSlug' => $area->slug, 'shopSlug' => $shop->slug])
        ->assertSee($service->name)
        ->assertSee('opacity-50');
});

it('shows unavailable banner for offline shop', function () {
    $area = Area::factory()->create(['slug' => 'maadi']);
    $shop = Shop::factory()->create(['area_id' => $area->id, 'slug' => 'king', 'status' => ShopStatus::Active, 'is_online' => false]);

    Livewire::test(ShopPage::class, ['areaSlug' => $area->slug, 'shopSlug' => $shop->slug])
        ->assertSee('مغلق');
});

it('increments view count on visit', function () {
    Queue::fake();

    $area = Area::factory()->create(['slug' => 'maadi']);
    $shop = Shop::factory()->create(['area_id' => $area->id, 'slug' => 'king', 'status' => ShopStatus::Active]);

    Livewire::test(ShopPage::class, ['areaSlug' => $area->slug, 'shopSlug' => $shop->slug]);

    Queue::assertPushed(IncrementShopView::class);
});

it('returns 404 if shop slug or area slug is invalid', function () {
    $this->get('/invalid-area/invalid-shop')
        ->assertStatus(404);
});
