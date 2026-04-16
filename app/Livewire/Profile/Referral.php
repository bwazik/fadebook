<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\ReferralStatus;
use App\Enums\ShopStatus;
use App\Models\Referral as ReferralModel;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Referral extends Component
{
    public ?int $selectedReferralId = null;

    public ?int $selectedShopId = null;

    public function mount(): void
    {
        $this->dispatch('hide-bottom-nav');

        // Auto-select a shop if any are available
        $firstShop = $this->availableShops->first();
        if ($firstShop) {
            $this->selectedShopId = $firstShop->id;
        }
    }

    public function openReferral(int $id): void
    {
        $this->selectedReferralId = $id;
    }

    #[Computed]
    public function selectedReferral(): ?ReferralModel
    {
        if (! $this->selectedReferralId) {
            return null;
        }

        return $this->user->referralsGiven()
            ->with(['invitee', 'coupon'])
            ->find($this->selectedReferralId);
    }

    #[Computed]
    public function user(): User
    {
        return Auth::user();
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total_invites' => $this->user->referralsGiven()->count(),
            'successful_invites' => $this->user->referralsGiven()->where('status', ReferralStatus::Rewarded)->count(),
            'pending_invites' => $this->user->referralsGiven()->where('status', ReferralStatus::Pending)->count(),
        ];
    }

    #[Computed]
    public function availableShops()
    {
        return Shop::where('status', ShopStatus::Active)
            ->where('referral_enabled', true)
            ->get(['id', 'name']);
    }

    #[Computed]
    public function referralLink(): string
    {
        $params = ['ref' => $this->user->referral_code];
        if ($this->selectedShopId) {
            $params['shop'] = $this->selectedShopId;
        }

        return route('register', $params);
    }

    #[Computed]
    public function recentReferrals()
    {
        return $this->user->referralsGiven()
            ->with(['invitee', 'coupon'])
            ->latest()
            ->take(10)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.profile.referral');
    }
}
