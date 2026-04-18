<?php

namespace App\Notifications\Admin;

use App\Models\User;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserRegisteredNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct(public User $user) {}

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
        return $this->user->id;
    }

    protected function getNotificationType(): string
    {
        return 'admin_user_registered';
    }

    protected function getEntityType(): string
    {
        return 'user';
    }

    protected function getTitle(): string
    {
        return 'عميل جديد سجل';
    }

    protected function getShortMessage(): string
    {
        return "عميل جديد سجل: {$this->user->name}";
    }

    protected function getMessage(): string
    {
        return "في عميل جديد سجل في السيستم: \n".
               "الاسم: {$this->user->name} \n".
               "التليفون: {$this->user->phone} \n".
               'التاريخ: '.now()->format('Y-m-d H:i');
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-user-plus';
    }

    protected function getIconBg(): string
    {
        return 'bg-blue-600';
    }

    protected function getActionUrl(): string
    {
        return '/admin/users';
    }

    protected function getActionText(): string
    {
        return 'عرض المستخدمين';
    }

    protected function getCustomData(): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
        ];
    }

    public function getWhatsAppData(): array
    {
        return [
            'user_name' => $this->user->name,
            'user_phone' => $this->user->phone,
            'time' => now()->format('Y-m-d H:i'),
        ];
    }
}
