<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Models\Barber;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ManageReviews extends Component
{
    public int $perPage = 10;

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public Shop $shop;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->shop = $user->shop()->firstOrFail();

        $this->dispatch('show-bottom-nav');
    }

    #[Computed]
    public function reviews()
    {
        return Review::where('reviewable_type', Shop::class)
            ->where('reviewable_id', $this->shop->id)
            ->with(['user', 'booking.barber', 'booking.reviews'])
            ->latest()
            ->limit($this->perPage)
            ->get();
    }

    #[Computed]
    public function hasMore(): bool
    {
        return Review::where('reviewable_type', Shop::class)
            ->where('reviewable_id', $this->shop->id)
            ->count() > $this->perPage;
    }

    #[Computed]
    public function stats()
    {
        return [
            'average_rating' => (float) $this->shop->average_rating,
            'total_reviews' => $this->shop->total_reviews,
        ];
    }

    public function getBarberRating(Review $review): ?float
    {
        $barberReview = $review->booking?->reviews
            ->firstWhere('reviewable_type', Barber::class);

        return $barberReview ? (float) $barberReview->rating : null;
    }

    public function render(): View
    {
        return view('livewire.dashboard.manage-reviews');
    }
}
