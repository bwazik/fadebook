<?php

namespace App\Notifications\User;

use App\Models\Booking;
use App\Notifications\Channels\FcmChannel;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct(public Booking $booking) {}

    public function via($notifiable): array
    {
        return ['database', FcmChannel::class, WhatsAppChannel::class];
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
        return 'booking_confirmed_client';
    }

    protected function getEntityType(): string
    {
        return 'booking';
    }

    protected function getTitle(): string
    {
        return 'تم تأكيد حجزك';
    }

    protected function getShortMessage(): string
    {
        return "تم تأكيد حجزك في {$this->booking->shop->name}";
    }

    protected function getMessage(): string
    {
        $settingsLink = route('profile.settings');

        return "حجزك في {$this->booking->shop->name} ميعاده {$this->booking->scheduled_at->format('Y-m-d H:i')}. من فضلك تكون موجود قبل الميعاد بـ 15 دقيقة. \n\n لو عايز تقفل التنبيهات تقدر تدخل من هنا: {$settingsLink}";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-check-circle';
    }

    protected function getIconBg(): string
    {
        return 'bg-emerald-500';
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
            'barber_name' => $this->booking->barber->user->name ?? 'متاح',
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
