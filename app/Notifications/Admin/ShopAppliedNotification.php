<?php

namespace App\Notifications\Admin;

use App\Models\Shop;
use App\Notifications\Channels\WhatsAppChannel;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ShopAppliedNotification extends Notification
{
    use Queueable;

    public function __construct(public Shop $shop) {}

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('طلب انضمام صالون جديد')
            ->body("صالون {$this->shop->name} يطلب الانضمام للمنصة. رقم التواصل: {$this->shop->phone}.")
            ->icon('heroicon-o-building-storefront')
            ->iconColor('success')
            ->url('/admin/shops')
            ->getDatabaseMessage();
    }

    public function getWhatsAppTemplate(): string
    {
        return 'shop_applied';
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
