<?php

namespace App\Livewire\Auth;

use App\Contracts\WhatsAppNotificationChannel;
use App\Enums\OtpPurpose;
use App\Models\OtpCode;
use App\Models\User;
use App\Support\EgyptianPhoneNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $phone = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute(Auth::user()->homeRouteName(), navigate: true);
        }
    }

    public function sendOtp(WhatsAppNotificationChannel $notifier): mixed
    {
        $validated = $this->validate();
        $phone = EgyptianPhoneNumber::normalize($validated['phone']);

        if ($phone === null) {
            $this->addError('phone', 'اكتب رقم موبايل مصري صح.');

            return null;
        }

        $key = 'password-reset:'.$phone;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('phone', 'استني شوية وبعدين جرّب تاني.');

            return null;
        }

        RateLimiter::hit($key, 3600);
        session()->put('password_reset_phone', $phone);

        $user = User::query()->where('phone', $phone)->first();

        if ($user !== null) {
            OtpCode::query()
                ->where('phone', $phone)
                ->where('purpose', OtpPurpose::PasswordReset)
                ->where('is_used', false)
                ->update(['is_used' => true]);

            $otp = OtpCode::query()->create([
                'phone' => $phone,
                'code' => (string) random_int(100000, 999999),
                'purpose' => OtpPurpose::PasswordReset,
                'attempts' => 0,
                'is_used' => false,
                'expires_at' => now()->addMinutes(10),
            ]);

            if (! $notifier->sendOtp($phone, $otp->code)) {
                RateLimiter::clear($key);
                $otp->update(['is_used' => true]);
                $this->addError('phone', 'حصلت مشكلة وإحنا بنبعت الكود. حاول تاني.');

                return null;
            }
        }

        session()->flash('status', 'لو الرقم مسجل عندنا هيوصلك كود على واتساب دلوقتي.');

        return $this->redirectRoute('password.reset', navigate: true);
    }

    protected function rules(): array
    {
        return [
            'phone' => ['required', 'string'],
        ];
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('components.layouts.app', ['title' => 'استرجاع كلمة السر']);
    }
}
