<?php

declare(strict_types=1);

namespace App\Livewire\Booking;

use App\Enums\BarberSelectionMode;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Shop;
use App\Services\BookingService;
use App\Services\CouponService;
use App\Services\SlotCalculatorService;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CreateBooking extends Component
{
    use WithRateLimiting, WithToast;

    public int $step = 1;

    public string $shopSlug;

    public Shop $shop;

    public ?int $selectedServiceId = null;

    public ?int $selectedBarberId = null;

    public ?string $selectedDate = null;

    public ?string $selectedSlot = null;

    public ?int $selectedCategory = null;

    public string $couponCode = '';

    public bool $policyAccepted = false;

    public array $availableSlots = [];

    public float $discountAmount = 0;

    public float $finalAmount = 0;

    public ?int $selectedCouponId = null;

    public bool $showTermsModal = false;

    public function mount(string $shopSlug, ?int $serviceId = null): void
    {
        $this->shopSlug = $shopSlug;
        $this->shop = Shop::where('slug', $shopSlug)
            ->with([
                'serviceCategories',
                'services' => fn ($q) => $q->with('category'),
                'barbers' => fn ($q) => $q->with('services')->active(),
            ])
            ->firstOrFail();

        if (! $this->shop->is_online) {
            $this->flashToastError(__('messages.toast_shop_unavailable'));

            $this->redirectRoute('shop.show', ['areaSlug' => $this->shop->area->slug, 'shopSlug' => $this->shop->slug], navigate: true);

            return;
        }

        $draft = session()->get("booking_draft_{$this->shopSlug}");

        if ($serviceId && $this->shop->services->contains('id', $serviceId)) {
            // If the user clicked a specific service or refreshed a URL with a serviceId
            if ($draft && ($draft['selectedServiceId'] ?? null) == $serviceId) {
                // It's the same service as the draft, restore full progress
                $this->restoreFromDraft($draft);
            } else {
                // New service or no draft, start from the beginning for this service
                $this->selectedServiceId = $serviceId;
                $this->step = $this->shop->barber_selection_mode === BarberSelectionMode::AnyAvailable ? 3 : 2;
                $this->saveDraft();
            }
        } elseif ($draft) {
            // No specific service ID provided, restore whatever draft we found
            $this->restoreFromDraft($draft);
        }

        $this->dispatch('hide-bottom-nav');
    }

    private function restoreFromDraft(array $draft): void
    {
        $this->step = $draft['step'] ?? 1;
        $this->selectedServiceId = $draft['selectedServiceId'] ?? null;
        $this->selectedBarberId = $draft['selectedBarberId'] ?? null;
        $this->selectedDate = $draft['selectedDate'] ?? null;
        $this->selectedSlot = $draft['selectedSlot'] ?? null;
        $this->couponCode = $draft['couponCode'] ?? '';
        $this->discountAmount = (float) ($draft['discountAmount'] ?? 0);
        $this->finalAmount = (float) ($draft['finalAmount'] ?? 0);
        $this->selectedCouponId = $draft['selectedCouponId'] ?? null;

        if ($this->selectedDate && $this->selectedServiceId) {
            $this->loadSlots();
        }
    }

    public function updated($property): void
    {
        if (empty($property) || $property === '$') {
            return;
        }

        $this->saveDraft();
    }

    private function saveDraft(): void
    {
        session()->put("booking_draft_{$this->shopSlug}", [
            'step' => $this->step,
            'selectedServiceId' => $this->selectedServiceId,
            'selectedBarberId' => $this->selectedBarberId,
            'selectedDate' => $this->selectedDate,
            'selectedSlot' => $this->selectedSlot,
            'couponCode' => $this->couponCode,
            'discountAmount' => $this->discountAmount,
            'finalAmount' => $this->finalAmount,
            'selectedCouponId' => $this->selectedCouponId,
        ]);
    }

    public function selectService(int $serviceId): void
    {
        $service = $this->shop->services->firstWhere('id', $serviceId);

        if (! $service || ! $service->is_active) {
            $this->toastError(__('messages.toast_service_unavailable'));

            return;
        }

        $this->selectedServiceId = $serviceId;

        if ($this->shop->barber_selection_mode === BarberSelectionMode::AnyAvailable) {
            $this->step = 3;
        } else {
            $this->step = 2;
        }

        $this->saveDraft();
    }

    public function selectBarber(int $barberId): void
    {
        $this->selectedBarberId = $barberId;
        $this->step = 3;
        $this->saveDraft();
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->selectedSlot = null;
        $this->loadSlots();
        $this->saveDraft();
    }

    public function selectSlot(string $slot): void
    {
        $this->selectedSlot = $slot;
        $this->step = 4;
        $this->calculateTotals();
        $this->saveDraft();
    }

    public function applyCoupon(CouponService $couponService): void
    {
        if ($this->isRateLimited('apply-coupon', 5, 60)) {
            return;
        }

        if (empty($this->couponCode)) {
            return;
        }

        try {
            $service = Service::find($this->selectedServiceId);
            $result = $couponService->validateAndCalculate(
                $this->couponCode,
                $this->shop,
                $service,
                Auth::user()
            );

            $this->discountAmount = (float) $result['discount_amount'];
            $this->finalAmount = (float) $result['final_amount'];
            $this->selectedCouponId = $result['coupon']->id;

            $this->toastSuccess(__('messages.booking_coupon_applied'));
            $this->saveDraft();
        } catch (\Exception $e) {
            $this->discountAmount = 0;
            $this->calculateTotals();
            $this->toastError($e->getMessage());
        }
    }

    private function calculateTotals(): void
    {
        $service = Service::find($this->selectedServiceId);
        $this->finalAmount = (float) ($service?->price ?? 0);
        $this->discountAmount = 0;
        $this->selectedCouponId = null;
    }

    public function toggleTermsModal(): void
    {
        $this->showTermsModal = ! $this->showTermsModal;
    }

    private function loadSlots(): void
    {
        $service = Service::find($this->selectedServiceId);
        $barber = $this->selectedBarberId ? Barber::find($this->selectedBarberId) : null;

        if (! $service || ! $this->selectedDate) {
            return;
        }

        $calculator = app(SlotCalculatorService::class);
        $this->availableSlots = $calculator->getAvailableSlots($this->shop, $barber, $service, $this->selectedDate);
    }

    #[Computed]
    public function groupedSlots(): array
    {
        $grouped = [
            'morning' => [],
            'afternoon' => [],
            'evening' => [],
        ];

        foreach ($this->availableSlots as $slot) {
            $hour = (int) explode(':', $slot)[0];

            if ($hour < 12) {
                $grouped['morning'][] = $slot;
            } elseif ($hour < 17) {
                $grouped['afternoon'][] = $slot;
            } else {
                $grouped['evening'][] = $slot;
            }
        }

        return $grouped;
    }

    #[Computed]
    public function availableBarbers()
    {
        if (! $this->selectedServiceId) {
            return collect();
        }

        return $this->shop->barbers
            ->where('is_active', true)
            ->filter(function ($barber) {
                return $barber->services->contains('id', $this->selectedServiceId);
            });
    }

    public function goBack(): void
    {
        if ($this->step > 1) {
            if ($this->step === 3 && $this->shop->barber_selection_mode === BarberSelectionMode::AnyAvailable) {
                $this->step = 1;
            } else {
                $this->step--;
            }
            $this->saveDraft();
        } else {
            $this->redirectRoute('shop.show', ['areaSlug' => $this->shop->area->slug, 'shopSlug' => $this->shop->slug], navigate: true);
        }
    }

    public function confirmBooking(BookingService $bookingService): void
    {
        if ($this->isRateLimited('confirm-booking', 3, 60)) {
            return;
        }

        if (! $this->policyAccepted) {
            $this->toastError(__('messages.booking_policy_error'));

            return;
        }

        if (! $this->selectedServiceId || ! $this->selectedDate || ! $this->selectedSlot) {
            $this->toastError(__('messages.booking_data_incomplete'));

            return;
        }

        try {
            $booking = $bookingService->initiate(
                Auth::user(),
                $this->shop,
                [
                    'service_id' => $this->selectedServiceId,
                    'barber_id' => $this->selectedBarberId,
                    'date' => $this->selectedDate,
                    'time' => $this->selectedSlot,
                    'coupon_code' => $this->couponCode,
                ]
            );

            session()->forget("booking_draft_{$this->shopSlug}");
            $this->flashToastSuccess(__('messages.booking_request_success'));

            $this->redirectRoute('booking.show', ['bookingUuid' => $booking->uuid], navigate: true);

        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function filterByServiceCategory(?int $categoryId): void
    {
        $this->selectedCategory = $categoryId;
        $this->saveDraft();
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
    public function totalBeforeDiscount(): float
    {
        $service = $this->shop->services->firstWhere('id', $this->selectedServiceId);

        return (float) ($service?->price ?? 0);
    }

    #[Computed]
    public function filteredServices()
    {
        $services = $this->shop->services;

        if ($this->selectedCategory) {
            $services = $services->where('service_category_id', $this->selectedCategory);
        }

        return $services->sortBy('sort_order')->values();
    }

    public function render(): View
    {
        return view('livewire.booking.create-booking');
    }
}
