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
        return 'مرحتش الحجز!';
    }

    protected function getShortMessage(): string
    {
        return "تحذير: تم تسجيلك كغياب عن ميعادك في {$this->booking->shop->name}";
    }

    protected function getMessage(): string
    {
        $settingsLink = route('profile.settings');

        return "تم تسجيلك كغياب (No-Show) في حجزك مع {$this->booking->shop->name}. ده التحذير رقم {$this->strikeCount}. لو اتكرر الموضوع حسابك هيتوقف. \n\n لو عايز تقفل التنبيهات تقدر تدخل من هنا: {$settingsLink}";
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
}
