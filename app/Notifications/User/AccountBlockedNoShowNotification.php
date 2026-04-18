<?php

namespace App\Notifications\User;

use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountBlockedNoShowNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct() {}

    public function via($notifiable): array
    {
        return ['database', WhatsAppChannel::class];
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
        return 'account_blocked_no_show';
    }

    protected function getEntityType(): string
    {
        return 'user';
    }

    protected function getTitle(): string
    {
        return 'تنبيه أمان: تم تعليق حسابك';
    }

    protected function getShortMessage(): string
    {
        return 'تم تعليق حسابك مؤقتاً لتكرار الغياب، يرجى التواصل مع الدعم.';
    }

    protected function getMessage(): string
    {
        return 'للأسف تم تعليق حسابك مؤقتاً بسبب تكرار عدم الحضور (No-Show) في المواعيد المؤكدة. يرجى التواصل مع الدعم الفني لحل المشكلة.';
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
