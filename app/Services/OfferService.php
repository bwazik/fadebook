<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OfferType;
use App\Models\Offer;
use App\Models\Shop;

class OfferService
{
    /**
     * Synchronize offers for a given shop based on its coupons and referral status.
     */
    public function syncOffersForShop(Shop $shop): void
    {
        // 1. Handle Referral Offer
        if ($shop->referral_enabled) {
            Offer::updateOrCreate(
                [
                    'shop_id' => $shop->id,
                    'type' => OfferType::Referral,
                ],
                [
                    'title' => __('messages.offers_referral_sync_title'),
                    'description' => __('messages.offers_referral_sync_desc'),
                    'is_active' => true,
                ]
            );
        } else {
            Offer::where('shop_id', $shop->id)
                ->where('type', OfferType::Referral)
                ->delete();
        }

        // 2. Handle Public Discount Offers
        $activeCoupons = $shop->coupons()
            ->whereNull('user_id')
            ->where('is_active', true)
            ->get();

        foreach ($activeCoupons as $coupon) {
            $isFixed = $coupon->discount_type->value !== 1; // 1 is Percentage usually, let's check DiscountType enum

            if ($isFixed && ! $shop->show_service_prices) {
                $discountLabel = __('messages.offers_discount_hidden');
            } else {
                $discountLabel = $coupon->discount_type->value === 1
                    ? __('messages.offers_discount_sync_label_percent', ['value' => (int) $coupon->discount_value])
                    : __('messages.offers_discount_sync_label_fixed', ['value' => (int) $coupon->discount_value]);
            }

            Offer::updateOrCreate(
                [
                    'shop_id' => $shop->id,
                    'type' => OfferType::Discount,
                    'coupon_id' => $coupon->id,
                ],
                [
                    'title' => $discountLabel,
                    'description' => __('messages.offers_discount_sync_desc', ['code' => $coupon->code]),
                    'start_date' => $coupon->start_date,
                    'end_date' => $coupon->end_date,
                    'is_active' => true,
                ]
            );
        }

        // 3. Cleanup: Remove discount offers for coupons that are no longer active/exist
        Offer::where('shop_id', $shop->id)
            ->where('type', OfferType::Discount)
            ->whereNotNull('coupon_id')
            ->whereNotIn('coupon_id', $activeCoupons->pluck('id'))
            ->delete();
    }

    /**
     * Synchronize offers for all shops in the system.
     */
    public function syncAll(): void
    {
        Shop::all()->each(fn (Shop $shop) => $this->syncOffersForShop($shop));
    }

    /**
     * Get the count of active offers for the notification badge.
     */
    public function getActiveOfferCount(): int
    {
        return Offer::where('is_active', true)->count();
    }
}
