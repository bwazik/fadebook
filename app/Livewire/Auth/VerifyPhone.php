<?php

namespace App\Livewire\Auth;

use App\Enums\OtpType;
use App\Exceptions\OtpException;
use App\Models\User;
use App\Services\OtpService;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VerifyPhone extends Component
{
    use WithRateLimiting, WithToast;

    public $otp;

    public function mount()
    {
        if (Auth::check() && Auth::user()->phone_verified_at) {
            return redirect()->route('home');
        }

        if (session()->has('must_verify')) {
            $this->toastError(__('messages.verify_phone_prompt'));
        }
    }

    public function verify(OtpService $otpService)
    {
        if ($this->isRateLimited('verify-otp', 5, 60)) {
            return;
        }

        $validator = Validator::make([
            'otp' => $this->otp,
        ], [
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $type = session('verification_type', OtpType::PhoneVerification);

        if (! ($type instanceof OtpType)) {
            $type = OtpType::tryFrom((int) $type) ?? OtpType::PhoneVerification;
        }

        try {
            $otpService->verify($user->phone, $this->otp, $type);

            $user->phone_verified_at = now();
            $user->save();

            $redirect = session('verification_redirect', route('home'));
            session()->forget(['otp_sent_for_verification', 'verification_redirect', 'verification_phone', 'verification_type']);

            return redirect()->intended($redirect);
        } catch (OtpException $e) {
            $this->toastException($e);
        }
    }

    public function resend(OtpService $otpService)
    {
        if ($this->isRateLimited('resend-otp', 3, 120)) {
            return;
        }

        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $type = session('verification_type', OtpType::PhoneVerification);

        if (! ($type instanceof OtpType)) {
            $type = OtpType::tryFrom((int) $type) ?? OtpType::PhoneVerification;
        }

        try {
            $otpService->resend(
                phone: $user->phone,
                type: $type,
                userId: $user->id
            );

            $this->toastSuccess(__('messages.otp_sent'));
            $this->dispatch('resend-cooldown', seconds: $this->getRateLimitDuration('resend-otp', 120));
        } catch (OtpException $e) {
            $this->toastException($e);
        }
    }

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user && $user->phone_verified_at) {
            redirect()->route('home');
        }

        return view('livewire.auth.verify-phone', [
            'phone' => $user ? $user->phone : session('verification_phone'),
            'type' => session('verification_type', OtpType::PhoneVerification),
        ]);
    }
}
