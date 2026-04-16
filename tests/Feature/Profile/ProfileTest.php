<?php

declare(strict_types=1);

use App\Enums\BookingStatus;
use App\Enums\ReferralStatus;
use App\Enums\UserRole;
use App\Livewire\Profile\EditProfile;
use App\Livewire\Profile\Index;
use App\Models\Booking;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders the profile index page', function () {
    $user = User::factory()->create(['role' => UserRole::Client]);

    actingAs($user)
        ->get(route('profile.index'))
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee($user->phone);
});

it('shows the correct user stats on profile index', function () {
    $user = User::factory()->create(['role' => UserRole::Client]);

    // Create bookings with different statuses
    Booking::factory()->count(3)->create([
        'client_id' => $user->id,
        'status' => BookingStatus::Completed,
    ]);

    Booking::factory()->count(2)->create([
        'client_id' => $user->id,
        'status' => BookingStatus::Pending,
    ]);

    // Create successful referrals (rewarded)
    Referral::factory()->count(4)->create([
        'referrer_id' => $user->id,
        'status' => ReferralStatus::Rewarded,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSet('stats.total_bookings', 5)
        ->assertSet('stats.completed_bookings', 3)
        ->assertSet('stats.successful_invites', 4);
});

it('renders the edit profile page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee(__('messages.profile_edit_info'));
});

it('updates user profile information', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('name', 'New Name')
        ->set('email', 'new@example.com')
        ->call('updateProfile')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->name)->toBe('New Name')
        ->and($user->email)->toBe('new@example.com');
});

it('updates user password with valid current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('current_password', 'old-password')
        ->set('new_password', 'new-password')
        ->set('new_password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasNoErrors();

    $user->refresh();
    expect(Hash::check('new-password', $user->password))->toBeTrue();
});

it('fails to update password with incorrect current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->set('current_password', 'wrong-password')
        ->set('new_password', 'new-password')
        ->set('new_password_confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);

    $user->refresh();
    expect(Hash::check('old-password', $user->password))->toBeTrue();
});
