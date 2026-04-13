<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:send-booking-notifications')]
#[Description('Sends smart WhatsApp reminders (before) and follow-ups (after) for bookings')]
class SendBookingReminders extends Command
{
    public function handle(): void
    {
        $now = now();

        /**
         * 1. PRE-SERVICE REMINDERS (1 hour before)
         */
        $oneHourStart = $now->copy()->addHours(1);
        $oneHourEnd = $oneHourStart->copy()->addMinutes(5);

        $oneHourBookings = Booking::with(['client', 'shop', 'service'])
            ->where('status', BookingStatus::Confirmed)
            ->whereBetween('scheduled_at', [$oneHourStart, $oneHourEnd])
            ->where('created_at', '<', $now->copy()->subHours(1))
            ->get();

        foreach ($oneHourBookings as $booking) {
            // TODO: Send 1-hour WhatsApp reminder
        }

        /**
         * 2. POST-SERVICE FOLLOW-UPS (2 hours after)
         * Frames the rating request as a 'Thank You' message.
         */
        $followUpStart = $now->copy()->subHours(2)->subMinutes(5);
        $followUpEnd = $now->copy()->subHours(2);

        $completedBookings = Booking::with(['client', 'shop', 'service', 'barber'])
            ->where('status', BookingStatus::Completed)
            ->whereBetween('scheduled_at', [$followUpStart, $followUpEnd])
            ->get();

        foreach ($completedBookings as $booking) {
            // TODO: Implementation of frequency cap (e.g., skip if user was notified in last 30 days)
            // TODO: Send "Thank you + Rating Request" WhatsApp message
        }
    }
}
