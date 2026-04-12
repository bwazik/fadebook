<?php

namespace App\Livewire\Auth;

use App\Enums\OtpType;
use App\Exceptions\OtpException;
use App\Models\User;
use App\Rules\EgyptianPhone;
use App\Services\OtpService;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ForgotPassword extends Component
{
    use WithRateLimiting, WithToast;

    public $step = 1;

    public $phone;

    public bool $otpVerified = false;

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->route('password.change');
        }
    }

    public $otp;

    public $password;

    public $password_confirmation;

    public function sendOtp(OtpService $otpService)
    {
        if ($this->isRateLimited('forgot-password-send', 3, 120)) {
            return;
        }

        $validator = Validator::make([
            'phone' => $this->phone,
        ], [
            'phone' => ['required', 'string', new EgyptianPhone],
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        $user = User::where('phone', $this->phone)->first();

        if ($user) {
            try {
                $otpService->generateAndSend((string) $this->phone, OtpType::PasswordReset, $user->id);
            } catch (OtpException $e) {
                $this->toastError($e->getMessage());

                return;
            }
        } else {
            // Fake delay to prevent timing attacks
            usleep(500000);
        }

        $this->step = 2;
        $this->toastSuccess(__('messages.otp_sent'));
        $this->dispatch('resend-cooldown', seconds: $this->getRateLimitDuration('forgot-password-send', 120));
    }

    public function verifyOtp(OtpService $otpService)
    {
        if ($this->isRateLimited('forgot-password-verify', 5, 60)) {
            return;
        }

        $validator = Validator::make([
            'otp' => $this->otp,
        ], [
            'otp' => 'required|string|digits:6',
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        $user = User::where('phone', $this->phone)->first();

        if (! $user) {
            // Fake delay
            usleep(500000);
            $this->toastError(__('messages.invalid_otp'));

            return;
        }

        try {
            $otpService->verify((string) $this->phone, (string) $this->otp, OtpType::PasswordReset);
            $this->otpVerified = true;
            $this->step = 3;
        } catch (OtpException $e) {
            $this->toastError($e->getMessage());
        }
    }

    public function resendOtp(OtpService $otpService)
    {
        if ($this->isRateLimited('forgot-password-resend', 3, 120)) {
            return;
        }

        $user = User::where('phone', $this->phone)->first();

        if ($user) {
            try {
                $otpService->resend((string) $this->phone, OtpType::PasswordReset, $user->id);
                $this->toastSuccess(__('messages.otp_sent'));
                $this->dispatch('resend-cooldown', seconds: $this->getRateLimitDuration('forgot-password-resend', 120));
            } catch (OtpException $e) {
                $this->toastError($e->getMessage());
            }
        } else {
            usleep(500000);
            $this->toastSuccess(__('messages.otp_sent'));
            $this->dispatch('resend-cooldown', seconds: $this->getRateLimitDuration('forgot-password-resend', 120));
        }
    }

    public function resetPassword()
    {
        if (! $this->otpVerified) {
            $this->toastError(__('messages.otp_invalid'));

            return;
        }

        if ($this->isRateLimited('forgot-password-reset', 5, 60)) {
            return;
        }

        $validator = Validator::make([
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ], [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        $user = User::where('phone', $this->phone)->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($this->password),
                'phone_verified_at' => now(),
            ]);

            Auth::login($user);
        }

        return redirect()->route('home')->with('success', __('messages.password_changed_success'));
    }

    public function goBack()
    {
        if ($this->step > 1) {
            $this->step--;
            $this->otp = null;
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
