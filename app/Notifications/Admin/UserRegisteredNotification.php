<?php

namespace App\Notifications\Admin;

use App\Models\User;
use App\Notifications\Channels\WhatsAppChannel;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(public User $user) {}

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('تسجيل عميل جديد')
            ->body("تم تسجيل عميل جديد في المنصة: {$this->user->name} ({$this->user->phone}).")
            ->icon('heroicon-o-user-plus')
            ->iconColor('info')
            ->getDatabaseMessage();
    }

    public function getWhatsAppTemplate(): string
    {
        return 'user_registered';
    }

    public function getWhatsAppData(): array
    {
        return [
            'user_name' => $this->user->name,
            'phone' => $this->user->phone,
            'date' => now()->format('Y-m-d H:i'),
        ];
    }

    public function getWhatsAppPriority(): string
    {
        return 'urgent';
    }
}
