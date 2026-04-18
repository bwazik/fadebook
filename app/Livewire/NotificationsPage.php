<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
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

        // Support our standard, Laravel standard, and Filament actions
        $url = $notification->data['action_url']
            ?? $notification->data['url']
            ?? ($notification->data['actions'][0]['url'] ?? null)
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

    public int $perPage = 10;

    /**
     * Load more notifications.
     */
    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    /**
     * Check if there are more notifications to load.
     */
    #[Computed]
    public function hasMore(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->notifications()->count() > $this->perPage;
    }

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('livewire.notifications', [
            'notifications' => $user->notifications()->latest()->limit($this->perPage)->get(),
        ]);
    }
}
