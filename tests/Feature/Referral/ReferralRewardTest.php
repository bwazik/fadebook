<?php

use App\Enums\BookingStatus;
use App\Enums\ReferralStatus;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Referral;
use App\Models\Shop;
use App\Models\User;
use App\Services\ReferralService;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    app(SettingsService::class)->set('referral_enabled', 'true');
    app(SettingsService::class)->set('referral_unlimited_mode', 'true');
    app(SettingsService::class)->set('referral_reward_discount_type', '1');
    app(SettingsService::class)->set('referral_reward_discount_value', '20');
    app(SettingsService::class)->set('referral_reward_expiry_days', '30');
});

test('it rewards the referrer when the referred user completes their first booking', function () {
    $referrer = User::factory()->create();
    $referredUser = User::factory()->create();
    $shop = Shop::factory()->create();

    // Create a pending referral
    $referral = Referral::create([
        'referrer_id' => $referrer->id,
        'invitee_id' => $referredUser->id,
        'status' => ReferralStatus::Pending,
    ]);

    $booking = Booking::factory()->create([
        'client_id' => $referredUser->id,
        'shop_id' => $shop->id,
        'status' => BookingStatus::Confirmed,
    ]);

    $referralService = app(ReferralService::class);
    $referralService->handleBookingCompleted($booking);

    $referral->refresh();

    expect($referral->status)->toBe(ReferralStatus::Rewarded)
        ->and($referral->coupon_id)->not->toBeNull();

    $coupon = Coupon::find($referral->coupon_id);

    expect($coupon)->not->toBeNull()
        ->and((float) $coupon->discount_value)->toBe(20.0)
        ->and($coupon->usage_limit)->toBe(1);
});

test('it skips non-pending referrals', function () {
    $referrer = User::factory()->create();
    $referredUser = User::factory()->create();
    $shop = Shop::factory()->create();

    // Create an already rewarded referral
    $referral = Referral::create([
        'referrer_id' => $referrer->id,
        'invitee_id' => $referredUser->id,
        'status' => ReferralStatus::Rewarded,
    ]);

    $booking = Booking::factory()->create([
        'client_id' => $referredUser->id,
        'shop_id' => $shop->id,
        'status' => BookingStatus::Confirmed,
    ]);

    $referralService = app(ReferralService::class);
    $referralService->handleBookingCompleted($booking);

    $referral->refresh();

    expect($referral->coupon_id)->toBeNull();
});

test('it skips unlimited mode false if referrer already reached limit', function () {
    app(SettingsService::class)->set('referral_unlimited_mode', 'false');

    $referrer = User::factory()->create();
    $referredUser1 = User::factory()->create();
    $referredUser2 = User::factory()->create();
    $shop = Shop::factory()->create();

    $coupon = Coupon::factory()->create([
        'shop_id' => $shop->id,
    ]);

    // Already rewarded one previous user
    Referral::create([
        'referrer_id' => $referrer->id,
        'invitee_id' => $referredUser1->id,
        'status' => ReferralStatus::Rewarded,
        'coupon_id' => $coupon->id,
    ]);

    $referral = Referral::create([
        'referrer_id' => $referrer->id,
        'invitee_id' => $referredUser2->id,
        'status' => ReferralStatus::Pending,
    ]);

    $booking = Booking::factory()->create([
        'client_id' => $referredUser2->id,
        'shop_id' => $shop->id,
        'status' => BookingStatus::Confirmed,
    ]);

    $referralService = app(ReferralService::class);
    $referralService->handleBookingCompleted($booking);

    $referral->refresh();

    expect($referral->status)->toBe(ReferralStatus::Skipped);
});
