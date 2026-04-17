<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Setting;
use App\Models\User;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads, WithToast;

    public $avatar;

    public bool $showTermsSheet = false;

    public bool $showPrivacySheet = false;

    public bool $showContactSheet = false;

    public function mount(): void
    {
        $this->dispatch('show-bottom-nav');
    }

    public function updatedAvatar(): void
    {
        $validator = Validator::make([
            'avatar' => $this->avatar,
        ], [
            'avatar' => 'image|max:3072',
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        /** @var User $user */
        $user = Auth::user();

        // Clear existing avatar
        $user->clearCollection('avatar');

        // Store new avatar
        $path = $this->avatar->store('avatars', 'public');
        $user->images()->create([
            'path' => $path,
            'collection' => 'avatar',
        ]);

        $this->toastSuccess(__('messages.profile_avatar_updated'));
        $this->avatar = null;
        unset($this->user);
    }

    #[Computed]
    public function badges(): array
    {
        $user = $this->user;
        $badges = [];

        // Role Badge
        $roleKey = match ($user->role) {
            UserRole::Client => 'role_client',
            UserRole::Barber => 'role_barber',
            UserRole::BarberOwner => 'role_owner',
            UserRole::SuperAdmin => 'role_admin',
            default => 'role_client'
        };

        $badges[] = [
            'label' => __('messages.' . $roleKey),
            'type' => 'accent',
        ];

        // Verification Badge
        if ($user->phone_verified_at) {
            $badges[] = [
                'label' => __('messages.profile_verified'),
                'type' => 'success',
            ];
        }

        // Birthday Badge
        if ($user->birthday) {
            $badges[] = [
                'label' => $user->birthday->translatedFormat('j F Y'),
                'type' => 'gray',
            ];
        }

        return $badges;
    }

    #[Computed]
    public function user(): User
    {
        return Auth::user();
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total_bookings' => $this->user->bookings()->count(),
            'completed_bookings' => $this->user->bookings()->where('status', BookingStatus::Completed)->count(),
            'canceled_bookings' => $this->user->bookings()->where('status', BookingStatus::Cancelled)->count(),
        ];
    }

    #[Computed]
    public function termsContent(): string
    {
        return Setting::get('terms_content', __('messages.profile_terms_fallback'));
    }

    #[Computed]
    public function privacyContent(): string
    {
        return Setting::get('privacy_content', __('messages.profile_privacy_fallback'));
    }

    #[Computed]
    public function contactDeveloperContent(): string
    {
        return Setting::get('contact_developer_content', __('messages.profile_contact_dev_fallback'));
    }

    #[Computed]
    public function developerWhatsApp(): string
    {
        return Setting::get('developer_whatsapp', '201211111111');
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('home'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.profile.index');
    }
}
