<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\User;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EditProfile extends Component
{
    use WithToast;

    public string $name = '';

    public string $email = '';

    public ?string $birthday = null;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email ?? '';
        $this->birthday = $user->birthday ? $user->birthday->format('Y-m-d') : null;

        $this->dispatch('hide-bottom-nav');
    }

    public function updateProfile(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'birthday' => 'nullable|date|before:today',
        ], [
            'name.required' => 'الاسم مطلوب',
            'birthday.before' => 'تاريخ الميلاد يجب أن يكون في الماضي',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email ?: null,
            'birthday' => $this->birthday ?: null,
        ]);

        $this->toastSuccess('تم تحديث البيانات بنجاح');
    }

    public function render(): View
    {
        return view('livewire.profile.edit-profile');
    }
}
