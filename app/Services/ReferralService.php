<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\DiscountType;
use App\Enums\ReferralStatus;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralService
{
    /**
     * Called from the Register Livewire component after the new user is persisted.
     */
    public function handleRegistration(User $invitee, string $referralCode, ?string $shopId = null): void
    {
        $settings = app(SettingsService::class);
        $isReferralEnabled = filter_var($settings->get('referral_enabled', 'true'), FILTER_VALIDATE_BOOLEAN);

        if (! $isReferralEnabled) {
            return;
        }

        $referrer = User::where('referral_code', $referralCode)->first();

        if (! $referrer) {
            return;
        }

        // Make sure referrer isn't the invitee (edge case)
        if ($referrer->id === $invitee->id) {
            return;
        }

        // Avoid duplicate referral links
        Referral::firstOrCreate([
            'referrer_id' => $referrer->id,
            'invitee_id' => $invitee->id,
        ], [
            'shop_id' => $shopId ? (int) $shopId : null,
            'status' => ReferralStatus::Pending,
        ]);
    }

    /**
     * Called from BookingService::markCompleted().
     */
    public function handleBookingCompleted(Booking $booking): void
    {
        $invitee = $booking->client;

        if (! $invitee) {
            return;
        }

        // 1. Find the pending Referral for this booking's client
        $referral = Referral::where('invitee_id', $invitee->id)
            ->where('status', ReferralStatus::Pending)
            ->first();

        if (! $referral) {
            return;
        }

        // If the referral is tied to a specific shop, ensure the invitee booked at that shop
        if ($referral->shop_id && $referral->shop_id !== $booking->shop_id) {
            return;
        }

        // 2. Check if it's the invitee's FIRST completed booking
        $completedBookingsCount = Booking::where('client_id', $invitee->id)
            ->where('status', BookingStatus::Completed)
            ->count();

        // This booking is already marked as completed before this hook is called,
        // so the count should be exactly 1 if it's the first completed booking.
        if ($completedBookingsCount > 1) {
            return;
        }

        $referrer = $referral->referrer;

        // 3. Check referral_unlimited_mode and whether referrer already earned once
        $settings = app(SettingsService::class);

        // Global check
        $isReferralEnabled = filter_var($settings->get('referral_enabled', 'true'), FILTER_VALIDATE_BOOLEAN);
        if (! $isReferralEnabled) {
            return;
        }

        // PER-SHOP CHECK: Reward only if the shop has referrals enabled
        if (! $booking->shop->referral_enabled) {
            return;
        }

        $isUnlimited = filter_var($settings->get('referral_reward_unlimited_mode', 'true'), FILTER_VALIDATE_BOOLEAN);
        $canEarn = $this->canReferrerEarn($referrer, $isUnlimited);

        if (! $canEarn) {
            $referral->update(['status' => ReferralStatus::Skipped, 'booking_id' => $booking->id]);

            return;
        }

        // 4. Issue Reward
        DB::transaction(function () use ($referral, $referrer, $booking, $settings) {
            $discountType = (int) $settings->get('referral_reward_discount_type', DiscountType::Percentage->value);
            $discountValue = (float) $settings->get('referral_reward_discount_value', 15);
            $expiryDays = (int) $settings->get('referral_reward_coupon_expiry_days', 7);

            $couponCode = 'REF-'.strtoupper(Str::random(6));

            $coupon = Coupon::create([
                'shop_id' => $booking->shop_id,
                'user_id' => $referrer->id,
                'code' => $couponCode,
                'discount_type' => DiscountType::from($discountType),
                'discount_value' => $discountValue,
                'start_date' => now(),
                'end_date' => now()->addDays($expiryDays),
                'is_active' => true,
                'usage_limit' => 1,
                'usage_limit_per_user' => 1,
            ]);

            $referral->update([
                'status' => ReferralStatus::Rewarded,
                'coupon_id' => $coupon->id,
                'booking_id' => $booking->id,
                'rewarded_at' => now(),
            ]);

            // 6. Send WhatsApp to referrer
            // TODO: Send WhatsApp notification to referrer (referral_reward_issued)
        });
    }

    /**
     * Check if the referrer can still earn a reward.
     */
    public function canReferrerEarn(User $referrer, bool $isUnlimited = true): bool
    {
        if ($isUnlimited) {
            return true;
        }

        return ! Referral::where('referrer_id', $referrer->id)
            ->where('status', ReferralStatus::Rewarded)
            ->exists();
    }

    /**
     * Get the full referral link for a user.
     */
    public function getReferralLink(User $user): string
    {
        return route('register', ['ref' => $user->referral_code]);
    }
}
