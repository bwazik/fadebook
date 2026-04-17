<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\CancelledBy;
use App\Enums\PaymentMode;
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
        protected WhatsAppService $whatsappService,
        protected CouponService $couponService,
        protected SettingsService $settingsService,
        protected ReferralService $referralService
    ) {
    }

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

            if (!in_array($data['time'], $availableSlots)) {
                throw new Exception(__('messages.booking_slot_unavailable'));
            }

            // RE-CALCULATE PRICING (Security FIX: Do not trust client-side amounts)
            $discountAmount = 0;
            $finalAmount = (float) $service->price;
            $couponId = null;

            if (!empty($data['coupon_code'])) {
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
                    $discountAmount = 0;
                    $finalAmount = (float) $service->price;
                }
            }

            // CALCULATE DEPOSIT & COMMISSION
            $paymentMode = $shop->payment_mode instanceof PaymentMode
                ? $shop->payment_mode
                : PaymentMode::from((int) $shop->payment_mode);

            $depositAmount = 0;
            if ($paymentMode === PaymentMode::FullPayment) {
                $depositAmount = $finalAmount;
            } elseif ($paymentMode === PaymentMode::PartialDeposit) {
                $depositAmount = ($finalAmount * (float) $shop->deposit_percentage) / 100;
            }

            $commissionAmount = ($finalAmount * (float) $shop->commission_rate) / 100;

            $scheduledAt = Carbon::parse($data['date'] . ' ' . $data['time']);

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
                'deposit_amount' => $depositAmount,
                'commission_amount' => $commissionAmount,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'payment_reference' => $data['payment_reference'] ?? null,
                'policy_accepted' => true,
                'status' => BookingStatus::Pending,
                'coupon_id' => $couponId,
            ]);

            return $booking;
        });
    }

    public function updatePending(Booking $booking, array $data): Booking
    {
        return DB::transaction(function () use ($booking, $data) {
            if ($booking->status !== BookingStatus::Pending) {
                throw new Exception('لا يمكن تعديل حجز غير معلق');
            }

            $shop = $booking->shop;
            $client = $booking->client;
            $service = Service::findOrFail($data['service_id']);

            // Re-check slot availability (excluding the current booking if needed, but slotCalculator usually uses existing bookings)
            // For MVP simplicity, we check availability normally.
            $availableSlots = $this->slotCalculator->getAvailableSlots(
                $shop,
                $data['barber_id'] ? Barber::find($data['barber_id']) : null,
                $service,
                $data['date']
            );

            // In updatePendig, if they are keeping the SAME time/date/barber, we skip availability check
            $sameTimeDateBarber = $booking->scheduled_at->format('Y-m-d') === $data['date'] &&
                $booking->scheduled_at->format('H:i') === $data['time'] &&
                $booking->barber_id === $data['barber_id'];

            if (!$sameTimeDateBarber && !in_array($data['time'], $availableSlots)) {
                throw new Exception(__('messages.booking_slot_unavailable'));
            }

            // RE-CALCULATE PRICING
            $discountAmount = 0;
            $finalAmount = (float) $service->price;
            $couponId = null;

            if (!empty($data['coupon_code'])) {
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
                    $discountAmount = 0;
                    $finalAmount = (float) $service->price;
                }
            }

            // CALCULATE DEPOSIT & COMMISSION
            $paymentMode = $shop->payment_mode instanceof PaymentMode
                ? $shop->payment_mode
                : PaymentMode::from((int) $shop->payment_mode);

            $depositAmount = 0;
            if ($paymentMode === PaymentMode::FullPayment) {
                $depositAmount = $finalAmount;
            } elseif ($paymentMode === PaymentMode::PartialDeposit) {
                $depositAmount = ($finalAmount * (float) $shop->deposit_percentage) / 100;
            }

            $commissionAmount = ($finalAmount * (float) $shop->commission_rate) / 100;
            $scheduledAt = Carbon::parse($data['date'] . ' ' . $data['time']);

            $booking->update([
                'barber_id' => $data['barber_id'],
                'service_id' => $service->id,
                'scheduled_at' => $scheduledAt,
                'service_price' => $service->price,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'deposit_amount' => $depositAmount,
                'commission_amount' => $commissionAmount,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'payment_reference' => $data['payment_reference'] ?? null,
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

    public function verifyPayment(Booking $booking): void
    {
        $booking->update([
            'status' => BookingStatus::Confirmed,
            'paid_amount' => $booking->deposit_amount,
            'payment_verified_at' => now(),
            'confirmed_at' => now(),
        ]);

        // Record coupon usage if exists
        if ($booking->coupon_id) {
            $this->couponService->recordUsage($booking->coupon, $booking->client);
        }

        // TODO: Send WhatsApp notification to Client (booking_confirmed_client)
    }

    public function cancel(Booking $booking, CancelledBy $by): void
    {
        $booking->update([
            'status' => BookingStatus::Cancelled,
            'cancelled_at' => now(),
            'cancelled_by' => $by,
        ]);

        if ($by === CancelledBy::Client) {
            $client = $booking->client;
            $client->increment('cancellation_count');

            $limit = (int) $this->settingsService->get('max_cancellation_limit', 5);

            if ($client->cancellation_count >= $limit) {
                $client->update(['is_blocked' => true]);
                // TODO: Send WhatsApp notification (account_blocked_cancellation)
            }

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
            'paid_amount' => $booking->final_amount,
        ]);

        // 3. Trigger Referral verification
        $this->referralService->handleBookingCompleted($booking);

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
            // TODO: Send WhatsApp notification (account_blocked_no_show)
        }
    }
}
