<?php

namespace App\Notifications\Admin;

use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusChangedAdminNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct(public Booking $booking, public ?string $statusLabel = null)
    {
        $this->statusLabel ??= $booking->status->getLabel();
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
        return 'admin_booking_status_changed';
    }

    protected function getEntityType(): string
    {
        return 'booking';
    }

    protected function getTitle(): string
    {
        return "تغيير حالة حجز: {$this->statusLabel}";
    }

    protected function getShortMessage(): string
    {
        return "تغيير حالة حجز {$this->booking->booking_code} لـ {$this->statusLabel}";
    }

    protected function getMessage(): string
    {
        return "حالة الحجز اتغيرت لـ ({$this->statusLabel}): \n".
               "الصالون: {$this->booking->shop->name} \n".
               "العميل: {$this->booking->client->name} \n".
               "الكود: {$this->booking->booking_code} \n".
               "الميعاد: {$this->booking->scheduled_at->format('Y-m-d H:i')}";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-arrow-path';
    }

    protected function getIconBg(): string
    {
        return 'bg-slate-600';
    }

    protected function getActionUrl(): string
    {
        return '/admin/bookings';
    }

    protected function getActionText(): string
    {
        return 'عرض الحجوزات';
    }

    protected function getCustomData(): array
    {
        return [
            'booking_id' => $this->booking->id,
            'new_status' => $this->statusLabel,
        ];
    }

    public function getWhatsAppData(): array
    {
        return [
            'shop_name' => $this->booking->shop->name,
            'client_name' => $this->booking->client->name,
            'status' => $this->statusLabel,
            'booking_code' => $this->booking->booking_code,
            'time' => $this->booking->scheduled_at->format('Y-m-d H:i'),
        ];
    }
}
