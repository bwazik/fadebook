<?php

namespace App\Notifications\Admin;

use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookingAdminNotification extends Notification
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
        return 'new_booking_admin';
    }

    protected function getEntityType(): string
    {
        return 'booking';
    }

    protected function getTitle(): string
    {
        return 'حجز جديد في السيستم';
    }

    protected function getShortMessage(): string
    {
        return "حجز جديد في صالون {$this->booking->shop->name}";
    }

    protected function getMessage(): string
    {
        return "في حجز جديد اتعمل: \n".
               "الصالون: {$this->booking->shop->name} \n".
               "العميل: {$this->booking->client->name} \n".
               "الميعاد: {$this->booking->scheduled_at->format('Y-m-d H:i')} \n".
               "المبلغ: {$this->booking->final_amount} ج.م";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-calendar-days';
    }

    protected function getIconBg(): string
    {
        return 'bg-indigo-600';
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
            'shop_name' => $this->booking->shop->name,
        ];
    }

    public function getWhatsAppData(): array
    {
        return [
            'shop_name' => $this->booking->shop->name,
            'client_name' => $this->booking->client->name,
            'service' => $this->booking->service->name,
            'time' => $this->booking->scheduled_at->format('Y-m-d H:i'),
            'total' => $this->booking->final_amount,
        ];
    }
}
