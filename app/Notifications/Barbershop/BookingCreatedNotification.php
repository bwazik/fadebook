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
        return 'طلب حجز جديد';
    }

    protected function getShortMessage(): string
    {
        return "طلب حجز جديد بانتظار التأكيد من العميل {$this->booking->client->name}.";
    }

    protected function getMessage(): string
    {
        $service = $this->booking->service->name;
        $barberInfo = $this->booking->barber ? "الحلاق: {$this->booking->barber->name} \n" : '';
        $paymentInfo = $this->booking->paymentMethod ? "طريقة الدفع: {$this->booking->paymentMethod->name} \n" : '';
        $refInfo = $this->booking->payment_reference ? "الرقم المرجعي: {$this->booking->payment_reference} \n" : '';

        return "يوجد طلب حجز جديد بانتظار تأكيدك! \n".
               "{$barberInfo}".
               "العميل: {$this->booking->client->name} \n".
               "رقم الموبايل: {$this->booking->client->phone} \n".
               "الخدمة: {$service} \n".
               "الميعاد: {$this->booking->scheduled_at->translatedFormat('Y-m-d H:i')} \n\n".
               "{$paymentInfo}{$refInfo}".
               'أدخل على لوحة التحكم للحصول على التفاصيل وتأكيد الموعد.';
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-calendar-days';
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

    public function getWhatsAppTemplate(): string
    {
        return 'booking_created_barber';
    }

    public function getWhatsAppData(): array
    {
        return [
            'client_name' => $this->booking->client->name,
            'barber_info' => $this->booking->barber ? "الحلاق: {$this->booking->barber->name}\n" : '',
            'service' => $this->booking->service->name,
            'time' => $this->booking->scheduled_at->translatedFormat('Y-m-d H:i'),
            'payment_info' => $this->booking->paymentMethod ? "طريقة الدفع: {$this->booking->paymentMethod->name}\n" : '',
            'payment_ref_info' => $this->booking->payment_reference ? "الرقم المرجعي للتحويل: {$this->booking->payment_reference}\n" : '',
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
