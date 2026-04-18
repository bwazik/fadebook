<?php

namespace App\Notifications\Admin;

use App\Models\Booking;
use App\Notifications\Channels\WhatsAppChannel;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookingAdminNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        $barberInfo = $this->booking->barber ? " مع الحلاق {$this->booking->barber->name}" : '';

        return FilamentNotification::make()
            ->title('تسجيل حجز جديد')
            ->body("تم تسجيل حجز جديد في صالون {$this->booking->shop->name}{$barberInfo} للعميل {$this->booking->client->name}.")
            ->icon('heroicon-o-calendar-days')
            ->iconColor('info')
            ->actions([
                Action::make('view')
                    ->label('عرض الحجز')
                    ->url('/admin/bookings'),
            ])
            ->getDatabaseMessage();
    }

    public function getWhatsAppTemplate(): string
    {
        return 'new_booking_admin';
    }

    public function getWhatsAppData(): array
    {
        return [
            'shop_name' => $this->booking->shop->name,
            'barber_info' => $this->booking->barber ? "الحلاق: {$this->booking->barber->name}\n" : '',
            'client_name' => $this->booking->client->name,
            'service' => $this->booking->service->name,
            'time' => $this->booking->scheduled_at->format('Y-m-d H:i'),
            'total' => $this->booking->final_amount,
        ];
    }
}
