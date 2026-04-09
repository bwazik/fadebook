<?php

use App\Enums\ShopStatus;
use App\Livewire\Auth\Login;
use App\Models\Area;
use App\Models\Shop;
use App\Models\User;
use Livewire\Livewire;

test('registered client can login and is redirected to marketplace', function () {
    User::factory()->create([
        'phone' => '01012345678',
        'password' => 'password',
    ]);

    Livewire::test(Login::class)
        ->set('phone', '01012345678')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('marketplace'));

    $this->assertAuthenticated();
});

test('owner login redirects to pending screen after onboarding submission', function () {
    $owner = User::factory()->owner()->create([
        'phone' => '01012345678',
        'password' => 'password',
    ]);

    Shop::factory()->for($owner, 'owner')->create([
        'area_id' => Area::factory(),
        'status' => ShopStatus::Pending,
    ]);

    Livewire::test(Login::class)
        ->set('phone', '01012345678')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('owner.pending'));
});

test('wrong password shows a generic error', function () {
    User::factory()->create([
        'phone' => '01012345678',
        'password' => 'password',
    ]);

    Livewire::test(Login::class)
        ->set('phone', '01012345678')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertSee('بيانات الدخول غلط');

    $this->assertGuest();
});

test('unknown phone shows the same generic error', function () {
    Livewire::test(Login::class)
        ->set('phone', '01012345678')
        ->set('password', 'password')
        ->call('login')
        ->assertSee('بيانات الدخول غلط');
});

test('authenticated users are redirected away from login page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('login'));

    $response->assertRedirect(route('marketplace'));
});

test('login attempts are rate limited after five failures', function () {
    User::factory()->create([
        'phone' => '01012345678',
        'password' => 'password',
    ]);

    $component = Livewire::test(Login::class)
        ->set('phone', '01012345678')
        ->set('password', 'wrong-password');

    foreach (range(1, 5) as $attempt) {
        $component->call('login');
    }

    $component->call('login')->assertSee('جرب تاني بعد دقيقة.');
});
