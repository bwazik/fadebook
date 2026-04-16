<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\ReferralStatus;
use App\Models\Referral as ReferralModel;
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

    public function mount(): void
    {
        $this->dispatch('hide-bottom-nav');
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
    public function referralLink(): string
    {
        return route('register', ['ref' => $this->user->referral_code]);
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
