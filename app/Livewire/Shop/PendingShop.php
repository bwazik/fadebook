<?php

namespace App\Livewire\Shop;

use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PendingShop extends Component
{
    public Shop $shop;

    public function mount(): void
    {
        $user = Auth::user();

        if ($user === null) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        if ($user->role !== UserRole::BarberOwner) {
            $this->redirectRoute($user->homeRouteName(), navigate: true);

            return;
        }

        $shop = $user->shops()->with('area')->latest('id')->first();

        if ($shop === null) {
            $this->redirectRoute('onboarding', navigate: true);

            return;
        }

        if ($shop->status === ShopStatus::Active) {
            $this->redirectRoute('owner.dashboard', navigate: true);

            return;
        }

        if ($shop->status === ShopStatus::Rejected) {
            $this->redirectRoute('onboarding', navigate: true);

            return;
        }

        $this->shop = $shop;
    }

    public function render()
    {
        return view('livewire.shop.pending-shop')
            ->layout('components.layouts.app', ['title' => 'طلبك تحت المراجعة']);
    }
}
