<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\Barber;
use App\Models\Shop;
use App\Models\User;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ManageBarbers extends Component
{
    use WithFileUploads, WithRateLimiting, WithToast;

    public Shop $shop;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $phone = '';

    public array $unavailabilityDates = [];

    public array $daysOff = [];

    public ?string $newUnavailabilityDate = null;

    public string $password = 'fadebook123';

    public $avatar;

    public array $selectedServices = [];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->shop = $user->shop()->firstOrFail();
        $this->dispatch('show-bottom-nav');
    }

    #[Computed]
    public function availableServices()
    {
        return $this->shop->services;
    }

    #[Computed]
    public function barbers()
    {
        return $this->shop->barbers()->with(['services', 'images'])->latest()->get();
    }

    public function toggleActive(int $barberId): void
    {
        if ($this->isRateLimited('manage-barbers', 10, 60)) {
            return;
        }

        $barber = $this->shop->barbers()->findOrFail($barberId);
        $barber->update(['is_active' => ! $barber->is_active]);
        $this->toastSuccess($barber->is_active ? 'تم تفعيل الحلاق' : 'تم إيقاف الحلاق');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->avatar = null;
        $this->showForm = true;
    }

    public function edit(int $barberId): void
    {
        $barber = $this->shop->barbers()->with('services')->findOrFail($barberId);
        $this->editingId = $barber->id;
        $this->name = $barber->name;
        $this->phone = $barber->phone ?? '';
        $this->unavailabilityDates = $barber->unavailabilities()
            ->get()
            ->pluck('unavailable_date')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->toArray();
        $this->daysOff = $barber->days_off ?? [];
        $this->avatar = null;
        $this->selectedServices = $barber->services->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->showForm = true;
    }

    public function save(): void
    {
        if ($this->isRateLimited('manage-barbers', 10, 60)) {
            return;
        }

        try {
            $this->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20|unique:barbers,phone,'.($this->editingId ?? 'NULL').',id,shop_id,'.$this->shop->id.',deleted_at,NULL',
                'avatar' => 'nullable|image|max:2048',
                'selectedServices' => 'array',
            ], [
                'name.required' => 'يرجى إدخال اسم الحلاق',
                'phone.required' => 'يرجى إدخال رقم الهاتف',
                'phone.unique' => 'رقم الهاتف هذا مسجل بالفعل لحلاق آخر في محلك',
                'avatar.image' => 'يجب أن يكون الملف صورة',
                'avatar.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
            ]);
        } catch (ValidationException $e) {
            $this->toastError($e->validator->errors()->first());

            throw $e;
        }

        // 1. Find or Create User
        $user = User::where('phone', $this->phone)->first();

        if (! $user) {
            $user = User::create([
                'name' => $this->name,
                'phone' => $this->phone,
                'password' => $this->password, // Already hashed via cast in User model
                'role' => UserRole::Client, // Observer will upgrade to Barber
                'phone_verified_at' => now(), // Assume owner verifies it
            ]);
        }

        if ($this->editingId) {
            $barber = $this->shop->barbers()->findOrFail($this->editingId);
            $barber->update([
                'user_id' => $user->id,
                'name' => $this->name,
                'phone' => $this->phone,
                'days_off' => $this->daysOff,
            ]);

            // Sync Unavailability
            $barber->unavailabilities()->delete();
            foreach ($this->unavailabilityDates as $date) {
                $barber->unavailabilities()->create(['unavailable_date' => $date]);
            }

            $barber->services()->sync($this->selectedServices);

            if ($this->avatar) {
                // Wipe old disk files first using the new trait helper
                $barber->clearCollection('avatar');

                $path = $this->avatar->store('barbers/avatars', 'public');
                $barber->images()->create([
                    'path' => $path,
                    'collection' => 'avatar',
                ]);
            }

            $this->toastSuccess('تم تعديل بيانات الحلاق');
        } else {
            /** @var Barber $barber */
            $barber = $this->shop->barbers()->create([
                'user_id' => $user->id,
                'name' => $this->name,
                'phone' => $this->phone,
                'days_off' => $this->daysOff,
                'is_active' => true,
            ]);

            // Sync Unavailability
            foreach ($this->unavailabilityDates as $date) {
                $barber->unavailabilities()->create(['unavailable_date' => $date]);
            }

            $barber->services()->sync($this->selectedServices);

            if ($this->avatar) {
                $path = $this->avatar->store('barbers/avatars', 'public');
                $barber->images()->create([
                    'path' => $path,
                    'collection' => 'avatar',
                ]);
            }

            $this->toastSuccess('تم إضافة الحلاق بنجاح');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->phone = '';
        $this->password = 'fadebook123';
        $this->avatar = null;
        $this->selectedServices = [];
        $this->unavailabilityDates = [];
        $this->daysOff = [];
        $this->newUnavailabilityDate = null;
    }

    public function addUnavailabilityDate(): void
    {
        if (! $this->newUnavailabilityDate) {
            return;
        }

        if (in_array($this->newUnavailabilityDate, $this->unavailabilityDates)) {
            $this->toastError('التاريخ ده متضاف بالفعل');

            return;
        }

        $this->unavailabilityDates[] = $this->newUnavailabilityDate;
        $this->newUnavailabilityDate = null;
    }

    public function removeUnavailabilityDate(string $date): void
    {
        $this->unavailabilityDates = array_filter($this->unavailabilityDates, fn ($d) => $d !== $date);
    }

    public function deleteBarber(int $barberId): void
    {
        if ($this->isRateLimited('manage-barbers')) {
            return;
        }

        $barber = $this->shop->barbers()->findOrFail($barberId);

        // We could also delete the linked user if needed,
        // but for now we just delete the barber profile
        $barber->delete();

        $this->toastSuccess('تم حذف الحلاق بنجاح');
    }

    public function render(): View
    {
        return view('livewire.dashboard.manage-barbers');
    }
}
