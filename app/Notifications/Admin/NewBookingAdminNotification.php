<?php

namespace App\Notifications\Admin;

use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookingAdminNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing(['shop', 'client', 'service', 'barber', 'paymentMethod', 'coupon']);
    }

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        $timeStr = $this->booking->scheduled_at->translatedFormat('l, d F Y').' الساعة '.$this->booking->scheduled_at->format('g:i A');

        return FilamentNotification::make()
            ->title('تسجيل حجز جديد')
            ->body("صالون {$this->booking->shop->name} | كود الحجز: {$this->booking->booking_code} | الميعاد: {$timeStr}")
            ->icon('heroicon-o-calendar-days')
            ->iconColor('info')
            ->getDatabaseMessage();
    }

    public function getWhatsAppTemplate(): string
    {
        return 'new_booking_admin';
    }

    public function getWhatsAppData(): array
    {
        return [
            'booking_details' => $this->formatBookingDetails(),
        ];
    }

    public function getWhatsAppPriority(): string
    {
        return 'instant';
    }

    protected function formatBookingDetails(): string
    {
        $lines = [
            "كود الحجز: {$this->booking->booking_code}",
            "الصالون: {$this->booking->shop->name}",
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
