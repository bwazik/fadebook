<?php

namespace App\Services;

use App\Contracts\WhatsAppNotificationChannel;
use App\Enums\ShopStatus;
use App\Models\Shop;

class ShopReviewService
{
    public function __construct(public WhatsAppNotificationChannel $notifier) {}

    public function approve(Shop $shop): Shop
    {
        $shop->loadMissing('owner');

        $shop->update([
            'status' => ShopStatus::Active,
            'rejection_reason' => null,
        ]);

        $this->notifier->send($shop->owner->phone, 'تمت الموافقة على الصالون بتاعك.');

        return $shop->fresh(['owner']);
    }

    public function reject(Shop $shop, string $reason): Shop
    {
        $shop->loadMissing('owner');

        $shop->update([
            'status' => ShopStatus::Rejected,
            'rejection_reason' => $reason,
        ]);

        $this->notifier->send($shop->owner->phone, sprintf('تم رفض طلب الصالون. السبب: %s', $reason));

        return $shop->fresh(['owner']);
    }
}
