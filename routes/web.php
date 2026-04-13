<?php

use App\Livewire\Booking\BookingDetails;
use App\Livewire\Booking\BookingList;
use App\Livewire\Booking\CreateBooking;
use App\Livewire\Home;
use App\Livewire\Onboarding\PendingApproval;
use App\Livewire\Onboarding\ShopSetup;
use App\Livewire\Search;
use App\Livewire\Shop\ShopPage;
use Illuminate\Support\Facades\Route;

Route::get('/offline', fn () => view('offline'))->name('offline');

Route::middleware('auth')->group(function () {
    Route::get('/onboarding/shop', ShopSetup::class)->name('onboarding.shop');
    Route::get('/onboarding/pending', PendingApproval::class)->name('onboarding.pending');
});

// Authenticated & Verified (Main app routes)
Route::middleware(['auth', 'phone.verified'])->group(function () {
    Route::get('/offers', fn () => 'offers')->name('offers');
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/book/{shopSlug}/{serviceId?}', CreateBooking::class)->name('booking.create');
    Route::get('/bookings', BookingList::class)->name('bookings.index');
    Route::get('/bookings/{bookingUuid}', BookingDetails::class)->name('booking.show');
});

// Phase 2 Public Routes
Route::get('/', Home::class)->name('home');
Route::get('/search', Search::class)->name('search');
Route::get('/{areaSlug}/{shopSlug}', ShopPage::class)->name('shop.show');
Route::view('profile', 'profile')->name('profile.index');

require __DIR__.'/auth.php';
