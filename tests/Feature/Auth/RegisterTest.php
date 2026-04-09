<?php

use App\Enums\UserRole;
use App\Livewire\Auth\Register;
use App\Models\User;
use Livewire\Livewire;

test('client can register and gets redirected to marketplace', function () {
    Livewire::test(Register::class)
        ->set('name', 'أحمد')
        ->set('phone', '+201012345678')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('role', UserRole::Client->value)
        ->call('register')
        ->assertRedirect(route('marketplace'));

    $user = User::query()->first();

    expect($user)->not()->toBeNull();
    expect($user->phone)->toBe('01012345678');
    expect($user->role)->toBe(UserRole::Client);

    $this->assertAuthenticatedAs($user);
});

test('shop owner registration redirects to onboarding', function () {
    Livewire::test(Register::class)
        ->set('name', 'محمود')
        ->set('phone', '01012345678')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('role', UserRole::BarberOwner->value)
        ->call('register')
        ->assertRedirect(route('onboarding'));

    expect(User::query()->first()->role)->toBe(UserRole::BarberOwner);
});

test('duplicate phone shows a clear arabic error', function () {
    User::factory()->create(['phone' => '01012345678']);

    Livewire::test(Register::class)
        ->set('name', 'أحمد')
        ->set('phone', '00201012345678')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('role', UserRole::Client->value)
        ->call('register')
        ->assertSee('رقم الموبايل ده مسجل قبل كده.');

    expect(User::query()->count())->toBe(1);
});

test('password must be at least eight characters', function () {
    Livewire::test(Register::class)
        ->set('name', 'أحمد')
        ->set('phone', '01012345678')
        ->set('password', '1234567')
        ->set('password_confirmation', '1234567')
        ->set('role', UserRole::Client->value)
        ->call('register')
        ->assertHasErrors(['password' => ['min']]);
});

test('phone must be a valid egyptian number', function () {
    Livewire::test(Register::class)
        ->set('name', 'أحمد')
        ->set('phone', '12345')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('role', UserRole::Client->value)
        ->call('register')
        ->assertHasErrors(['phone']);
});
