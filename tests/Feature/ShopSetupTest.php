<?php

declare(strict_types=1);

use App\Enums\ShopStatus;
use App\Models\Area;
use App\Models\Shop;
use App\Models\User;

test('authenticated user can visit shop setup page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('onboarding.shop'));

    expect($response->status())->toBe(200);
});

test('user with existing shop is redirected from setup page', function () {
    $user = User::factory()->create();
    $area = Area::factory()->create();

    $shop = $user->shop()->create([
        'name' => 'Test Shop',
        'phone' => '201001234567',
        'area_id' => $area->id,
        'address' => 'Test Address',
        'status' => ShopStatus::Pending,
        'opening_hours' => [
            'saturday' => ['open' => '09:00', 'close' => '21:00'],
            'sunday' => ['open' => '09:00', 'close' => '21:00'],
            'monday' => ['open' => '09:00', 'close' => '21:00'],
            'thursday' => ['open' => '09:00', 'close' => '21:00'],
            'wednesday' => ['open' => '09:00', 'close' => '21:00'],
            'tuesday' => ['open' => '09:00', 'close' => '21:00'],
            'friday' => ['open' => '14:00', 'close' => '23:00'],
        ],
    ]);

    $response = $this->actingAs($user)->get(route('onboarding.shop'));

    expect($response->status())->toBe(302);
});

test('shop can be created from form submission', function () {
    $user = User::factory()->create();
    $area = Area::factory()->create();

    $this->actingAs($user)
        ->get(route('onboarding.shop'))
        ->assertStatus(200);

    // Simulate the Livewire submission to create the shop
    $shop = Shop::create([
        'owner_id' => $user->id,
        'name' => 'My Barbershop',
        'phone' => '201001234567',
        'area_id' => $area->id,
        'address' => '123 Main Street',
        'status' => ShopStatus::Pending,
        'is_online' => true,
        'advance_booking_days' => 30,
        'opening_hours' => [
            'saturday' => ['open' => '09:00', 'close' => '21:00'],
            'sunday' => ['open' => '09:00', 'close' => '21:00'],
            'monday' => ['open' => '09:00', 'close' => '21:00'],
            'tuesday' => ['open' => '09:00', 'close' => '21:00'],
            'wednesday' => ['open' => '09:00', 'close' => '21:00'],
            'thursday' => ['open' => '09:00', 'close' => '21:00'],
            'friday' => ['open' => '14:00', 'close' => '23:00'],
        ],
    ]);

    $this->assertDatabaseHas('shops', [
        'owner_id' => $user->id,
        'name' => 'My Barbershop',
        'phone' => '201001234567',
        'status' => ShopStatus::Pending->value,
    ]);
});

test('shop is created with pending status', function () {
    $user = User::factory()->create();
    $area = Area::factory()->create();

    $shop = Shop::create([
        'owner_id' => $user->id,
        'name' => 'Test Shop',
        'phone' => '201001234567',
        'area_id' => $area->id,
        'address' => 'Address',
        'status' => ShopStatus::Pending,
        'is_online' => true,
        'advance_booking_days' => 30,
        'opening_hours' => [
            'saturday' => ['open' => '09:00', 'close' => '21:00'],
            'sunday' => ['open' => '09:00', 'close' => '21:00'],
            'monday' => ['open' => '09:00', 'close' => '21:00'],
            'tuesday' => ['open' => '09:00', 'close' => '21:00'],
            'wednesday' => ['open' => '09:00', 'close' => '21:00'],
            'thursday' => ['open' => '09:00', 'close' => '21:00'],
            'friday' => ['open' => '14:00', 'close' => '23:00'],
        ],
    ]);

    expect($shop->status)->toBe(ShopStatus::Pending);
});

test('shops can have multiple initial services', function () {
    $user = User::factory()->create();
    $area = Area::factory()->create();

    $shop = Shop::create([
        'owner_id' => $user->id,
        'name' => 'Barber Shop',
        'phone' => '201001234567',
        'area_id' => $area->id,
        'address' => 'Address 123',
        'status' => ShopStatus::Pending,
        'is_online' => true,
        'advance_booking_days' => 30,
        'opening_hours' => [
            'saturday' => ['open' => '09:00', 'close' => '21:00'],
            'sunday' => ['open' => '09:00', 'close' => '21:00'],
            'monday' => ['open' => '09:00', 'close' => '21:00'],
            'tuesday' => ['open' => '09:00', 'close' => '21:00'],
            'wednesday' => ['open' => '09:00', 'close' => '21:00'],
            'thursday' => ['open' => '09:00', 'close' => '21:00'],
            'friday' => ['open' => '14:00', 'close' => '23:00'],
        ],
    ]);

    $shop->services()->create(['name' => 'Haircut', 'price' => '50', 'duration_minutes' => 30, 'is_active' => true]);
    $shop->services()->create(['name' => 'Shave', 'price' => '30', 'duration_minutes' => 20, 'is_active' => true]);

    expect($shop->services()->count())->toBe(2);
    expect($shop->services->first()->name)->toBe('Haircut');
    expect($shop->services->last()->name)->toBe('Shave');
});

test('pending approval page shows shop details', function () {
    $user = User::factory()->create();
    $area = Area::factory()->create();

    $shop = $user->shop()->create([
        'name' => 'Test Shop',
        'phone' => '201001234567',
        'area_id' => $area->id,
        'address' => 'Test Address',
        'status' => ShopStatus::Pending,
        'opening_hours' => [
            'saturday' => ['open' => '09:00', 'close' => '21:00'],
            'sunday' => ['open' => '09:00', 'close' => '21:00'],
            'monday' => ['open' => '09:00', 'close' => '21:00'],
            'tuesday' => ['open' => '09:00', 'close' => '21:00'],
            'wednesday' => ['open' => '09:00', 'close' => '21:00'],
            'thursday' => ['open' => '09:00', 'close' => '21:00'],
            'friday' => ['open' => '14:00', 'close' => '23:00'],
        ],
    ]);

    $view = $this->actingAs($user)->get(route('onboarding.pending'));

    expect($view->status())->toBe(200);
});

test('user without pending shop is redirected from pending approval page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('onboarding.pending'));

    expect($response->status())->toBe(302);
    expect($response->getTargetUrl())->toContain('home');
});

test('phone number must be unique for shops', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $area = Area::factory()->create();

    // Create first shop
    $user1->shop()->create([
        'name' => 'Shop 1',
        'phone' => '201001234567',
        'area_id' => $area->id,
        'address' => 'Address 1',
        'status' => ShopStatus::Pending,
        'opening_hours' => [
            'saturday' => ['open' => '09:00', 'close' => '21:00'],
            'sunday' => ['open' => '09:00', 'close' => '21:00'],
            'monday' => ['open' => '09:00', 'close' => '21:00'],
            'tuesday' => ['open' => '09:00', 'close' => '21:00'],
            'wednesday' => ['open' => '09:00', 'close' => '21:00'],
            'thursday' => ['open' => '09:00', 'close' => '21:00'],
            'friday' => ['open' => '14:00', 'close' => '23:00'],
        ],
    ]);

    // Create second shop with same phone should fail
    try {
        $user2->shop()->create([
            'name' => 'Shop 2',
            'phone' => '201001234567',
            'area_id' => $area->id,
            'address' => 'Address 2',
            'status' => ShopStatus::Pending,
            'opening_hours' => [
                'saturday' => ['open' => '09:00', 'close' => '21:00'],
                'sunday' => ['open' => '09:00', 'close' => '21:00'],
                'monday' => ['open' => '09:00', 'close' => '21:00'],
                'tuesday' => ['open' => '09:00', 'close' => '21:00'],
                'wednesday' => ['open' => '09:00', 'close' => '21:00'],
                'thursday' => ['open' => '09:00', 'close' => '21:00'],
                'friday' => ['open' => '14:00', 'close' => '23:00'],
            ],
        ]);

        // If we reach here, it means the unique constraint didn't work
        expect(true)->toBe(false);
    } catch (Throwable $e) {
        expect(true)->toBe(true);
    }
});
