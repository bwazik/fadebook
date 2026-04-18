<?php

namespace App\Notifications\User;

use App\Notifications\Channels\FcmChannel;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountBlockedCancellationNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct() {}

    public function via($notifiable): array
    {
        return ['database', FcmChannel::class, WhatsAppChannel::class];
    }

    public function toDatabase($notifiable): array
    {
        return $this->getStandardData();
    }

    protected function getEntityId()
    {
        return null;
    }

    protected function getNotificationType(): string
    {
        return 'account_blocked_cancellation';
    }

    protected function getEntityType(): string
    {
        return 'user';
    }

    protected function getTitle(): string
    {
        return 'حسابك اتوقف';
    }

    protected function getShortMessage(): string
    {
        return 'حسابك اتوقف لتكرار الإلغاء';
    }

    protected function getMessage(): string
    {
        $settingsLink = route('profile.settings');

        return "تم إيقاف حسابك لتكرار إلغاء الحجوزات. يرجى التواصل مع الدعم. \n\n لو عايز تقفل التنبيهات تقدر تدخل من هنا: {$settingsLink}";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-no-symbol';
    }

    protected function getIconBg(): string
    {
        return 'bg-red-600';
    }

    protected function getActionUrl(): string
    {
        return '/contact';
    }

    protected function getActionText(): string
    {
        return 'تواصل معانا';
    }

    protected function getCustomData(): array
    {
        return [];
    }

    public function getWhatsAppData(): array
    {
        return [
            'settings_url' => route('profile.settings'),
        ];
    }
}
