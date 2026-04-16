<?php

namespace App\Providers;

use App\Services\OfferService;
use App\Services\ReferralCodeGenerator;
use App\Services\ReferralService;
use App\Services\SettingsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
        $this->app->singleton(ReferralCodeGenerator::class);
        $this->app->singleton(ReferralService::class);
        $this->app->singleton(OfferService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
