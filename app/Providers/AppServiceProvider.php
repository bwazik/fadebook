<?php

namespace App\Providers;

use App\Contracts\WhatsAppNotificationChannel;
use App\Services\LogWhatsAppNotifier;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WhatsAppNotificationChannel::class, LogWhatsAppNotifier::class);
    }

    public function boot(): void
    {
        //
    }
}
