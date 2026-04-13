<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\CancelledBy;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected BookingCodeGenerator $codeGenerator,
        protected SlotCalculatorService $slotCalculator,
        protected WhatsappService $whatsappService,
        protected CouponService $couponService
    ) {}

    public function initiate(User $client, Shop $shop, array $data): Booking
    {
        return DB::transaction(function () use ($client, $shop, $data) {
            if ($client->is_blocked) {
                throw new Exception(__('messages.booking_blocked_user'));
            }

            $activeStatuses = [
                BookingStatus::Pending,
                BookingStatus::Confirmed,
                BookingStatus::InProgress,
            ];

            $hasActiveBooking = $client->bookings()
                ->whereIn('status', $activeStatuses)
                ->exists();

            if ($hasActiveBooking) {
                throw new Exception(__('messages.booking_active_exists'));
            }

            $service = Service::findOrFail($data['service_id']);

            // Re-check slot availability
            $availableSlots = $this->slotCalculator->getAvailableSlots(
                $shop,
                $data['barber_id'] ? Barber::find($data['barber_id']) : null,
                $service,
                $data['date']
            );

            if (! in_array($data['time'], $availableSlots)) {
                throw new Exception(__('messages.booking_slot_unavailable'));
            }

            // RE-CALCULATE PRICING (Security FIX: Do not trust client-side amounts)
            $discountAmount = 0;
            $finalAmount = (float) $service->price;
            $couponId = null;

            if (! empty($data['coupon_code'])) {
                try {
                    $result = $this->couponService->validateAndCalculate(
                        $data['coupon_code'],
                        $shop,
                        $service,
                        $client
                    );
                    $discountAmount = (float) $result['discount_amount'];
                    $finalAmount = (float) $result['final_amount'];
                    $couponId = $result['coupon']->id;
                } catch (Exception $e) {
                    // If coupon is invalid now, we just proceed without discount
                    // or we could throw an exception if we want to be strict.
                    // For now, if they tampered with code, they just lose the discount.
                    $discountAmount = 0;
                    $finalAmount = (float) $service->price;
                }
            }

            $scheduledAt = Carbon::parse($data['date'].' '.$data['time']);

            $booking = Booking::create([
                'client_id' => $client->id,
                'shop_id' => $shop->id,
                'barber_id' => $data['barber_id'],
                'service_id' => $service->id,
                'booking_code' => $this->codeGenerator->generate($shop),
                'scheduled_at' => $scheduledAt,
                'service_price' => $service->price,
                'discount_amount' => $discountAmount,
                'paid_amount' => 0,
                'final_amount' => $finalAmount,
                'policy_accepted' => true,
                'status' => BookingStatus::Pending,
                'coupon_id' => $couponId,
            ]);

            return $booking;
        });
    }

    public function confirm(Booking $booking): void
    {
        $booking->update([
            'status' => BookingStatus::Confirmed,
            'confirmed_at' => now(),
        ]);

        // Record coupon usage if exists
        if ($booking->coupon_id) {
            $this->couponService->recordUsage($booking->coupon, $booking->client);
        }

        // TODO: Send WhatsApp notification to Client (booking_confirmed_client)

        // TODO: Send WhatsApp notification to Shop Owner (booking_created_owner)
    }

    public function cancel(Booking $booking, CancelledBy $by): void
    {
        $booking->update([
            'status' => BookingStatus::Cancelled,
            'cancelled_at' => now(),
            'cancelled_by' => $by,
        ]);

        if ($by === CancelledBy::Client) {
            // TODO: Send WhatsApp notification to Owner (booking_cancelled_owner)
        } else {
            // TODO: Send WhatsApp notification to Client (booking_cancelled_client)
        }
    }

    public function markArrived(Booking $booking): void
    {
        $booking->update([
            'status' => BookingStatus::InProgress,
            'arrived_at' => now(),
        ]);

        // TODO: Send WhatsApp notification (booking_arrived)
    }

    public function markCompleted(Booking $booking): void
    {
        $booking->update([
            'status' => BookingStatus::Completed,
            'completed_at' => now(),
        ]);

        // Send review request
        // TODO: Send WhatsApp notification (booking_review_request)
    }

    public function markNoShow(Booking $booking): void
    {
        $booking->update([
            'status' => BookingStatus::NoShow,
        ]);

        $client = $booking->client;
        $client->increment('no_show_count');

        if ($client->no_show_count === 1) {
            // TODO: Send WhatsApp notification (no_show_warning)
        } elseif ($client->no_show_count >= 2) {
            $client->update(['is_blocked' => true]);
            // TODO: Send WhatsApp notification (account_blocked)
        }
    }
}
