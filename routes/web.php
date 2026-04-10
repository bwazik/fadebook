<?php

use Illuminate\Support\Facades\Route;

Route::get('/offline', fn () => view('offline'))->name('offline');

// Authenticated & Verified (Main app routes)
Route::middleware(['auth', 'phone.verified'])->group(function () {
    Route::get('/', fn () => 'home')->name('home');
    Route::get('/offers', fn () => 'offers')->name('offers');
    Route::get('/search', fn () => 'search')->name('search');
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile.index');
    Route::get('/bookings', fn () => 'bookings')->name('bookings');
});

// Load Auth Routes
require __DIR__.'/auth.php';
