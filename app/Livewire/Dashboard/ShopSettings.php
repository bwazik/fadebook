<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Enums\BarberSelectionMode;
use App\Enums\PaymentMode;
use App\Models\Area;
use App\Models\Shop;
use App\Models\User;
use App\Rules\EgyptianPhone;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ShopSettings extends Component
{
    use WithFileUploads, WithRateLimiting, WithToast;

    public Shop $shop;

    // Basic Info
    public string $name = '';

    public string $phone = '';

    public string $description = '';

    public string $address = '';

    public ?int $area_id = null;

    // Booking Settings
    public int $advance_booking_days = 30;

    public int $barber_selection_mode = 1;

    public int $payment_mode = 0;

    public float $deposit_percentage = 0;

    public bool $is_online = false;

    // Opening Hours
    public array $opening_hours = [];

    // Images
    public $logo;

    public $banner;

    public $newGalleryImages = [];

    public array $gallery = []; // To track existing gallery images for deletion

    public array $existingPaymentMethods = [];

    public int $newMethodType = 1;

    public string $newMethodPhone = '';

    public string $newMethodAccount = '';

    public string $newMethodLink = '';

    public ?int $editingMethodId = null;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->shop = $user->shop()->firstOrFail();
        $this->name = $this->shop->name;
        $this->phone = $this->shop->phone;
        $this->description = $this->shop->description ?? '';
        $this->address = $this->shop->address;
        $this->area_id = $this->shop->area_id;

        $this->advance_booking_days = $this->shop->advance_booking_days;
        $this->barber_selection_mode = $this->shop->barber_selection_mode->value ?? 1;
        $this->payment_mode = $this->shop->payment_mode->value ?? 0;
        $this->deposit_percentage = (float) ($this->shop->deposit_percentage ?? 0);
        $this->is_online = (bool) $this->shop->is_online;

        $defaultHours = [
            'saturday' => ['open' => '09:00', 'close' => '21:00', 'is_open' => true],
            'sunday' => ['open' => '09:00', 'close' => '21:00', 'is_open' => true],
            'monday' => ['open' => '09:00', 'close' => '21:00', 'is_open' => true],
            'tuesday' => ['open' => '09:00', 'close' => '21:00', 'is_open' => true],
            'wednesday' => ['open' => '09:00', 'close' => '21:00', 'is_open' => true],
            'thursday' => ['open' => '09:00', 'close' => '21:00', 'is_open' => true],
            'friday' => ['open' => '14:00', 'close' => '23:00', 'is_open' => true],
        ];

        $shopHours = is_string($this->shop->opening_hours) ? json_decode($this->shop->opening_hours, true) : $this->shop->opening_hours;

        foreach (['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {
            $this->opening_hours[$day] = [
                'open' => $shopHours[$day]['open'] ?? $defaultHours[$day]['open'],
                'close' => $shopHours[$day]['close'] ?? $defaultHours[$day]['close'],
                'is_open' => isset($shopHours[$day]['open']) && isset($shopHours[$day]['close']),
            ];
        }

        // Initialize gallery from DB
        $this->gallery = $this->shop->getImages('gallery')
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(fn ($img) => [
                'id' => $img->id,
                'url' => Storage::url($img->path),
            ])->toArray();

        $this->dispatch('show-bottom-nav');

        $this->loadPaymentMethods();
    }

    public function loadPaymentMethods(): void
    {
        $this->existingPaymentMethods = $this->shop->paymentMethods()
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
    }

    public function savePaymentMethod(): void
    {
        if ($this->isRateLimited('shop-settings-payment', 10, 60)) {
            return;
        }

        try {
            $this->validate([
                'newMethodPhone' => ['required', 'string', new EgyptianPhone],
                'newMethodAccount' => 'nullable|string|max:255',
                'newMethodLink' => 'nullable|url|max:255',
            ]);
        } catch (ValidationException $e) {
            $this->toastError(collect($e->errors())->flatten()->first() ?? 'في حاجة غلط في البيانات، راجعها تاني');
            throw $e;
        }

        if ($this->editingMethodId) {
            $method = $this->shop->paymentMethods()->find($this->editingMethodId);
            if ($method) {
                $method->update([
                    'type' => $this->newMethodType,
                    'phone_number' => $this->newMethodPhone,
                    'account_name' => $this->newMethodAccount,
                    'pay_link' => $this->newMethodLink,
                ]);
                $this->toastSuccess('تم تحديث وسيلة الدفع');
            }
        } else {
            $this->shop->paymentMethods()->create([
                'type' => $this->newMethodType,
                'phone_number' => $this->newMethodPhone,
                'account_name' => $this->newMethodAccount,
                'pay_link' => $this->newMethodLink,
                'is_active' => true,
            ]);
            $this->toastSuccess('تم إضافة وسيلة الدفع بنجاح');
        }

        $this->resetPaymentForm();
        $this->loadPaymentMethods();
    }

    public function editPaymentMethod(int $id): void
    {
        $method = $this->shop->paymentMethods()->find($id);
        if ($method) {
            $this->editingMethodId = $id;
            $this->newMethodType = (int) $method->type->value;
            $this->newMethodPhone = $method->phone_number;
            $this->newMethodAccount = $method->account_name ?? '';
            $this->newMethodLink = $method->pay_link ?? '';
            $this->dispatch('scroll-to-payment-form');
        }
    }

    public function resetPaymentForm(): void
    {
        $this->editingMethodId = null;
        $this->newMethodPhone = '';
        $this->newMethodAccount = '';
        $this->newMethodLink = '';
        $this->newMethodType = 1;
    }

    public function togglePaymentMethod(int $id): void
    {
        if ($this->isRateLimited('shop-settings', 10, 60)) {
            return;
        }

        $method = $this->shop->paymentMethods()->find($id);
        if ($method) {
            $method->update(['is_active' => ! $method->is_active]);
            $this->loadPaymentMethods();
        }
    }

    public function deletePaymentMethod(int $id): void
    {
        if ($this->isRateLimited('shop-settings', 10, 60)) {
            return;
        }

        $method = $this->shop->paymentMethods()->find($id);
        if ($method) {
            $method->delete();
            $this->loadPaymentMethods();
            $this->toastSuccess('تم مسح وسيلة الدفع');
        }
    }

    public function deleteGalleryImage(int $imageId): void
    {
        if ($this->isRateLimited('shop-settings', 5, 60)) {
            return;
        }

        $image = $this->shop->images()->find($imageId);
        if ($image) {
            $image->delete();
            $this->gallery = array_values(array_filter($this->gallery, fn ($img) => $img['id'] !== $imageId));
            $this->toastSuccess('تم مسح الصورة بنجاح');
        }
    }

    public function save(): void
    {
        if ($this->isRateLimited('shop-settings', 5, 60)) {
            return;
        }

        try {
            $this->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'description' => 'nullable|string',
                'address' => 'required|string',
                'area_id' => 'required|exists:areas,id',
                'advance_booking_days' => 'required|integer|min:1|max:90',
                'barber_selection_mode' => 'required|in:1,2',
                'payment_mode' => 'required|in:0,1,2',
                'deposit_percentage' => 'required|numeric|min:0|max:100',
                'logo' => 'nullable|image|max:3072',
                'banner' => 'nullable|image|max:3072',
                'newGalleryImages.*' => 'image|max:3072',
            ]);
        } catch (ValidationException $e) {
            $this->toastError(collect($e->errors())->flatten()->first() ?? 'في حاجة غلط في البيانات، راجعها تاني');
            throw $e;
        }

        $formattedHours = [];
        foreach ($this->opening_hours as $day => $data) {
            if ($data['is_open']) {
                $formattedHours[$day] = [
                    'open' => $data['open'],
                    'close' => $data['close'],
                ];
            }
        }

        $this->shop->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'description' => $this->description,
            'address' => $this->address,
            'area_id' => $this->area_id,
            'advance_booking_days' => $this->advance_booking_days,
            'barber_selection_mode' => BarberSelectionMode::from((int) $this->barber_selection_mode),
            'payment_mode' => PaymentMode::from((int) $this->payment_mode),
            'deposit_percentage' => $this->payment_mode == 1 ? $this->deposit_percentage : 0,
            'is_online' => $this->is_online,
            'opening_hours' => $formattedHours,
        ]);

        // Handle Logo
        if ($this->logo) {
            $this->shop->clearCollection('logo');
            $path = $this->logo->store('shops/logos', 'public');
            $this->shop->images()->create([
                'path' => $path,
                'disk' => 'public',
                'collection' => 'logo',
            ]);
            $this->logo = null;
        }

        // Handle Banner
        if ($this->banner) {
            $this->shop->clearCollection('banner');
            $path = $this->banner->store('shops/banners', 'public');
            $this->shop->images()->create([
                'path' => $path,
                'disk' => 'public',
                'collection' => 'banner',
            ]);
            $this->banner = null;
        }

        // Handle Gallery
        if (! empty($this->newGalleryImages)) {
            foreach ($this->newGalleryImages as $image) {
                $path = $image->store('shops/gallery', 'public');
                $maxOrder = $this->shop->getImages('gallery')->max('sort_order') ?? -1;
                $this->shop->images()->create([
                    'path' => $path,
                    'disk' => 'public',
                    'collection' => 'gallery',
                    'sort_order' => $maxOrder + 1,
                ]);
            }
            $this->newGalleryImages = [];
            // Refresh gallery array
            $this->gallery = $this->shop->getImages('gallery')
                ->orderBy('sort_order', 'asc')
                ->get()
                ->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => Storage::url($img->path),
                ])->toArray();
        }

        $this->toastSuccess('تم حفظ الإعدادات بنجاح');
    }

    public function render(): View
    {
        return view('livewire.dashboard.shop-settings', [
            'areas' => Area::all(),
            'days' => ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
        ]);
    }
}
