<?php

use App\Livewire\Booking\BookingDetails;
use App\Livewire\Booking\BookingList;
use App\Livewire\Booking\CreateBooking;
use App\Livewire\Dashboard\ClientList;
use App\Livewire\Dashboard\Financials;
use App\Livewire\Dashboard\Home as DashboardHome;
use App\Livewire\Dashboard\ManageBarbers;
use App\Livewire\Dashboard\ManageCategories;
use App\Livewire\Dashboard\ManageServices;
use App\Livewire\Dashboard\Reservations;
use App\Livewire\Dashboard\ShopSettings;
use App\Livewire\Home;
use App\Livewire\Onboarding\PendingApproval;
use App\Livewire\Onboarding\ShopSetup;
use App\Livewire\Shop\ShopPage;
use Illuminate\Support\Facades\Route;

Route::get('/offline', fn () => view('offline'))->name('offline');

Route::middleware('auth')->group(function () {
    Route::get('/onboarding/shop', ShopSetup::class)->name('onboarding.shop');
    Route::get('/onboarding/pending', PendingApproval::class)->name('onboarding.pending');
});

// Shop Owner Dashboard Routes
Route::middleware(['auth', 'role:barber_owner'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', DashboardHome::class)->name('home');
    Route::get('/settings', ShopSettings::class)->name('settings');
    Route::get('/barbers', ManageBarbers::class)->name('barbers');
    Route::get('/services', ManageServices::class)->name('services');
    Route::get('/services/categories', ManageCategories::class)->name('categories');
    Route::get('/reservations', Reservations::class)->name('reservations');
    Route::get('/clients', ClientList::class)->name('clients');
    Route::get('/financials', Financials::class)->name('financials');
});

// Authenticated & Verified (Main app routes)
Route::middleware(['auth', 'phone.verified'])->group(function () {
    Route::get('/offers', fn () => 'offers')->name('offers');
    Route::get('/book/{shopSlug}/{serviceId?}', CreateBooking::class)->name('booking.create');
    Route::get('/bookings', BookingList::class)->name('bookings.index');
    Route::get('/bookings/{bookingUuid}', BookingDetails::class)->name('booking.show');
});

// Phase 2 Public Routes
Route::get('/', Home::class)->name('home');
Route::get('/{areaSlug}/{shopSlug}', ShopPage::class)->name('shop.show');
Route::view('profile', 'profile')->name('profile.index');

require __DIR__.'/auth.php';
