<?php

namespace App\Notifications\Barbershop;

use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification
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
        return 'booking_created_barber';
    }

    protected function getEntityType(): string
    {
        return 'booking';
    }

    protected function getTitle(): string
    {
        return 'حجز جديد';
    }

    protected function getShortMessage(): string
    {
        return "حجز جديد ومحتاج تأكيد من {$this->booking->client->name}";
    }

    protected function getMessage(): string
    {
        $service = $this->booking->service->name;
        $price = $this->booking->final_amount;
        $method = $this->booking->paymentMethod->name ?? 'غير محدد';
        $ref = $this->booking->payment_reference ?? 'بدون';

        return "حجز جديد ومحتاج تأكيد! \n".
               "العميل: {$this->booking->client->name} \n".
               "التليفون: {$this->booking->client->phone} \n".
               "الخدمة: {$service} \n".
               "الميعاد: {$this->booking->scheduled_at->format('Y-m-d H:i')} \n".
               "المبلغ: {$price} ج.م \n".
               "طريقة الدفع: {$method} \n".
               "رقم العملية: {$ref}";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-calendar';
    }

    protected function getIconBg(): string
    {
        return 'bg-blue-500';
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

    public function getWhatsAppData(): array
    {
        return [
            'client_name' => $this->booking->client->name,
            'service' => $this->booking->service->name,
            'time' => $this->booking->scheduled_at->format('Y-m-d H:i'),
            'payment_method' => $this->booking->paymentMethod->name ?? 'غير محدد',
            'payment_ref' => $this->booking->payment_reference ?? 'بدون',
        ];
    }

    public function getRelatedShopId(): ?int
    {
        return $this->booking->shop_id;
    }
}
