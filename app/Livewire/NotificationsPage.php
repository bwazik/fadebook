<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class NotificationsPage extends Component
{
    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();
    }

    /**
     * Handle notification click: mark as read and redirect if URL exists.
     */
    public function handleNotificationClick(string $id): mixed
    {
        /** @var User $user */
        $user = Auth::user();

        $notification = $user->notifications()->findOrFail($id);

        if (! $notification->read_at) {
            $notification->markAsRead();
        }

        // Support both our standard and Filament/Laravel standard keys
        $url = $notification->data['action_url']
            ?? $notification->data['url']
            ?? null;

        if ($url) {
            return $this->redirect($url, navigate: true);
        }

        return null;
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(string $id): void
    {
        /** @var User $user */
        $user = Auth::user();

        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
    }

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('livewire.notifications', [
            'notifications' => $user->notifications()->latest()->limit(50)->get(),
        ]);
    }
}
