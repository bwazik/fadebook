<?php

namespace App\Livewire\Auth;

use App\Enums\OtpType;
use App\Enums\UserRole;
use App\Exceptions\OtpException;
use App\Models\User;
use App\Notifications\Admin\UserSecurityChangedAdminNotification;
use App\Services\OtpService;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ChangePassword extends Component
{
    use WithRateLimiting, WithToast;

    public $step = 1;

    public $phone;

    public bool $otpVerified = false;

    public $otp;

    public $current_password;

    public $password;

    public $password_confirmation;

    public function mount(): void
    {
        $this->phone = Auth::user()->phone;
    }

    public function sendOtp(OtpService $otpService): void
    {
        if ($this->isRateLimited('change-password-send', 3, 120)) {
            return;
        }

        $validator = Validator::make([
            'current_password' => $this->current_password,
        ], [
            'current_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        /** @var User $user */
        $user = Auth::user();

        if (! Hash::check($this->current_password, $user->password)) {
            $this->toastError(__('messages.current_password_invalid'));

            return;
        }

        try {
            $otpService->generateAndSend((string) $this->phone, OtpType::ChangePassword, $user->id);
            $this->step = 2;
            $this->toastSuccess(__('messages.otp_sent'));
            $this->dispatch('resend-cooldown', seconds: $this->getRateLimitDuration('change-password-send', 120));
        } catch (OtpException $e) {
            $this->toastException($e);
        }
    }

    public function verifyOtp(OtpService $otpService): void
    {
        if ($this->isRateLimited('change-password-verify', 5, 60)) {
            return;
        }

        $validator = Validator::make(
            ['otp' => $this->otp],
            ['otp' => 'required|string|digits:6']
        );

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        try {
            // Verify and burn the OTP immediately to prevent fake progression
            $otpService->verify((string) $this->phone, (string) $this->otp, OtpType::ChangePassword);

            $this->otpVerified = true;
            $this->step = 3;
        } catch (OtpException $e) {
            $this->toastException($e);
        }
    }

    public function resendOtp(OtpService $otpService): void
    {
        if ($this->isRateLimited('change-password-resend', 3, 120)) {
            return;
        }

        try {
            $otpService->resend((string) $this->phone, OtpType::ChangePassword, Auth::id());
            $this->toastSuccess(__('messages.otp_sent'));
            $this->dispatch('resend-cooldown', seconds: $this->getRateLimitDuration('change-password-resend', 120));
        } catch (OtpException $e) {
            $this->toastException($e);
        }
    }

    public function resetPassword(OtpService $otpService)
    {
        if (! $this->otpVerified) {
            $this->toastError(__('messages.otp_invalid'));

            return null;
        }

        if ($this->isRateLimited('change-password-update', 5, 60)) {
            return null;
        }

        $validator = Validator::make(
            [
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ],
            ['password' => 'required|string|min:8|confirmed']
        );

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return null;
        }

        try {
            /** @var User $user */
            $user = Auth::user();
            $user->update([
                'password' => Hash::make($this->password),
                'phone_verified_at' => now(),
            ]);

            // Notify Admins
            $admins = User::query()->where('role', UserRole::SuperAdmin)->get();
            Notification::send($admins, new UserSecurityChangedAdminNotification($user, 'password'));

            $this->flashToastSuccess(__('messages.password_changed_success'));

            return $this->redirectRoute('profile.edit', navigate: true);
        } catch (OtpException $e) {
            $this->toastException($e);

            return null;
        }
    }

    public function goBack(): void
    {
        if ($this->step > 1) {
            $this->step--;
            $this->otp = null;
        }
    }

    public function render(): View
    {
        return view('livewire.auth.change-password', [
            'maskedPhone' => $this->maskPhone($this->phone ?? ''),
        ]);
    }

    private function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 7) {
            return $phone;
        }

        return substr($phone, 0, 3).'•••••••'.substr($phone, -2);
    }
}
