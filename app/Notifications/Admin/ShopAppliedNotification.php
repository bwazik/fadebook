<?php

namespace App\Notifications\Admin;

use App\Models\Shop;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ShopAppliedNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct(public Shop $shop) {}

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return $this->getStandardData();
    }

    protected function getEntityId()
    {
        return $this->shop->id;
    }

    protected function getNotificationType(): string
    {
        return 'shop_applied';
    }

    protected function getEntityType(): string
    {
        return 'shop';
    }

    protected function getTitle(): string
    {
        return 'طلب انضمام جديد';
    }

    protected function getShortMessage(): string
    {
        return "صالون {$this->shop->name} قدم طلب انضمام.";
    }

    protected function getMessage(): string
    {
        return "في صالون جديد عايز ينضم لينا: \n".
               "الاسم: {$this->shop->name} \n".
               "الهاتف: {$this->shop->phone} \n".
               "العنوان: {$this->shop->address}";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-building-storefront';
    }

    protected function getIconBg(): string
    {
        return 'bg-amber-600';
    }

    protected function getActionUrl(): string
    {
        return '/admin/shops';
    }

    protected function getActionText(): string
    {
        return 'مراجعة الطلبات';
    }

    protected function getCustomData(): array
    {
        return [
            'shop_id' => $this->shop->id,
            'shop_name' => $this->shop->name,
        ];
    }

    public function getWhatsAppData(): array
    {
        return [
            'shop_name' => $this->shop->name,
            'owner_name' => $this->shop->owner->name ?? 'صاحب محل',
            'phone' => $this->shop->phone,
            'area' => $this->shop->address,
        ];
    }
}
