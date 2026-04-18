<?php

namespace App\Notifications\Barbershop;

use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCancelledOwnerNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct(public Booking $booking) {}

    public function via($notifiable): array
    {
        return [WhatsAppChannel::class];
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
        return 'booking_cancelled_owner';
    }

    protected function getEntityType(): string
    {
        return 'booking';
    }

    protected function getTitle(): string
    {
        return 'تم إلغاء حجز';
    }

    protected function getShortMessage(): string
    {
        return "قام العميل {$this->booking->client->name} بإلغاء موعده الساعة {$this->booking->scheduled_at->translatedFormat('H:i')}.";
    }

    protected function getMessage(): string
    {
        $barberInfo = $this->booking->barber ? " مع الحلاق {$this->booking->barber->name}" : '';

        return "إشعار: تم إلغاء الحجز من قِبل العميل {$this->booking->client->name} الخاص بموعد {$this->booking->scheduled_at->translatedFormat('Y-m-d H:i')}{$barberInfo}. والموعد متاح الآن للحجز مرة أخرى.";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-calendar-days';
    }

    protected function getIconBg(): string
    {
        return 'bg-red-500';
    }

    protected function getActionUrl(): string
    {
        return '/admin';
    }

    protected function getActionText(): string
    {
        return 'لوحة التحكم';
    }

    protected function getCustomData(): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }

    public function getWhatsAppTemplate(): string
    {
        return 'booking_cancelled_owner';
    }

    public function getWhatsAppData(): array
    {
        return [
            'client_name' => $this->booking->client->name,
            'barber_name' => $this->booking->barber?->name ?? 'غير محدد',
            'service' => $this->booking->service->name,
            'time' => $this->booking->scheduled_at->translatedFormat('Y-m-d H:i'),
        ];
    }

    public function getRelatedShopId(): ?int
    {
        return $this->booking->shop_id;
    }

    public function getWhatsAppPriority(): string
    {
        return 'urgent';
    }
}
