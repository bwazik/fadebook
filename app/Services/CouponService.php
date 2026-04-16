<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\DiscountType;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Service;
use App\Models\Shop;
use App\Models\User;
use Exception;

class CouponService
{
    /**
     * Validate a coupon code and calculate the discount for a specific shop and service.
     *
     * @throws Exception
     */
    public function validateAndCalculate(string $code, Shop $shop, Service $service, User $user): array
    {
        $code = strtoupper(trim($code));

        $coupon = Coupon::where('code', $code)
            ->where(function ($query) use ($shop) {
                $query->where('shop_id', $shop->id)
                    ->orWhereNull('shop_id');
            })
            ->where('is_active', true)
            ->first();

        if (! $coupon) {
            throw new Exception(__('messages.coupon_invalid'));
        }

        $this->validateGeneralRules($coupon, (float) $service->price, $user);

        $discountValue = (float) $coupon->discount_value;
        $discountAmount = 0;

        if ($coupon->discount_type === DiscountType::Fixed) {
            $discountAmount = $discountValue;
        } elseif ($coupon->discount_type === DiscountType::Percentage) {
            $discountAmount = ($service->price * $discountValue) / 100;
        }

        $discountAmount = min($discountAmount, (float) $service->price);

        return [
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
            'final_amount' => max(0, $service->price - $discountAmount),
        ];
    }

    /**
     * Check general validity rules for a coupon.
     *
     * @throws Exception
     */
    protected function validateGeneralRules(Coupon $coupon, float $amount, User $user): void
    {
        $now = now();

        if ($coupon->start_date && $coupon->start_date > $now) {
            throw new Exception(__('messages.coupon_not_started'));
        }

        if ($coupon->end_date && $coupon->end_date < $now) {
            throw new Exception(__('messages.coupon_expired'));
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            throw new Exception(__('messages.coupon_limit_reached'));
        }

        if ($coupon->minimum_amount && $amount < $coupon->minimum_amount) {
            throw new Exception(__('messages.coupon_min_amount', ['amount' => $coupon->minimum_amount]));
        }

        // Check user usage specifically for this coupon in confirmed/completed bookings
        $userUsage = $user->bookings()
            ->where('coupon_id', $coupon->id)
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Completed, BookingStatus::InProgress])
            ->count();

        if ($coupon->usage_limit_per_user && $userUsage >= $coupon->usage_limit_per_user) {
            throw new Exception(__('messages.coupon_user_limit_reached'));
        }

        // If this coupon is locked to a specific user (e.g. referral coupons), reject all others
        if ($coupon->user_id && $coupon->user_id !== $user->id) {
            throw new Exception(__('messages.coupon_invalid'));
        }
    }

    /**
     * Record usage of a coupon after a successful booking confirmation.
     */
    public function recordUsage(Coupon $coupon, User $user): void
    {
        // 1. Increment global count
        $coupon->increment('used_count');

        // 2. Track per-user usage
        $usage = CouponUsage::firstOrCreate(
            ['coupon_id' => $coupon->id, 'user_id' => $user->id],
            ['usage_count' => 0]
        );
        $usage->increment('usage_count');
    }
}
