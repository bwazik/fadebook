<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BarberSelectionMode;
use App\Enums\BookingStatus;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Shop;
use Carbon\Carbon;

class SlotCalculatorService
{
    /**
     * Get available time slots for a given date.
     *
     * @return array<string> Array of time strings (e.g., ['09:00', '09:30'])
     */
    public function getAvailableSlots(Shop $shop, ?Barber $barber, Service $service, string $date): array
    {
        $targetDate = Carbon::parse($date);

        if ($targetDate->isPast() && ! $targetDate->isToday()) {
            return [];
        }

        $dayOfWeek = strtolower($targetDate->englishDayOfWeek);
        $openingHours = is_string($shop->opening_hours) ? json_decode($shop->opening_hours, true) : $shop->opening_hours;

        if (! isset($openingHours[$dayOfWeek]) || ! isset($openingHours[$dayOfWeek]['open']) || ! isset($openingHours[$dayOfWeek]['close'])) {
            return [];
        }

        $openTime = Carbon::parse($date.' '.$openingHours[$dayOfWeek]['open']);
        $closeTime = Carbon::parse($date.' '.$openingHours[$dayOfWeek]['close']);

        /**
         * PROOF: Check if specific barber is available on this date.
         * This handles both weekly days off and specific unavailability dates.
         */
        if ($barber && ! $barber->isAvailableOn($targetDate)) {
            return [];
        }

        // Ensure close time is after open time (handle past midnight if needed, though simple for now)
        if ($closeTime->lessThan($openTime)) {
            $closeTime->addDay();
        }

        $duration = $service->duration_minutes;
        $slots = [];

        $currentTime = $openTime->copy();

        // The interval between slot start times (configurable per shop in the future)
        $interval = 15;

        while ($currentTime->copy()->addMinutes($duration)->lessThanOrEqualTo($closeTime)) {
            // Skip past slots if today
            if ($currentTime->isPast()) {
                $currentTime->addMinutes($interval);

                continue;
            }

            $slots[] = $currentTime->format('H:i');
            $currentTime->addMinutes($interval);
        }

        // Fetch conflicting bookings
        $query = Booking::with('service')
            ->where('shop_id', $shop->id)
            ->whereDate('scheduled_at', $targetDate)
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::InProgress]);

        if ($barber && $shop->barber_selection_mode === BarberSelectionMode::ClientPicks) {
            $query->where('barber_id', $barber->id);
        }

        $bookings = $query->get();

        $availableSlots = [];

        // PERFORMANCE FIX: Pre-calculate active barbers count once, outside the loop (Fixes N+1)
        $activeBarbersCount = 1;
        if ($shop->barber_selection_mode === BarberSelectionMode::AnyAvailable) {
            /**
             * PROOF: Only count barbers who are AVAILABLE on this specific date.
             */
            $activeBarbersCount = $shop->barbers()
                ->where('is_active', true)
                ->get()
                ->filter(fn ($b) => $b->isAvailableOn($targetDate))
                ->count();

            if ($activeBarbersCount === 0) {
                return [];
            }
        }

        foreach ($slots as $slot) {
            $slotStart = Carbon::parse($date.' '.$slot);
            $slotEnd = $slotStart->copy()->addMinutes($duration);

            $conflict = false;

            if ($shop->barber_selection_mode === BarberSelectionMode::AnyAvailable) {
                // If any available, we need at least one barber who is free.
                // Simplified MVP logic: just check if total overlapping bookings >= total active barbers
                $overlappingBookings = 0;
                foreach ($bookings as $booking) {
                    $bookingStart = Carbon::parse($booking->scheduled_at);
                    $bookingEnd = $bookingStart->copy()->addMinutes($booking->service->duration_minutes);

                    if ($slotStart->lessThan($bookingEnd) && $slotEnd->greaterThan($bookingStart)) {
                        $overlappingBookings++;
                    }
                }

                if ($overlappingBookings >= $activeBarbersCount) {
                    $conflict = true;
                }
            } else {
                // Specific barber
                foreach ($bookings as $booking) {
                    $bookingStart = Carbon::parse($booking->scheduled_at);
                    $bookingEnd = $bookingStart->copy()->addMinutes($booking->service->duration_minutes);

                    // Check for overlap
                    if ($slotStart->lessThan($bookingEnd) && $slotEnd->greaterThan($bookingStart)) {
                        $conflict = true;
                        break;
                    }
                }
            }

            if (! $conflict) {
                $availableSlots[] = $slot;
            }
        }

        return $availableSlots;
    }
}
