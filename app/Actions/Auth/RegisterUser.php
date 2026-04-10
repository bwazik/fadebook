<?php

namespace App\Actions\Auth;

use App\Enums\OtpType;
use App\Enums\UserRole;
use App\Exceptions\OtpException;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * Create a new user, dispatch registered event, login, and send OTP.
     */
    public function execute(string $name, string $phone, string $password, UserRole $role): User
    {
        $user = DB::transaction(function () use ($name, $phone, $password, $role) {
            $user = User::create([
                'name' => $name,
                'phone' => $phone,
                'password' => Hash::make($password),
                'role' => $role->value,
            ]);

            return $user;
        });

        // Dispatch Laravel's registered event
        event(new Registered($user));

        // Authenticate the newly registered user
        Auth::login($user);

        // Prep session variables for the VerifyPhone component
        session([
            'verification_phone' => $user->phone,
            'verification_type' => OtpType::Registration,
            'verification_redirect' => route('home'),
        ]);

        // Generate and send registration OTP immediately
        // We catch and re-throw so the session is already set before we "error out"
        try {
            $this->otpService->generateAndSend(
                phone: $user->phone,
                type: OtpType::Registration,
                userId: $user->id
            );
        } catch (OtpException $e) {
            // Re-throw to inform the caller, but registration and session are already complete
            throw $e;
        }

        return $user;
    }
}
