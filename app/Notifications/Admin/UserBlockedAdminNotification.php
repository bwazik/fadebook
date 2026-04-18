<?php

namespace App\Notifications\Admin;

use App\Models\User;
use App\Notifications\Channels\WhatsAppChannel;
use App\Traits\NotificationDataStructure;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserBlockedAdminNotification extends Notification
{
    use NotificationDataStructure, Queueable;

    public function __construct(public User $user, public string $reason) {}

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
        return 'admin_user_blocked';
    }

    protected function getEntityType(): string
    {
        return 'user';
    }

    protected function getTitle(): string
    {
        return 'مستخدم تم حظره';
    }

    protected function getShortMessage(): string
    {
        return "تم حظر {$this->user->name}";
    }

    protected function getMessage(): string
    {
        return "تم حظر المستخدم: {$this->user->name} \n".
               "السبب: {$this->reason} \n".
               "الهاتف: {$this->user->phone}";
    }

    protected function getIcon(): string
    {
        return 'heroicon-o-lock-closed';
    }

    protected function getIconBg(): string
    {
        return 'bg-red-700';
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
            'reason' => $this->reason,
        ];
    }

    public function getWhatsAppData(): array
    {
        return [
            'user_name' => $this->user->name,
            'reason' => $this->reason,
            'user_phone' => $this->user->phone,
        ];
    }
}
