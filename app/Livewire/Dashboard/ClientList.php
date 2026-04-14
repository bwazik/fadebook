<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Enums\BookingStatus;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ClientList extends Component
{
    public string $search = '';

    public function mount(): void
    {
        $this->dispatch('show-bottom-nav');
    }

    #[Computed]
    public function clients()
    {
        $shopId = Auth::user()->shop->id;

        $query = User::whereHas('bookings', function ($q) use ($shopId) {
            $q->where('shop_id', $shopId);
        })->withCount(['bookings as total_visits' => function ($q) use ($shopId) {
            $q->where('shop_id', $shopId)->where('status', BookingStatus::Completed);
        }]);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            });
        }

        return $query->orderByDesc('total_visits')->get();
    }

    public function render(): View
    {
        return view('livewire.dashboard.client-list');
    }
}
