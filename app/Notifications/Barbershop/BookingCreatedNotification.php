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

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing(['shop', 'client', 'service', 'barber', 'paymentMethod', 'coupon']);
    }

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
        return "طلب حجز جديد برقم {$this->booking->booking_code} من العميل {$this->booking->client->name}.";
    }

    protected function getMessage(): string
    {
        return "يوجد طلب حجز جديد بانتظار تأكيدك:\n\n{$this->formatBookingDetails()}\n\nادخل على لوحة التحكم عشان تراجع الحجز وتأكد المعاد.";
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
            'booking_details' => $this->formatBookingDetails(),
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

    protected function formatBookingDetails(): string
    {
        $lines = [
            "كود الحجز: {$this->booking->booking_code}",
            "العميل: {$this->booking->client->name}",
        ];

        if ($this->booking->client?->phone) {
            $lines[] = "رقم الموبايل: {$this->booking->client->phone}";
        }

        if ($this->booking->barber?->name) {
            $lines[] = "الحلاق: {$this->booking->barber->name}";
        }

        if ($this->booking->service?->name) {
            $lines[] = "الخدمة: {$this->booking->service->name}";
        }

        $lines[] = 'التاريخ: '.$this->booking->scheduled_at->translatedFormat('l, d F Y');
        $lines[] = 'الوقت: '.$this->booking->scheduled_at->format('g:i A');

        if ($this->booking->shop->show_service_prices) {
            $lines[] = 'سعر الخدمة: '.$this->formatMoney($this->booking->service_price);
            $lines[] = 'الضريبة: '.$this->formatMoney(0);

            if ((float) $this->booking->discount_amount > 0) {
                $lines[] = 'الخصم: -'.$this->formatMoney($this->booking->discount_amount);
            }

            $lines[] = 'الإجمالي النهائي: '.$this->formatMoney($this->booking->final_amount);

            $remainingAmount = (float) $this->booking->final_amount - (float) $this->booking->paid_amount;
            if ($remainingAmount > 0) {
                $lines[] = 'المتبقي: '.$this->formatMoney($remainingAmount);
            }
        }

        if ((float) $this->booking->deposit_amount > 0) {
            $lines[] = 'المقدم المطلوب: '.$this->formatMoney($this->booking->deposit_amount);
        }

        if ((float) $this->booking->paid_amount > 0) {
            $lines[] = 'المدفوع: '.$this->formatMoney($this->booking->paid_amount);
        }

        if ($this->booking->coupon?->code) {
            $lines[] = "كوبون الخصم: {$this->booking->coupon->code}";
        }

        if ($this->booking->paymentMethod?->type?->getLabel()) {
            $lines[] = 'طريقة الدفع: '.$this->booking->paymentMethod->type->getLabel();
        }

        if ($this->booking->payment_reference) {
            $lines[] = "الرقم المرجعي: {$this->booking->payment_reference}";
        }

        if ($this->booking->notes) {
            $lines[] = "ملاحظات العميل: {$this->booking->notes}";
        }

        return implode("\n", $lines);
    }

    protected function formatMoney(float|string|int $amount): string
    {
        return number_format((float) $amount, 0).' ج.م';
    }
}
