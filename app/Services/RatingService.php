<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Barber;
use App\Models\Shop;

class RatingService
{
    /**
     * Recalculate average_rating and total_reviews for a shop.
     */
    public function recalculateShopRating(Shop $shop): void
    {
        $stats = $shop->reviews()
            ->active()
            ->selectRaw('COUNT(*) as total, AVG(rating) as average')
            ->first();

        $shop->update([
            'total_reviews' => $stats->total ?? 0,
            'average_rating' => $stats->average ? round((float) $stats->average, 1) : 0.0,
        ]);
    }

    /**
     * Recalculate average_rating and total_reviews for a barber.
     */
    public function recalculateBarberRating(Barber $barber): void
    {
        $stats = $barber->reviews()
            ->active()
            ->selectRaw('COUNT(*) as total, AVG(rating) as average')
            ->first();

        $barber->update([
            'total_reviews' => $stats->total ?? 0,
            'average_rating' => $stats->average ? round((float) $stats->average, 1) : 0.0,
        ]);
    }
}
