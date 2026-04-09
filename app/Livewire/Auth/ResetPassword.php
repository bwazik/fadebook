<?php

namespace App\Livewire\Auth;

use App\Enums\OtpPurpose;
use App\Models\OtpCode;
use App\Models\User;
use App\Support\EgyptianPhoneNumber;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $phone = '';

    public string $code = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectRoute(Auth::user()->homeRouteName(), navigate: true);
        }

        $this->phone = (string) session('password_reset_phone', '');
    }

    public function resetPassword(): mixed
    {
        $validated = $this->validate();
        $phone = EgyptianPhoneNumber::normalize($validated['phone']);

        if ($phone === null) {
            $this->addError('phone', 'ابدأ من شاشة استرجاع كلمة السر الأول.');

            return null;
        }

        $otp = OtpCode::query()
            ->where('phone', $phone)
            ->where('purpose', OtpPurpose::PasswordReset)
            ->where('is_used', false)
            ->latest('id')
            ->first();

        if ($otp === null || $otp->isExpired()) {
            if ($otp !== null) {
                $otp->update(['is_used' => true]);
            }

            $this->addError('code', 'الكود انتهت صلاحيته. اطلب كود جديد.');

            return null;
        }

        if ($otp->attempts >= 5) {
            $otp->update(['is_used' => true]);
            $this->addError('code', 'الكود مبقاش صالح. اطلب كود جديد.');

            return null;
        }

        if (! hash_equals($otp->code, $validated['code'])) {
            $attempts = $otp->attempts + 1;

            $otp->update([
                'attempts' => $attempts,
                'is_used' => $attempts >= 5,
            ]);

            $this->addError('code', $attempts >= 5 ? 'الكود مبقاش صالح. اطلب كود جديد.' : 'الكود اللي كتبته غلط.');

            return null;
        }

        $user = User::query()->where('phone', $phone)->first();

        if ($user === null) {
            $this->addError('phone', 'ابدأ من شاشة استرجاع كلمة السر الأول.');

            return null;
        }

        $user->update(['password' => $validated['password']]);
        $otp->update(['is_used' => true]);
        session()->forget('password_reset_phone');
        session()->flash('status', 'كلمة السر اتغيرت بنجاح.');

        return $this->redirectRoute('login', navigate: true);
    }

    protected function rules(): array
    {
        return [
            'phone' => ['required', 'string'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function render()
    {
        return view('livewire.auth.reset-password')
            ->layout('components.layouts.app', ['title' => 'كلمة سر جديدة']);
    }
}
