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
class ManageCategories extends Component
{
    use WithRateLimiting, WithToast;

    public Shop $shop;

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->shop = $user->shop()->firstOrFail();
        $this->dispatch('show-bottom-nav');
    }

    #[Computed]
    public function categories()
    {
        return $this->shop->serviceCategories()
            ->orderBy('sort_order')
            ->get();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $categoryId): void
    {
        $category = $this->shop->serviceCategories()->findOrFail($categoryId);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->showForm = true;
    }

    public function deleteCategory(int $categoryId): void
    {
        if ($this->isRateLimited('manage-categories', 10, 60)) {
            return;
        }

        $category = $this->shop->serviceCategories()->findOrFail($categoryId);

        if ($category->services()->count() > 0) {
            $this->toastError('لا يمكن حذف القسم لأنه يحتوي على خدمات مرتبطة به.');

            return;
        }

        $category->delete();
        $this->toastSuccess('تم حذف القسم بنجاح');
    }

    public function save(): void
    {
        if ($this->isRateLimited('manage-categories', 10, 60)) {
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($this->editingId) {
            $category = $this->shop->serviceCategories()->findOrFail($this->editingId);
            $category->update([
                'name' => $this->name,
            ]);
            $this->toastSuccess('تم تعديل القسم');
        } else {
            $this->shop->serviceCategories()->create([
                'name' => $this->name,
                'sort_order' => $this->shop->serviceCategories()->count(),
            ]);
            $this->toastSuccess('تم إضافة القسم بنجاح');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function updateOrder(array $items): void
    {
        if ($this->isRateLimited('manage-categories')) {
            return;
        }

        foreach ($items as $item) {
            $this->shop->serviceCategories()
                ->where('id', $item['value'])
                ->update(['sort_order' => $item['order']]);
        }
        $this->toastSuccess('تم تحديث الترتيب');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
    }

    public function render(): View
    {
        return view('livewire.dashboard.manage-categories');
    }
}
