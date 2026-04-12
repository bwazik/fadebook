<?php

use App\Livewire\Home;
use App\Livewire\Search;
use App\Livewire\Shop\ShopPage;
use Illuminate\Support\Facades\Route;

Route::get('/offline', fn () => view('offline'))->name('offline');

// Phase 2 Public Routes
Route::get('/', Home::class)->name('home');
Route::get('/search', Search::class)->name('search');
Route::get('/{areaSlug}/{shopSlug}', ShopPage::class)->name('shop.show');

// Authenticated & Verified (Main app routes)
Route::middleware(['auth', 'phone.verified'])->group(function () {
    Route::get('/offers', fn () => 'offers')->name('offers');
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile.index');
    Route::get('/bookings', fn () => 'bookings')->name('bookings');
});

// Load Auth Routes
require __DIR__.'/auth.php';
