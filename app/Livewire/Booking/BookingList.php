<?php

declare(strict_types=1);

namespace App\Livewire\Booking;

use App\Enums\BookingStatus;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BookingList extends Component
{
    public string $tab = 'upcoming'; // upcoming, completed, cancelled

    public int $perPage = 6;

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    public function mount(): void
    {
        $this->dispatch('show-bottom-nav');
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->perPage = 6;
    }

    #[Computed]
    public function bookings()
    {
        $query = $this->getBaseQuery();
        return $query->limit($this->perPage)->get();
    }

    #[Computed]
    public function hasMore(): bool
    {
        $query = $this->getBaseQuery();
        return $query->count() > $this->perPage;
    }

    private function getBaseQuery()
    {
        /** @var User $user */
        $user = Auth::user();

        $query = $user->bookings()->with(['shop.images', 'service', 'barber'])->latest();

        return match ($this->tab) {
            'upcoming' => $query->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::InProgress]),
            'completed' => $query->where('status', BookingStatus::Completed),
            'cancelled' => $query->whereIn('status', [BookingStatus::Cancelled, BookingStatus::NoShow]),
            default => $query,
        };
    }

    public function render(): View
    {
        return view('livewire.booking.booking-list');
    }
}
