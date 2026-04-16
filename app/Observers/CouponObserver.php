<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Coupon;
use App\Services\OfferService;

class CouponObserver
{
    public function created(Coupon $coupon): void
    {
        $this->sync($coupon);
    }

    public function updated(Coupon $coupon): void
    {
        $this->sync($coupon);
    }

    public function deleted(Coupon $coupon): void
    {
        $this->sync($coupon);
    }

    protected function sync(Coupon $coupon): void
    {
        if ($coupon->shop_id) {
            app(OfferService::class)->syncOffersForShop($coupon->shop);
        }
    }
}
