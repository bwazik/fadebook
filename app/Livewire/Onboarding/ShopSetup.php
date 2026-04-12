<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Enums\ShopStatus;
use App\Models\Area;
use App\Models\Shop;
use App\Rules\EgyptianPhone;
use App\Traits\WithToast;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ShopSetup extends Component
{
    use WithToast;

    public int $step = 1;

    // Step 1: Basic Info
    public string $shopName = '';

    public string $phone = '';

    public int $areaId = 0;

    public string $address = '';

    public string $description = '';

    // Step 2: Hours
    public array $openingHours = [
        'saturday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'sunday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'monday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'tuesday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'wednesday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'thursday' => ['is_open' => true, 'open' => '09:00', 'close' => '21:00'],
        'friday' => ['is_open' => true, 'open' => '14:00', 'close' => '23:00'],
    ];

    public function mount(): void
    {
        // If user already has a shop, redirect to pending approval
        if (Auth::user()?->shop) {
            redirect()->route('onboarding.pending');
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validateStep1();
            $this->step = 2;

            return;
        }
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    protected function validateStep1(): void
    {
        $this->validate([
            'shopName' => 'required|string|max:255',
            'phone' => ['required', 'string', 'unique:shops,phone', new EgyptianPhone],
            'areaId' => 'required|integer|exists:areas,id',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);
    }

    public function submit(): void
    {
        $this->validateStep1();

        // Create the shop
        $shop = Shop::create([
            'owner_id' => Auth::id(),
            'name' => $this->shopName,
            'phone' => $this->phone,
            'area_id' => $this->areaId,
            'address' => $this->address,
            'description' => $this->description,
            'status' => ShopStatus::Pending,
            'is_online' => true,
            'advance_booking_days' => 30,
            'opening_hours' => $this->openingHours,
        ]);

        $this->toastSuccess('تم إنشاء المحل بنجاح. يرجى الانتظار لتأكيد البيانات');

        redirect()->route('onboarding.pending');
    }

    public function render()
    {
        return view('livewire.onboarding.shop-setup', [
            'areas' => Area::all(),
        ]);
    }
}
