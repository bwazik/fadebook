<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('fadebook:send-review-requests')]
#[Description('Send review requests to users after their booking is completed')]
class SendBookingReviewRequests extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $bookings = Booking::where('status', BookingStatus::Completed)
            ->whereNull('review_request_sent_at')
            ->with(['client', 'shop'])
            ->limit(100)
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings found to request reviews from.');

            return 0;
        }

        $this->info('Processing '.$bookings->count().' booking review requests...');

        foreach ($bookings as $booking) {
            // TODO: The notification logic will be added here later (WhatsApp)
            // The unique link is: route('review.create', ['bookingUuid' => $booking->uuid])

            $booking->update([
                'review_request_sent_at' => now(),
            ]);
        }

        $this->info('Successfully processed review requests.');

        return 0;
    }
}
