<?php

declare(strict_types=1);

namespace App\Livewire\Shop;

use App\Jobs\TrackShopView;
use App\Models\Shop;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ShopPage extends Component
{
    use WithToast;

    public Shop $shop;

    public string $reviewSort = 'newest';

    public ?int $selectedCategory = null;

    public array $openingHours = [];

    public function mount(string $areaSlug, string $shopSlug): void
    {
        $this->shop = Shop::with([
            'area',
            'serviceCategories',
            'services' => fn ($q) => $q->with('category')->orderBy('sort_order', 'asc'),
            'barbers' => fn ($q) => $q->with('services')->active(),
            'images',
        ])
            ->where('slug', $shopSlug)
            ->whereHas('area', fn ($q) => $q->where('slug', $areaSlug))
            ->firstOrFail();

        $this->openingHours = is_array($this->shop->opening_hours)
            ? $this->shop->opening_hours
            : json_decode($this->shop->opening_hours ?? '[]', true);

        // Track view (async via background job)
        TrackShopView::dispatch(
            $this->shop,
            Auth::id(),
            request()->ip() ?? '127.0.0.1',
            request()->userAgent()
        );

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
            $period = date('a', $timestamp) === 'am' ? __('messages.time_am') : __('messages.time_pm');

            return "{$hour}:{$minute} {$period}";
        };

        return $formatTime($open).' - '.$formatTime($close);
    }

    public function filterByServiceCategory(?int $categoryId): void
    {
        $this->selectedCategory = $categoryId;
    }

    public function showServiceBlockedToast(bool $shopOnline, bool $serviceActive): void
    {
        if (! $shopOnline) {
            $this->toastError(__('messages.toast_shop_unavailable'));
        } elseif (! $serviceActive) {
            $this->toastError(__('messages.toast_service_unavailable'));
        }
    }

    #[Computed]
    public function filteredServices(): Collection
    {
        $services = $this->shop->services;

        if ($this->selectedCategory) {
            $services = $services->where('service_category_id', $this->selectedCategory);
        }

        return $services->sortBy('sort_order')->values();
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

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.shop.shop-page');
    }
}
