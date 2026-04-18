<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:update-booking-statuses')]
#[Description('Automatically update booking statuses based on time.')]
class UpdateBookingStatuses extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(BookingService $bookingService): void
    {
        $now = now();

        // 1. Pending/Confirmed -> InProgress
        // We auto-start the booking at the scheduled time.
        // This ensures the slot looks "active" in the owner's dashboard.
        $startedBookings = Booking::whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
            ->where('scheduled_at', '<=', $now)
            ->get();

        foreach ($startedBookings as $booking) {
            $bookingService->markArrived($booking);
        }

        // 2. InProgress -> Completed
        // "No News is Good News" fallback.
        // If 1 hour passes and the barber hasn't touched the app, we assume everything went well.
        // This protects clients from accidental No-Show strikes caused by busy/lazy barbers.
        $completedBookings = Booking::where('status', BookingStatus::InProgress)
            ->where('scheduled_at', '<=', $now->copy()->subHour())
            ->whereNull('completed_at')
            ->get();

        foreach ($completedBookings as $booking) {
            $bookingService->markCompleted($booking);
        }

        $this->info("Updated: {$startedBookings->count()} started, {$completedBookings->count()} completed.");
    }
}
