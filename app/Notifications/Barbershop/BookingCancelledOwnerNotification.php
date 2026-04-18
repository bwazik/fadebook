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

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing(['client', 'service', 'barber']);
    }

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
        return "تم إلغاء الحجز {$this->booking->booking_code} من العميل {$this->booking->client->name}.";
    }

    protected function getMessage(): string
    {
        return "إشعار: تم إلغاء الحجز من قِبل العميل.\n\n{$this->formatBookingDetails()}\n\nالخانة دي متاحة للحجز مرة تانية في النظام.";
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
            'booking_details' => $this->formatBookingDetails(),
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

    protected function formatBookingDetails(): string
    {
        $lines = [
            "كود الحجز: {$this->booking->booking_code}",
            "العميل: {$this->booking->client->name}",
        ];

        if ($this->booking->barber?->name) {
            $lines[] = "الحلاق: {$this->booking->barber->name}";
        }

        if ($this->booking->service?->name) {
            $lines[] = "الخدمة: {$this->booking->service->name}";
        }

        $lines[] = 'التاريخ: '.$this->booking->scheduled_at->translatedFormat('l, d F Y');
        $lines[] = 'الوقت: '.$this->booking->scheduled_at->format('g:i A');

        return implode("\n", $lines);
    }
}
