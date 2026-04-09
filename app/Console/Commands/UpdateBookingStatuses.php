<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Setting;
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

        // 1. Confirmed -> InProgress (when scheduled_at arrives)
        $confirmedCount = Booking::confirmed()
            ->where('scheduled_at', '<=', $now)
            ->update(['status' => BookingStatus::InProgress]);

        // 2. InProgress -> Completed (1 hour after scheduled_at)
        $completedCount = Booking::inProgress()
            ->where('scheduled_at', '<=', $now->copy()->subHour())
            ->whereNull('completed_at')
            ->update([
                'status' => BookingStatus::Completed,
                'completed_at' => $now,
            ]);

        // 3. Confirmed -> NoShow (grace period after scheduled_at, barber not arrived)
        $gracePeriodMinutes = (int) Setting::get('no_show_grace_period_minutes', 15);
        $noShowCount = Booking::confirmed()
            ->where('scheduled_at', '<=', $now->copy()->subMinutes($gracePeriodMinutes))
            ->whereNull('arrived_at')
            ->update(['status' => BookingStatus::NoShow]);

        $this->info("Updated: {$confirmedCount} to In Progress, {$completedCount} to Completed, {$noShowCount} to No Show.");
    }
}
