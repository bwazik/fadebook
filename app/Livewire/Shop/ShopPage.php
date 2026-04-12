<?php

declare(strict_types=1);

namespace App\Livewire\Shop;

use App\Jobs\IncrementShopView;
use App\Models\Shop;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ShopPage extends Component
{
    public Shop $shop;

    public string $reviewSort = 'newest';

    public array $openingHours = [];

    public function mount(string $areaSlug, string $shopSlug): void
    {
        $this->shop = Shop::with([
            'area',
            'services' => fn ($q) => $q->orderBy('sort_order', 'asc')->orderBy('name', 'asc'),
            'barbers', // loading all barbers, we will display active ones, or just let scope do it if defined
            'images',
        ])
            ->where('slug', $shopSlug)
            ->whereHas('area', fn ($q) => $q->where('slug', $areaSlug))
            ->firstOrFail();

        $this->openingHours = is_array($this->shop->opening_hours)
            ? $this->shop->opening_hours
            : json_decode($this->shop->opening_hours ?? '[]', true);

        // Dispatch job to increment view (async)
        IncrementShopView::dispatch($this->shop, request()->ip() ?? '127.0.0.1');

        $this->dispatch('hide-bottom-nav');
    }

    public function getFormattedHours(string $day): string
    {
        $timeData = $this->openingHours[$day] ?? null;

        if (! $timeData || ! is_array($timeData)) {
            return __('messages.closed');
        }

        $open = $timeData['open'] ?? null;
        $close = $timeData['close'] ?? null;

        if (! $open || ! $close) {
            return __('messages.closed');
        }

        $formatTime = function ($time) {
            $timestamp = strtotime($time);
            $hour = date('g', $timestamp);
            $minute = date('i', $timestamp);
            $period = date('a', $timestamp) === 'am' ? 'ص' : 'م';

            return "{$hour}:{$minute} {$period}";
        };

        return $formatTime($open).' - '.$formatTime($close);
    }

    public int $reviewsPerPage = 2;

    public function loadMoreReviews(): void
    {
        $this->reviewsPerPage += 2;
    }

    #[Computed]
    public function sortedReviews()
    {
        $query = $this->shop->reviews()->with('user');

        $query = match ($this->reviewSort) {
            'highest' => $query->orderByDesc('rating')->orderByDesc('created_at'),
            'lowest' => $query->orderBy('rating')->orderByDesc('created_at'),
            default => $query->latest(),
        };

        return $query->limit($this->reviewsPerPage)->get();
    }

    #[Computed]
    public function hasMoreReviews(): bool
    {
        return $this->shop->reviews()->count() > $this->reviewsPerPage;
    }

    #[Computed]
    public function startingPrice(): int
    {
        return (int) $this->shop->services->min('price') ?? 0;
    }

    #[Computed]
    public function galleryImages(): Collection
    {
        return $this->shop->images->where('collection', 'gallery');
    }

    #[Layout('components.layout.app')]
    public function render(): View
    {
        return view('livewire.shop.shop-page');
    }
}
