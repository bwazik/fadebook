<?php

namespace App\Notifications\Admin;

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
        $this->statusLabel ??= $booking->status->getLabel();
    }

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        $barberInfo = $this->booking->barber ? "، الحلاق: {$this->booking->barber->name}" : '';

        return FilamentNotification::make()
            ->title("تحديث حالة الحجز: {$this->statusLabel}")
            ->body("تم تحديث حالة الحجز رقم {$this->booking->booking_code} (الصالون: {$this->booking->shop->name}{$barberInfo}) إلى {$this->statusLabel}.")
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
            'shop_name' => $this->booking->shop->name,
            'barber_info' => $this->booking->barber ? "الحلاق: {$this->booking->barber->name}\n" : '',
            'client_name' => $this->booking->client->name,
            'status_label' => $this->statusLabel,
            'booking_code' => $this->booking->booking_code,
            'time' => $this->booking->scheduled_at->translatedFormat('Y-m-d H:i'),
        ];
    }

    public function getWhatsAppPriority(): string
    {
        return 'urgent';
    }
}
