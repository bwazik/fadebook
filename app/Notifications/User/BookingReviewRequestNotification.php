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
        return 'نعيماً! شاركنا تقييمك';
    }

    protected function getShortMessage(): string
    {
        return "نعيماً! شاركنا تقييمك لتجربتك في صالون {$this->booking->shop->name}.";
    }

    protected function getMessage(): string
    {
        $barberInfo = $this->booking->barber ? " مع الحلاق {$this->booking->barber->name}" : '';

        return "نعيماً! نتمنى أن تكون التجربة ممتازة في صالون {$this->booking->shop->name}{$barberInfo}. يرجى إعطاء تقييم لمساعدتنا في تحسين الخدمة.";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-star';
    }

    protected function getIconBg(): string
    {
        return 'bg-blue-500';
    }

    protected function getActionUrl(): string
    {
        return route('review.create', $this->booking->uuid);
    }

    protected function getActionText(): string
    {
        return 'إضافة تقييم';
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
        return 'booking_review_request';
    }

    public function getWhatsAppData(): array
    {
        return [
            'shop_name' => $this->booking->shop->name,
            'barber_info' => $this->booking->barber ? " مع الحلاق {$this->booking->barber->name}" : '',
            'review_url' => route('review.create', $this->booking->uuid),
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
