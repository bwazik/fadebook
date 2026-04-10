<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\RegisterUser;
use App\Enums\UserRole;
use App\Exceptions\OtpException;
use App\Rules\EgyptianPhone;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Register extends Component
{
    use WithRateLimiting, WithToast;

    public $step = 1;

    public $name;

    public $phone;

    public $password;

    public $password_confirmation;

    public $role = 'client';

    public function nextStep()
    {
        if ($this->isRateLimited('register-next-step', 5, 60)) {
            return;
        }

        $validator = Validator::make([
            'name' => $this->name,
            'phone' => $this->phone,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ], [
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'unique:users,phone', new EgyptianPhone],
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        $this->step = 2;
    }

    public function goBack()
    {
        $this->step = 1;
    }

    public function register(RegisterUser $registerUser)
    {
        if ($this->isRateLimited('register-submit', 3, 60)) {
            return;
        }

        $validator = Validator::make([
            'name' => $this->name,
            'phone' => $this->phone,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'role' => $this->role,
        ], [
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'unique:users,phone', new EgyptianPhone],
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|in:client,barber_owner',
        ]);

        if ($validator->fails()) {
            $this->toastError($validator->errors()->first());

            return;
        }

        try {
            $registerUser->execute(
                $this->name,
                $this->phone,
                $this->password,
                $this->role === 'barber_owner' ? UserRole::BarberOwner : UserRole::Client
            );
        } catch (OtpException $e) {
            // Log but proceed; VerifyPhone allows resending
            Log::warning('Registration OTP failed: '.$e->getMessage());
        }

        if ($this->role === 'barber_owner') {
            // Placeholder for Shop Registration flow
            // Could redirect to a specific shop onboarding route, but for now they must verify phone first.
            session(['verification_redirect' => route('home')]);
        }

        return redirect()->intended(route('phone.verification.show'));
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
