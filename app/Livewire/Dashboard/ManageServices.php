<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Models\Shop;
use App\Models\User;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ManageServices extends Component
{
    use WithRateLimiting, WithToast;

    public Shop $shop;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $price = '';

    public string $duration_minutes = '30';

    public string $description = '';

    public ?int $service_category_id = null;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->shop = $user->shop()->firstOrFail();
        $this->dispatch('show-bottom-nav');
    }

    #[Computed]
    public function groupedServices()
    {
        return $this->shop->services()
            ->with('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy(fn ($s) => $s->category?->name ?? 'أخرى');
    }

    #[Computed]
    public function categories()
    {
        return $this->shop->serviceCategories()->orderBy('sort_order')->get();
    }

    public function toggleActive(int $serviceId): void
    {
        if ($this->isRateLimited('manage-services', 10, 60)) {
            return;
        }

        $service = $this->shop->services()->findOrFail($serviceId);
        $service->update(['is_active' => ! $service->is_active]);
        $this->toastSuccess($service->is_active ? 'تم تفعيل الخدمة' : 'تم إيقاف الخدمة');
    }

    public function deleteService(int $serviceId): void
    {
        if ($this->isRateLimited('manage-services', 10, 60)) {
            return;
        }

        $service = $this->shop->services()->findOrFail($serviceId);
        $service->delete();
        $this->toastSuccess('تم حذف الخدمة بنجاح');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $serviceId): void
    {
        $service = $this->shop->services()->findOrFail($serviceId);
        $this->editingId = $service->id;
        $this->name = $service->name;
        $this->price = (string) $service->price;
        $this->duration_minutes = (string) $service->duration_minutes;
        $this->description = $service->description ?? '';
        $this->service_category_id = $service->service_category_id;
        $this->showForm = true;
    }

    public function save(): void
    {
        if ($this->isRateLimited('manage-services', 10, 60)) {
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5',
            'description' => 'nullable|string|max:1000',
            'service_category_id' => 'nullable|exists:service_categories,id',
        ]);

        if ($this->editingId) {
            $service = $this->shop->services()->findOrFail($this->editingId);
            $service->update([
                'name' => $this->name,
                'price' => $this->price === '' ? null : $this->price,
                'duration_minutes' => $this->duration_minutes,
                'description' => $this->description ?: null,
                'service_category_id' => $this->service_category_id ?: null,
            ]);
            $this->toastSuccess('تم تعديل الخدمة');
        } else {
            $this->shop->services()->create([
                'name' => $this->name,
                'price' => $this->price === '' ? null : $this->price,
                'duration_minutes' => $this->duration_minutes,
                'description' => $this->description ?: null,
                'service_category_id' => $this->service_category_id ?: null,
                'is_active' => true,
            ]);
            $this->toastSuccess('تم إضافة الخدمة بنجاح');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function updateOrder(int $id, int $position): void
    {
        if ($this->isRateLimited('manage-services')) {
            return;
        }

        $moved = $this->shop->services()->firstWhere('id', $id);

        if (! $moved) {
            return;
        }

        $services = $this->shop->services()
            ->where('service_category_id', $moved->service_category_id)
            ->orderBy('sort_order')
            ->get();

        $sorted = $services->reject(fn ($s) => $s->id === $id)->values();

        $sorted->splice($position, 0, [$moved]);

        foreach ($sorted as $index => $service) {
            $service->update(['sort_order' => $index + 1]);
        }
        $this->toastSuccess('تم تحديث الترتيب');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->price = '';
        $this->duration_minutes = '30';
        $this->description = '';
        $this->service_category_id = null;
    }

    public function render(): View
    {
        return view('livewire.dashboard.manage-services');
    }
}
