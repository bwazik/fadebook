<?php

namespace App\Notifications\User;

use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingReviewRequestNotification extends Notification
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
        return 'booking_review_request';
    }

    protected function getEntityType(): string
    {
        return 'booking';
    }

    protected function getTitle(): string
    {
        return 'نعيماً! إيه رأيك؟';
    }

    protected function getShortMessage(): string
    {
        return "نعيماً! إيه رأيك في حلاقتك في {$this->booking->shop->name}؟";
    }

    protected function getMessage(): string
    {
        $settingsLink = route('profile.settings');

        return "نعيماً! أتمنى تكون مبسوط من حلاقتك في {$this->booking->shop->name}. ياريت تقيم تجربتك عشان تفيد غيرك. \n\n لو عايز تقفل التنبيهات تقدر تدخل من هنا: {$settingsLink}";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-star';
    }

    protected function getIconBg(): string
    {
        return 'bg-amber-400';
    }

    protected function getActionUrl(): string
    {
        return route('booking.show', $this->booking->uuid);
    }

    protected function getActionText(): string
    {
        return 'قيم دلوقتي';
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
            'review_url' => route('review.create', $this->booking->uuid),
            'settings_url' => route('profile.settings'),
        ];
    }

    public function getRelatedShopId(): ?int
    {
        return $this->booking->shop_id;
    }
}
