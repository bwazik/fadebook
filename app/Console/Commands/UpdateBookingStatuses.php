<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
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
    public function handle(): void
    {
        $now = now();

        // 1. Pending/Confirmed -> InProgress
        // We auto-start the booking at the scheduled time.
        // This ensures the slot looks "active" in the owner's dashboard.
        $startedCount = Booking::whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
            ->where('scheduled_at', '<=', $now)
            ->update(['status' => BookingStatus::InProgress]);

        // 2. InProgress -> Completed
        // "No News is Good News" fallback.
        // If 1 hour passes and the barber hasn't touched the app, we assume everything went well.
        // This protects clients from accidental No-Show strikes caused by busy/lazy barbers.
        $completedCount = Booking::where('status', BookingStatus::InProgress)
            ->where('scheduled_at', '<=', $now->copy()->subHour())
            ->whereNull('completed_at')
            ->update([
                'status' => BookingStatus::Completed,
                'completed_at' => $now,
            ]);

        // NOTE: NoShow is now a MANUALLY triggered status only.
        // A barber must intentionally click "No Show" to punish a client.

        $this->info("Updated: {$startedCount} started, {$completedCount} completed.");
    }
}
