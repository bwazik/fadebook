<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

class BookingCodeGenerator
{
    /**
     * Generate a unique, shop-isolated booking code.
     * Pattern: [AlphaPrefix][ShopID][Zero-padded ShopSequence]
     * Example: Shop #7 (Barber) first booking -> B70001
     */
    public function generate(Shop $shop): string
    {
        return DB::transaction(function () use ($shop) {
            // Atomic increment on the shop record to prevent race conditions
            DB::table('shops')
                ->where('id', $shop->id)
                ->increment('total_bookings');

            // Get the new sequence number
            $sequence = (int) DB::table('shops')
                ->where('id', $shop->id)
                ->value('total_bookings');

            $prefix = strtoupper(substr((string) $shop->slug, 0, 1));
            $paddedSequence = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

            // Combining Prefix + ShopID + Sequence ensures global uniqueness
            // while giving every store its own internal B0001... sequence.
            return $prefix.$shop->id.$paddedSequence;
        }, 5); // Retry 5 times if deadlock occurs under heavy load
    }
}
