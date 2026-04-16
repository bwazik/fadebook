<?php

declare(strict_types=1);

use App\Livewire\Profile\AppSettings;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders the app settings page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('profile.settings'))
        ->assertOk()
        ->assertSee(__('messages.profile_app_settings'));
});

it('hides bottom navigation on app settings mount', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(AppSettings::class)
        ->assertDispatched('hide-bottom-nav');
});
