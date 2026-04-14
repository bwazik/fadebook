<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Enums\BookingStatus;
use App\Enums\CancelledBy;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use App\Services\BookingService;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Home extends Component
{
    use WithRateLimiting, WithToast;

    public int $perPage = 6;

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    #[Computed]
    public function isOwner(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user && $user->role === UserRole::BarberOwner;
    }

    public Shop $shop;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->shop = $user->shop()->firstOrFail();

        $this->dispatch('show-bottom-nav');
    }

    #[Computed]
    public function todayBookings()
    {
        return $this->shop->bookings()
            ->with(['client', 'service', 'barber'])
            ->whereDate('scheduled_at', today())
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::InProgress])
            ->orderBy('scheduled_at', 'asc')
            ->limit($this->perPage)
            ->get();
    }

    #[Computed]
    public function hasMore(): bool
    {
        return $this->shop->bookings()
            ->whereDate('scheduled_at', today())
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::InProgress])
            ->count() > $this->perPage;
    }

    #[Computed]
    public function pendingCount(): int
    {
        return $this->shop->bookings()
            ->where('status', BookingStatus::Pending)
            ->count();
    }

    #[Computed]
    public function stats()
    {
        $monthlyBookings = $this->shop->bookings()
            ->whereMonth('scheduled_at', now()->month)
            ->whereYear('scheduled_at', now()->year)
            ->where('status', BookingStatus::Completed)
            ->get();

        $gross = $monthlyBookings->sum('final_amount');
        $commissionRate = $this->shop->commission_rate ?? 0;
        $commission = $gross * ($commissionRate / 100);
        $net = $gross - $commission;

        return [
            'total_bookings' => $monthlyBookings->count(),
            'gross_earnings' => $gross,
            'commission_deducted' => $commission,
            'net_payout' => $net,
            'commission_rate' => $commissionRate,
            'average_rating' => (float) $this->shop->average_rating,
            'total_reviews' => $this->shop->total_reviews,
        ];
    }

    #[Computed]
    public function barberPerformance()
    {
        return $this->shop->barbers()
            ->where('is_active', true)
            ->get()
            ->sortByDesc('average_rating');
    }

    public function confirmReservation(int $bookingId, BookingService $bookingService): void
    {
        if ($this->isRateLimited('manage-bookings', 15, 60)) {
            return;
        }

        $booking = Booking::findOrFail($bookingId);
        if ($booking->status === BookingStatus::Pending) {
            $bookingService->confirm($booking);
            $this->toastSuccess('تم تأكيد الحجز');
        }
    }

    public function markArrived(int $bookingId, BookingService $bookingService): void
    {
        if ($this->isRateLimited('manage-bookings', 15, 60)) {
            return;
        }

        $booking = Booking::findOrFail($bookingId);
        if ($booking->status === BookingStatus::Confirmed) {
            $bookingService->markArrived($booking);
            $this->toastSuccess('تم تسجيل وصول العميل');
        }
    }

    public function markCompleted(int $bookingId, BookingService $bookingService): void
    {
        if ($this->isRateLimited('manage-bookings', 15, 60)) {
            return;
        }

        $booking = Booking::findOrFail($bookingId);
        if ($booking->status === BookingStatus::InProgress) {
            $bookingService->markCompleted($booking);
            $this->toastSuccess('تم إنهاء الموعد');
        }
    }

    public function markNoShow(int $bookingId, BookingService $bookingService): void
    {
        if ($this->isRateLimited('manage-bookings', 15, 60)) {
            return;
        }

        $booking = Booking::findOrFail($bookingId);
        if ($booking->status === BookingStatus::InProgress || $booking->status === BookingStatus::Confirmed) {
            $bookingService->markNoShow($booking);
            $this->toastSuccess('تم تسجيل العميل كغائب');
        }
    }

    public function cancelBooking(int $bookingId, BookingService $bookingService): void
    {
        if ($this->isRateLimited('manage-bookings', 15, 60)) {
            return;
        }

        $booking = Booking::findOrFail($bookingId);
        if ($booking->status === BookingStatus::Confirmed || $booking->status === BookingStatus::Pending) {
            $bookingService->cancel($booking, CancelledBy::Shop);
            $this->toastSuccess('تم إلغاء الموعد');
        }
    }

    public function render(): View
    {
        return view('livewire.dashboard.home');
    }
}
