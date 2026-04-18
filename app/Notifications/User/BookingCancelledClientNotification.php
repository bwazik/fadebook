<?php

namespace App\Notifications\User;

use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCancelledClientNotification extends Notification
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
        return 'booking_cancelled_client';
    }

    protected function getEntityType(): string
    {
        return 'booking';
    }

    protected function getTitle(): string
    {
        return 'تنبيه: تم إلغاء حجزك';
    }

    protected function getShortMessage(): string
    {
        return "للأسف، تم إلغاء حجزك في صالون {$this->booking->shop->name}.";
    }

    protected function getMessage(): string
    {
        return "نعتذر لك، تم إلغاء حجزك في صالون {$this->booking->shop->name} بتاريخ {$this->booking->scheduled_at->format('Y-m-d H:i')}. يمكنك حجز موعد آخر أو التواصل مع الدعم.";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-x-circle';
    }

    protected function getIconBg(): string
    {
        return 'bg-red-500';
    }

    protected function getActionUrl(): string
    {
        return route('bookings.index');
    }

    protected function getActionText(): string
    {
        return 'حجوزاتي';
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
            'booking_code' => $this->booking->booking_code,
            'time' => $this->booking->scheduled_at->format('Y-m-d H:i'),
            'settings_url' => route('profile.settings'),
        ];
    }

    public function getRelatedShopId(): ?int
    {
        return $this->booking->shop_id;
    }
}
