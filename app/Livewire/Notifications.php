<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Notifications extends Component
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
