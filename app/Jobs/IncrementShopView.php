<?php

namespace App\Jobs;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IncrementShopView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Shop $shop,
        public string $ipAddress
    ) {}

    public function handle(): void
    {
        // Check if there is already a view from this IP in the last 24 hours
        $recentView = $this->shop->views()
            ->where('ip_address', $this->ipAddress)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if (! $recentView) {
            $this->shop->views()->create([
                'ip_address' => $this->ipAddress,
                'user_agent' => request()->userAgent() ?? 'Unknown',
            ]);

            $this->shop->increment('total_views');
        }
    }
}
