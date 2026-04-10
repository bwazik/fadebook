<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;

class LoginUser
{
    /**
     * Authenticate the user and regenerate the session.
     */
    public function execute(string $phone, string $password): bool
    {
        $credentials = [
            'phone' => $phone,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->is_blocked) {
                Auth::logout();

                return false;
            }

            if (request()->hasSession()) {
                request()->session()->regenerate();
            }

            return true;
        }

        return false;
    }
}
