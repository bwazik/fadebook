<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;

class LogoutUser
{
    /**
     * Log the user out of the application and invalidate the session.
     */
    public function execute(): void
    {
        Auth::guard('web')->logout();

        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
    }
}
