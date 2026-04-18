<?php

use App\Http\Controllers\PushNotificationController;
use App\Livewire\Booking\BookingDetails;
use App\Livewire\Booking\BookingList;
use App\Livewire\Booking\CreateBooking;
use App\Livewire\Dashboard\ClientList;
use App\Livewire\Dashboard\Financials;
use App\Livewire\Dashboard\Home as DashboardHome;
use App\Livewire\Dashboard\ManageBarbers;
use App\Livewire\Dashboard\ManageCategories;
use App\Livewire\Dashboard\ManageReviews;
use App\Livewire\Dashboard\ManageServices;
use App\Livewire\Dashboard\Reservations;
use App\Livewire\Dashboard\ShopSettings;
use App\Livewire\Home;
use App\Livewire\Notifications;
use App\Livewire\Offers;
use App\Livewire\Onboarding\PendingApproval;
use App\Livewire\Onboarding\ShopSetup;
use App\Livewire\Profile\AppSettings;
use App\Livewire\Profile\EditProfile;
use App\Livewire\Profile\Index;
use App\Livewire\Profile\Referral;
use App\Livewire\Review\SubmitReview;
use App\Livewire\Shop\ShopPage;
use App\Livewire\WhatsAppConnect;
use Illuminate\Support\Facades\Route;

Route::get('/offline', fn () => view('offline'))->name('offline');

// Phase 2 Home
Route::get('/', Home::class)->name('home');

// Auth Routes (Login, Register, etc.)
require __DIR__.'/auth.php';

// Onboarding
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
    Route::get('/reviews', ManageReviews::class)->name('reviews');
});

// Authenticated & Verified (Main app routes)
Route::middleware(['auth', 'phone.verified'])->group(function () {
    Route::get('/offers', Offers::class)->name('offers');
    Route::get('/book/{shopSlug}/{serviceId?}', CreateBooking::class)->name('booking.create');
    Route::get('/bookings', BookingList::class)->name('bookings.index');
    Route::get('/bookings/{bookingUuid}', BookingDetails::class)->name('booking.show');
    Route::get('/review/{bookingUuid}', SubmitReview::class)->name('review.create');

    // Push Notifications Testing
    Route::post('/fcm-token', [PushNotificationController::class, 'updateToken'])->name('fcm.token.update');
    Route::get('/test-push', [PushNotificationController::class, 'testPush'])->name('fcm.test.push');

    // Phase 8 Profile & Settings
    Route::get('/notifications', Notifications::class)->name('notifications.index');
    Route::get('/profile', Index::class)->name('profile.index');
    Route::get('/profile/edit', EditProfile::class)->name('profile.edit');
    Route::get('/settings', AppSettings::class)->name('profile.settings');
    Route::get('/referral', Referral::class)->name('profile.referral');
});

// WhatsApp API Routes
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/whatsapp/connect', WhatsAppConnect::class)->name('whatsapp.connect');
});

// Catch-all Dynamic Shop Routes (MUST BE LAST)
Route::get('/{areaSlug}/{shopSlug}', ShopPage::class)->name('shop.show');
