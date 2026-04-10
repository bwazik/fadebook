<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Livewire\Livewire;

it('renders the login page', function () {
    $this->get('/login')->assertStatus(200);
});

it('can login with valid phone and password', function () {
    $user = User::factory()->create([
        'phone' => '01012345678',
        'password' => bcrypt('password'),
    ]);

    Livewire::test(Login::class)
        ->set('phone', '01012345678')
        ->set('password', 'password')
        ->call('authenticate')
        ->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($user);
});

it('cannot login with wrong password', function () {
    $user = User::factory()->create([
        'phone' => '01012345678',
        'password' => bcrypt('password'),
    ]);

    Livewire::test(Login::class)
        ->set('phone', '01012345678')
        ->set('password', 'wrongpassword')
        ->call('authenticate')
        ->assertDispatched('toast');

    $this->assertGuest();
});

it('cannot login if account is blocked', function () {
    $user = User::factory()->create([
        'phone' => '01012345678',
        'password' => bcrypt('password'),
        'is_blocked' => true,
    ]);

    Livewire::test(Login::class)
        ->set('phone', '01012345678')
        ->set('password', 'password')
        ->call('authenticate')
        ->assertDispatched('toast');

    $this->assertGuest();
});
