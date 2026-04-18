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

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing(['shop', 'barber']);
    }

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
        return "تذكير بميعاد الحجز {$this->booking->booking_code} في صالون {$this->booking->shop->name}.";
    }

    protected function getMessage(): string
    {
        $barberInfo = $this->booking->barber ? " مع الحلاق {$this->booking->barber->name}" : '';

        return "ميعاد الحجز {$this->booking->booking_code} في صالون {$this->booking->shop->name}{$barberInfo} اقترب. يرجى التوجه للصالون في الموعد المحدد.";
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
        return 'عرض الحجز';
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
        return 'booking_reminder_client';
    }

    public function getWhatsAppData(): array
    {
        return [
            'shop_name' => $this->booking->shop->name,
            'with_barber' => $this->booking->barber ? " مع الحلاق {$this->booking->barber->name}" : '',
            'time' => $this->booking->scheduled_at->translatedFormat('l, d F Y').' الساعة '.$this->booking->scheduled_at->format('g:i A'),
            'booking_code' => $this->booking->booking_code,
            'settings_url' => route('profile.settings'),
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
