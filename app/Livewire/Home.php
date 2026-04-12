<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\BookingStatus;
use App\Models\Area;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Home extends Component
{
    public ?int $selectedArea = null;

    public function mount(): void
    {
        $this->dispatch('show-bottom-nav');
    }

    public int $perPage = 6;

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    public string $sortBy = 'rating';

    public function updatedSortBy(): void
    {
        $this->perPage = 6;
    }

    public function filterByArea(?int $areaId): void
    {
        $this->selectedArea = $areaId;
        $this->perPage = 6;
    }

    public function sortShops(string $sortBy): void
    {
        if (in_array($sortBy, ['rating', 'newest'])) {
            $this->sortBy = $sortBy;
        }
        $this->perPage = 6;
    }

    #[Computed]
    public function upcomingBookingsCount(): int
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return 0;
        }

        return $user->bookings()
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
            ->count();
    }

    #[Computed]
    public function areas()
    {
        return Area::active()->get();
    }

    #[Computed]
    public function shops()
    {
        $query = Shop::active()->with([
            'area',
            'images',
            'barbers' => fn ($q) => $q->active()->with('images'),
        ]);

        if ($this->selectedArea) {
            $query->where('area_id', $this->selectedArea);
        }

        if ($this->sortBy === 'rating') {
            $query->orderByDesc('average_rating')->orderByDesc('total_reviews');
        } else {
            $query->latest();
        }

        return $query->limit($this->perPage)->get();
    }

    #[Computed]
    public function hasMore(): bool
    {
        $query = Shop::active();

        if ($this->selectedArea) {
            $query->where('area_id', $this->selectedArea);
        }

        return $query->count() > $this->perPage;
    }

    #[Layout('components.layout.app')]
    public function render(): View
    {
        return view('livewire.home', [
            'areas' => $this->areas,
            'shops' => $this->shops,
        ]);
    }
}
