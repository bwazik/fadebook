<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Enums\ShopStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PendingApproval extends Component
{
    public function mount(): void
    {
        // Redirect if user doesn't have a pending shop
        $shop = Auth::user()?->shop;
        if (! $shop || $shop->status !== ShopStatus::Pending) {
            redirect()->route('home');
        }
    }

    public function render()
    {
        $shop = Auth::user()->shop;

        return view('livewire.onboarding.pending-approval', [
            'shop' => $shop,
        ]);
    }
}
