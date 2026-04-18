<?php

namespace App\Notifications\User;

use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NoShowWarningNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct(public Booking $booking, public int $strikeCount) {}

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
        return 'no_show_warning';
    }

    protected function getEntityType(): string
    {
        return 'booking';
    }

    protected function getTitle(): string
    {
        return 'تنبيه: عدم حضور للموعد';
    }

    protected function getShortMessage(): string
    {
        return "تم تسجيل غيابك عن موعد الحجز في صالون {$this->booking->shop->name}.";
    }

    protected function getMessage(): string
    {
        return "تم تسجيلك كغياب (No-Show) في حجزك مع {$this->booking->shop->name}. هذا التحذير رقم ({$this->strikeCount}). تكرار الغياب قد يؤدي لتعليق حسابك التلقائي.";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-exclamation-triangle';
    }

    protected function getIconBg(): string
    {
        return 'bg-warning-500';
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
            'strike_count' => $this->strikeCount,
        ];
    }

    public function getWhatsAppData(): array
    {
        return [
            'shop_name' => $this->booking->shop->name,
            'strike_count' => $this->strikeCount,
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
