<?php

namespace App\Livewire\Auth;

use App\Enums\OtpType;
use App\Enums\UserRole;
use App\Exceptions\OtpException;
use App\Models\PhoneChangeHistory;
use App\Models\User;
use App\Notifications\Admin\UserSecurityChangedAdminNotification;
use App\Rules\EgyptianPhone;
use App\Services\OtpService;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ChangePhone extends Component
{
    use WithRateLimiting, WithToast;

    public $step = 1;

    public $current_password;

    public $new_phone;

    public $otp_code;

    public function verifyPasswordAndSendOtp(OtpService $otpService)
    {
        if ($this->isRateLimited('change-phone-send', 5, 120)) {
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        // Check if can change (once per week limit)
        if (! $user->canChangePhone()) {
            $nextDate = $user->getNextPhoneChangeDate();
            $this->toastError(__('messages.phone_change_limit', ['date' => $nextDate->format('Y-m-d')]));

            return;
        }

        $validator = Validator::make([
            'current_password' => $this->current_password,
            'new_phone' => $this->new_phone,
        ], [
            'current_password' => 'required|string',
            'new_phone' => ['required', 'string', 'different:current_password', 'unique:users,phone', new EgyptianPhone],
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        if (! Hash::check($this->current_password, $user->password)) {
            $this->toastError(__('messages.current_password_invalid'));

            return;
        }

        if ($this->new_phone === $user->phone) {
            $this->toastError(__('messages.new_phone_must_be_different'));

            return;
        }

        try {
            $otpService->generateAndSend(
                phone: $this->new_phone,
                type: OtpType::PhoneVerification,
                userId: $user->id
            );

            $this->step = 2;
            $this->toastSuccess(__('messages.otp_sent_to_new_phone'));
        } catch (OtpException $e) {
            $this->toastException($e);
        }
    }

    public function verifyOtp(OtpService $otpService)
    {
        if ($this->isRateLimited('change-phone-verify', 5, 60)) {
            return;
        }

        $validator = Validator::make([
            'otp_code' => $this->otp_code,
        ], [
            'otp_code' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        /** @var User $user */
        $user = Auth::user();

        try {
            $otpService->verify(
                phone: $this->new_phone,
                otpCode: $this->otp_code,
                type: OtpType::PhoneVerification
            );
        } catch (OtpException $e) {
            $this->toastException($e);

            return;
        }

        $oldPhone = $user->phone;

        // Update user
        $user->update([
            'phone' => $this->new_phone,
            'phone_verified_at' => now(),
        ]);

        // Save history securely
        PhoneChangeHistory::create([
            'user_id' => $user->id,
            'old_phone' => $oldPhone,
            'new_phone' => $this->new_phone,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Notify Admins
        $admins = User::query()->where('role', UserRole::SuperAdmin)->get();
        Notification::send($admins, new UserSecurityChangedAdminNotification($user, 'phone'));

        $this->flashToastSuccess(__('messages.phone_changed_success'));

        return $this->redirectRoute('profile.edit', navigate: true);
    }

    public function resendOtp(OtpService $otpService)
    {
        if ($this->isRateLimited('change-phone-resend', 3, 120)) {
            return;
        }

        /** @var User $user */
        $user = Auth::user();

        try {
            $otpService->resend(
                phone: $this->new_phone,
                type: OtpType::PhoneVerification,
                userId: $user->id
            );

            $this->toastSuccess(__('messages.otp_sent'));
        } catch (OtpException $e) {
            $this->toastException($e);
        }
    }

    public function goBack()
    {
        $this->step = 1;
    }

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        return view('livewire.auth.change-phone', [
            'currentPhone' => $user ? $this->maskPhone($user->phone) : '',
            'canChange' => $user ? $user->canChangePhone() : false,
            'nextChangeDate' => $user ? $user->getNextPhoneChangeDate() : null,
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
