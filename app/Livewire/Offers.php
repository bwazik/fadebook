<?php

namespace App\Livewire;

use App\Models\Offer;
use App\Models\Shop;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Offers extends Component
{
    /** @var int|null ID of the offer card the user clicked to open the modal */
    public ?int $selectedOfferId = null;

    /** @var int|null ID of the shop card the user clicked to open the referral modal */
    public ?int $selectedShopId = null;

    public function openOffer(int $couponId): void
    {
        $this->selectedOfferId = $couponId;
    }

    public function openShopReferral(int $shopId): void
    {
        $this->selectedShopId = $shopId;
    }

    public function closeModals(): void
    {
        $this->selectedOfferId = null;
        $this->selectedShopId = null;
    }

    public function closeOffer(): void
    {
        $this->selectedOfferId = null;
    }

    public function closeShopReferral(): void
    {
        $this->selectedShopId = null;
    }

    public function render()
    {
        // 1. Unified Discovery Feed from the 'offers' table
        $promotionalOffers = Offer::with(['shop.area', 'coupon'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->whereHas('shop', fn ($q) => $q->where('status', 1))
            ->latest()
            ->get();

        // 2. Selected items for modals
        $selectedOffer = $this->selectedOfferId
            ? Offer::with(['shop.area', 'coupon'])->find($this->selectedOfferId)
            : null;

        $selectedShop = $this->selectedShopId
            ? Shop::find($this->selectedShopId)
            : null;

        return view('livewire.offers', [
            'promotionalOffers' => $promotionalOffers,
            'selectedOffer' => $selectedOffer,
            'selectedShop' => $selectedShop,
        ]);
    }
}
