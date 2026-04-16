<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Shop;
use App\Services\OfferService;

class ShopObserver
{
    /**
     * Handle the Shop "updated" event.
     */
    public function updated(Shop $shop): void
    {
        // When a shop is approved (Pending -> Active)
        if ($shop->wasChanged('status') && $shop->status === ShopStatus::Active) {
            $owner = $shop->owner;

            if ($owner && $owner->role !== UserRole::BarberOwner) {
                $owner->update([
                    'role' => UserRole::BarberOwner,
                ]);
            }

            // Optional: Set approved_at if not set
            if (! $shop->approved_at) {
                $shop->updateQuietly([
                    'approved_at' => now(),
                ]);
            }
        }

        // Sync offers if referral status or name changed
        if ($shop->wasChanged(['referral_enabled', 'name'])) {
            app(OfferService::class)->syncOffersForShop($shop);
        }
    }
}
