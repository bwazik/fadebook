<?php

namespace App\Notifications\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     */
    public function send($notifiable, Notification $notification): void
    {
        if (! $notifiable->phone) {
            return;
        }

        if (! method_exists($notification, 'getWhatsAppTemplate')) {
            return;
        }

        /** @var mixed $notificationInstance */
        $notificationInstance = $notification;
        $template = $notificationInstance->getWhatsAppTemplate();

        if (! $template) {
            return;
        }

        $data = method_exists($notification, 'getWhatsAppData') ? $notificationInstance->getWhatsAppData() : [];
        $priority = method_exists($notification, 'getWhatsAppPriority') ? $notificationInstance->getWhatsAppPriority() : 'default';
        $shopId = method_exists($notification, 'getRelatedShopId') ? $notificationInstance->getRelatedShopId() : null;

        app(WhatsAppService::class)->sendMessage(
            phone: $notifiable->phone,
            template: $template,
            data: $data,
            priority: $priority,
            userId: $notifiable->id ?? null,
            shopId: $shopId
        );
    }
}
