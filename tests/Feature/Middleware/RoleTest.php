<?php

use App\Enums\ShopStatus;
use App\Models\Area;
use App\Models\Shop;
use App\Models\User;

test('unauthenticated users are redirected to login for protected routes', function () {
    $this->get(route('owner.pending'))->assertRedirect(route('login'));
});

test('client cannot access shop owner routes', function () {
    $client = User::factory()->create();

    $this->actingAs($client)
        ->get(route('owner.pending'))
        ->assertRedirect(route('marketplace'))
        ->assertSessionHas('toast', 'مش مسموحلك تدخل هنا');
});

test('owner cannot access admin dashboard', function () {
    $owner = User::factory()->owner()->create();

    Shop::factory()->for($owner, 'owner')->create([
        'area_id' => Area::factory(),
        'status' => ShopStatus::Pending,
    ]);

    $this->actingAs($owner)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('owner.pending'))
        ->assertSessionHas('toast', 'مش مسموحلك تدخل هنا');
});

test('super admin can access admin dashboard', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful();
});
