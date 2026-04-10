<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Livewire\Livewire;

it('renders the register page', function () {
    $this->get('/register')->assertStatus(200);
});

it('can register as client with valid data', function () {
    Livewire::test(Register::class)
        ->set('name', 'Ahmed Ali')
        ->set('phone', '01012345678')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('nextStep')
        ->assertSet('step', 2)
        ->set('role', 'client')
        ->call('register')
        ->assertRedirect(route('phone.verification.show'));

    $this->assertDatabaseHas('users', [
        'phone' => '01012345678',
        'name' => 'Ahmed Ali',
    ]);

    $this->assertAuthenticated();
});

it('cannot register with duplicate phone', function () {
    User::factory()->create([
        'phone' => '01012345678',
    ]);

    Livewire::test(Register::class)
        ->set('name', 'Omar Ali')
        ->set('phone', '01012345678')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('nextStep')
        ->assertHasErrors(['phone' => 'unique']);
});

it('password confirmation must match', function () {
    Livewire::test(Register::class)
        ->set('name', 'Omar Ali')
        ->set('phone', '01012345679')
        ->set('password', 'password123')
        ->set('password_confirmation', 'different_password')
        ->call('nextStep')
        ->assertHasErrors(['password' => 'confirmed']);
});
