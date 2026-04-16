<?php

use App\Enums\ReferralStatus;
use App\Models\Referral;
use App\Models\User;
use App\Services\ReferralService;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(SettingsService::class)->set('referral_enabled', 'true');
});

test('it creates a referral code for a new user on registration', function () {
    $user = User::factory()->create();

    expect($user->referral_code)->not->toBeNull()
        ->and(strlen($user->referral_code))->toBe(8);
});

test('it processes referral when new user registers with a valid referral code', function () {
    $referrer = User::factory()->create();

    $referredUser = User::factory()->create();

    $referralService = app(ReferralService::class);
    $referralService->handleRegistration($referredUser, $referrer->referral_code);

    $referral = Referral::where('referrer_id', $referrer->id)
        ->where('invitee_id', $referredUser->id)
        ->first();

    expect($referral)->not->toBeNull()
        ->and($referral->status)->toBe(ReferralStatus::Pending);
});

test('it skips referral when referral system is disabled', function () {
    app(SettingsService::class)->set('referral_enabled', 'false');

    $referrer = User::factory()->create();
    $referredUser = User::factory()->create();

    $referralService = app(ReferralService::class);
    $referralService->handleRegistration($referredUser, $referrer->referral_code);

    $count = Referral::count();
    expect($count)->toBe(0);
});

test('it skips referral processing when referrer code is invalid', function () {
    $referredUser = User::factory()->create();

    $referralService = app(ReferralService::class);
    $referralService->handleRegistration($referredUser, 'INVALID1');

    $count = Referral::count();
    expect($count)->toBe(0);
});
