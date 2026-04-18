<?php

namespace App\Notifications\User;

use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingReminderNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct(public Booking $booking) {}

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
        return $this->booking->id;
    }

    protected function getNotificationType(): string
    {
        return 'booking_reminder';
    }

    protected function getEntityType(): string
    {
        return 'booking';
    }

    protected function getTitle(): string
    {
        return 'تذكير بميعادك';
    }

    protected function getShortMessage(): string
    {
        return "تذكير: حجزك في {$this->booking->shop->name} كمان ساعة";
    }

    protected function getMessage(): string
    {
        $settingsLink = route('profile.settings');

        return "باقي ساعة على ميعادك في {$this->booking->shop->name}. من فضلك حاول تكون موجود في الميعاد عشان ما يضعش عليك. \n\n لو عايز تقفل التنبيهات تقدر تدخل من هنا: {$settingsLink}";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-clock';
    }

    protected function getIconBg(): string
    {
        return 'bg-amber-500';
    }

    protected function getActionUrl(): string
    {
        return route('booking.show', $this->booking->uuid);
    }

    protected function getActionText(): string
    {
        return 'تفاصيل الحجز';
    }

    protected function getCustomData(): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }

    public function getWhatsAppData(): array
    {
        return [
            'shop_name' => $this->booking->shop->name,
            'time' => $this->booking->scheduled_at->format('H:i'),
            'settings_url' => route('profile.settings'),
        ];
    }

    public function getRelatedShopId(): ?int
    {
        return $this->booking->shop_id;
    }
}
