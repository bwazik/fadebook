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

    public function mount(): void
    {
        $this->dispatch('show-bottom-nav');
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    #[Computed]
    public function bookings()
    {
        /** @var User $user */
        $user = Auth::user();

        $query = $user->bookings()->with(['shop.images', 'service', 'barber'])->latest();

        $query = match ($this->tab) {
            'upcoming' => $query->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::InProgress]),
            'completed' => $query->where('status', BookingStatus::Completed),
            'cancelled' => $query->whereIn('status', [BookingStatus::Cancelled, BookingStatus::NoShow]),
            default => $query,
        };

        return $query->get();
    }

    public function render(): View
    {
        return view('livewire.booking.booking-list');
    }
}
