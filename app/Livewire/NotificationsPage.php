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
     * Get the base query for client notifications.
     */
    private function clientNotificationsQuery()
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->notifications()->clientOnly();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        $this->clientNotificationsQuery()->whereNull('read_at')->update(['read_at' => now()]);
    }

    /**
     * Handle notification click: mark as read and redirect if URL exists.
     */
    public function handleNotificationClick(string $id): mixed
    {
        $notification = $this->clientNotificationsQuery()->findOrFail($id);

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
        $notification = $this->clientNotificationsQuery()->findOrFail($id);
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
        return $this->clientNotificationsQuery()->count() > $this->perPage;
    }

    public function render(): View
    {
        return view('livewire.notifications', [
            'notifications' => $this->clientNotificationsQuery()->latest()->limit($this->perPage)->get(),
        ]);
    }
}
