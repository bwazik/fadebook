<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/offline', fn () => view('offline'))->name('offline');

Route::get('/home', fn () => 'home')->name('home');
Route::get('/bookings', fn () => 'bookings')->name('bookings');
Route::get('/search', fn () => 'search')->name('search');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
