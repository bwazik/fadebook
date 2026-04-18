<?php

namespace App\Notifications\Admin;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusChangedAdminNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking, public ?string $statusLabel = null)
    {
        $this->booking->loadMissing(['shop', 'client', 'barber']);
        $this->statusLabel ??= $booking->status->getLabel();
    }

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title("تحديث حالة الحجز: {$this->statusLabel}")
            ->body("رقم الحجز: {$this->booking->booking_code} | الحالة: {$this->statusLabel} | {$this->getStatusTimeLabel()}: {$this->getStatusTime()}")
            ->icon('heroicon-o-arrow-path')
            ->iconColor('gray')
            ->getDatabaseMessage();
    }

    public function getWhatsAppTemplate(): string
    {
        return 'booking_status_changed_admin';
    }

    public function getWhatsAppData(): array
    {
        return [
            'status_label' => $this->statusLabel,
            'booking_code' => $this->booking->booking_code,
            'status_time_label' => $this->getStatusTimeLabel(),
            'status_time' => $this->getStatusTime(),
        ];
    }

    public function getWhatsAppPriority(): string
    {
        return 'instant';
    }

    protected function getStatusTime(): string
    {
        $statusTime = match ($this->booking->status) {
            BookingStatus::Pending => $this->booking->created_at,
            BookingStatus::Confirmed => $this->booking->confirmed_at ?? $this->booking->payment_verified_at,
            BookingStatus::InProgress => $this->booking->arrived_at,
            BookingStatus::Completed => $this->booking->completed_at,
            BookingStatus::Cancelled => $this->booking->cancelled_at,
            BookingStatus::NoShow => $this->booking->updated_at,
        };

        $time = $statusTime ?? $this->booking->updated_at ?? now();

        return $time->translatedFormat('l, d F Y').' الساعة '.$time->format('g:i A');
    }

    protected function getStatusTimeLabel(): string
    {
        return match ($this->booking->status) {
            BookingStatus::Pending => 'وقت الطلب',
            BookingStatus::Confirmed => 'وقت التأكيد',
            BookingStatus::InProgress => 'وقت الوصول',
            BookingStatus::Completed => 'وقت الإكمال',
            BookingStatus::Cancelled => 'وقت الإلغاء',
            BookingStatus::NoShow => 'وقت تسجيل الحالة',
        };
    }
}
