<?php

namespace App\Notifications\Admin;

use App\Models\User;
use App\Notifications\Channels\WhatsAppChannel;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserBlockedAdminNotification extends Notification
{
    use Queueable;

    public function __construct(public User $user, public string $reason) {}

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('تعليق حساب عميل')
            ->body("تم تعليق حساب العميل {$this->user->name} تلقائياً. السبب: {$this->reason}.")
            ->icon('heroicon-o-lock-closed')
            ->iconColor('danger')
            ->getDatabaseMessage();
    }

    public function getWhatsAppTemplate(): string
    {
        return 'user_blocked_admin';
    }

    public function getWhatsAppData(): array
    {
        return [
            'user_name' => $this->user->name,
            'reason' => $this->reason,
            'phone' => $this->user->phone,
        ];
    }

    public function getWhatsAppPriority(): string
    {
        return 'instant';
    }
}
