<?php

use App\Enums\ShopStatus;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Shop\OnboardingWizard;
use App\Livewire\Shop\PendingShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'marketplace')->name('marketplace');

Route::livewire('/register', Register::class)->name('register');
Route::livewire('/login', Login::class)->name('login');
Route::livewire('/forgot-password', ForgotPassword::class)->name('password.request');
Route::livewire('/reset-password', ResetPassword::class)->name('password.reset');

Route::middleware(['auth', 'role:barber_owner'])->group(function (): void {
    Route::livewire('/onboarding', OnboardingWizard::class)->name('onboarding');
    Route::livewire('/owner/pending', PendingShop::class)->name('owner.pending');

    Route::get('/owner/dashboard', function (Request $request) {
        $shop = $request->user()->shops()->with('area')->latest('id')->first();

        if ($shop?->status !== ShopStatus::Active) {
            return redirect()->route($request->user()->homeRouteName());
        }

        return view('owner-dashboard', ['shop' => $shop]);
    })->name('owner.dashboard');
});

Route::middleware(['auth', 'role:super_admin'])->get('/admin', function () {
    return view('admin-dashboard');
})->name('admin.dashboard');

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('marketplace');
})->middleware('auth')->name('logout');
