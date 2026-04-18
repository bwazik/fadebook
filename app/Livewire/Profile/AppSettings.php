<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\User;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AppSettings extends Component
{
    use WithToast;

    public bool $whatsappNotifications;

    public function mount(): void
    {
        $this->whatsappNotifications = (bool) Auth::user()->whatsapp_notifications;
        $this->dispatch('hide-bottom-nav');
    }

    public function toggleWhatsApp(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $user->update(['whatsapp_notifications' => $this->whatsappNotifications]);

        $message = $this->whatsappNotifications
            ? __('messages.profile_whatsapp_alerts_enabled')
            : __('messages.profile_whatsapp_alerts_disabled');

        $this->toastSuccess($message);
    }

    public function render(): View
    {
        return view('livewire.profile.app-settings');
    }
}
