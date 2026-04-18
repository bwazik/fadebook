<?php

namespace App\Notifications\Admin;

use App\Models\User;
use App\Notifications\Channels\WhatsAppChannel;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserSecurityChangedAdminNotification extends Notification
{
    use Queueable;

    public function __construct(public User $user, public string $changeType) {}

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        $action = $this->changeType === 'phone' ? 'تغيير رقم الموبايل' : 'تغيير كلمة المرور';
        $body = $this->changeType === 'phone'
            ? "العميل {$this->user->name} قام بتغيير رقم موبايله بنجاح إلى {$this->user->phone}."
            : "العميل {$this->user->name} قام بتغيير كلمة المرور الخاصة به.";

        return FilamentNotification::make()
            ->title("تحديث أمني: {$action}")
            ->body($body)
            ->icon('heroicon-o-shield-check')
            ->iconColor('warning')
            ->getDatabaseMessage();
    }

    public function getWhatsAppTemplate(): string
    {
        return 'user_security_changed_admin';
    }

    public function getWhatsAppData(): array
    {
        $action = $this->changeType === 'phone' ? 'تغيير رقم الموبايل' : 'تغيير كلمة المرور';
        $details = $this->changeType === 'phone'
            ? "الرقم الجديد هو: {$this->user->phone}"
            : 'تم تغيير كلمة المرور بنجاح.';

        return [
            'user_name' => $this->user->name,
            'action' => $action,
            'details' => $details,
            'date' => now()->format('Y-m-d H:i'),
        ];
    }
}
