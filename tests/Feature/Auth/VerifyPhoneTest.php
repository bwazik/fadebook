<?php

use App\Enums\OtpType;
use App\Models\User;
use App\Services\OtpService;
use Livewire\Livewire;
use App\Livewire\Auth\VerifyPhone;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['phone_verified_at' => null]);
    $this->otpService = app(OtpService::class);
});

it('redirects unverified user to verification page', function () {
    $this->actingAs($this->user)
        ->get(route('home'))
        ->assertRedirect(route('phone.verification.show'));
});

it('redirects verified user to home if accessing verification page', function () {
    $this->user->update(['phone_verified_at' => now()]);

    $this->actingAs($this->user)
        ->get(route('phone.verification.show'))
        ->assertRedirect(route('home'));
});

it('can verify a valid otp', function () {
    $otpCode = '123456';
    
    // Create a verification record reaching the model directly
    \App\Models\PhoneVerification::create([
        'user_id' => $this->user->id,
        'phone' => $this->user->phone,
        'otp_code' => Hash::make($otpCode),
        'type' => OtpType::PhoneVerification,
        'expires_at' => now()->addMinutes(5),
    ]);

    Livewire::actingAs($this->user)
        ->test(VerifyPhone::class)
        ->set('otp', $otpCode)
        ->call('verify')
        ->assertRedirect(route('home'));

    $this->user->refresh();
    expect($this->user->phone_verified_at)->not->toBeNull();
});

it('shows toast error for invalid otp', function () {
    Livewire::actingAs($this->user)
        ->test(VerifyPhone::class)
        ->set('otp', '000000') // Wrong code
        ->call('verify')
        ->assertDispatched('toast', type: 'error');

    $this->user->refresh();
    expect($this->user->phone_verified_at)->toBeNull();
});

it('can resend otp', function () {
    $mockOtp = $this->mock(OtpService::class);
    $mockOtp->shouldReceive('resend')
        ->once()
        ->with($this->user->phone, OtpType::PhoneVerification, $this->user->id)
        ->andReturn(true);

    Livewire::actingAs($this->user)
        ->test(VerifyPhone::class)
        ->call('resend')
        ->assertDispatched('toast', type: 'success')
        ->assertDispatched('resend-cooldown');
});
