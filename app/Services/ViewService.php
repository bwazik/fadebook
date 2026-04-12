<?php

namespace App\Services;

use App\Models\Shop;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewService
{
    /**
     * Track a view for a shop.
     * Only counts once per user/IP per day.
     */
    public function trackView(Shop $shop, Request $request): bool
    {
        return $this->processTracking(
            $shop,
            Auth::id(),
            $request->ip() ?? '127.0.0.1',
            $request->userAgent()
        );
    }

    /**
     * Track a view using raw data (useful for background jobs).
     */
    public function trackViewRaw(Shop $shop, ?int $userId, string $ip, ?string $userAgent): bool
    {
        return $this->processTracking($shop, $userId, $ip, $userAgent);
    }

    /**
     * Internal processor for view tracking.
     */
    protected function processTracking(Shop $shop, ?int $userId, string $ip, ?string $userAgent): bool
    {
        $hasViewedToday = $this->hasViewedAlready($shop, $userId, $ip);

        if ($hasViewedToday) {
            return false;
        }

        View::create([
            'shop_id' => $shop->id,
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'viewed_at' => now(),
        ]);

        // Increment total views on the shop model
        $shop->increment('total_views');

        return true;
    }

    /**
     * Get view count for a shop.
     */
    public function getViewCount(Shop $shop): int
    {
        return (int) $shop->total_views;
    }

    /**
     * Check if user/IP viewed already today.
     */
    public function hasViewedAlready(Shop $shop, ?int $userId, string $ip): bool
    {
        return View::where('shop_id', $shop->id)
            ->where(function ($query) use ($userId, $ip) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ip);
                }
            })
            ->today()
            ->exists();
    }
}
