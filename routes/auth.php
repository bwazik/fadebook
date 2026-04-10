<?php

use App\Actions\Auth\LogoutUser;
use App\Http\Middleware\EnsurePhoneIsVerified;
use App\Livewire\Auth\ChangePassword;
use App\Livewire\Auth\ChangePhone;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifyPhone;
use Illuminate\Support\Facades\Route;

// ============================================================================
// GUEST ROUTES - Registration, Login, and Password Reset
// ============================================================================
Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');

    // Forgot Password Flow
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
});

// ============================================================================
// AUTHENTICATED ROUTES
// ============================================================================
Route::middleware('auth')->group(function () {
    // Logout Action
    Route::post('/logout', function (LogoutUser $logoutUser) {
        $logoutUser->execute();

        return redirect('/');
    })->name('logout');

    Route::get('/verify-phone', VerifyPhone::class)->name('phone.verification.show');

    // Change Password Flow
    Route::get('/change-password', ChangePassword::class)->name('password.change');
});

// ============================================================================
// PHONE CHANGING ROUTES (Requires Auth AND an already verified Phone)
// ============================================================================
Route::middleware(['auth', EnsurePhoneIsVerified::class])->group(function () {
    Route::get('/change-phone', ChangePhone::class)->name('phone.change');
});
