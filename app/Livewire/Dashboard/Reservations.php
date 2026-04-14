<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Enums\BookingStatus;
use App\Enums\CancelledBy;
use App\Models\Booking;
use App\Services\BookingService;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Reservations extends Component
{
    use WithRateLimiting, WithToast;

    public string $tab = 'upcoming'; // upcoming, completed, cancelled

    public int $perPage = 6;

    public function loadMore(): void
    {
        $this->perPage += 6;
    }

    public function mount(): void
    {
        $this->dispatch('show-bottom-nav');
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->perPage = 6;
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

    #[Computed]
    public function bookings()
    {
        $query = $this->getBaseQuery();

        return $query->limit($this->perPage)->get();
    }

    #[Computed]
    public function hasMore(): bool
    {
        $query = $this->getBaseQuery();

        return $query->count() > $this->perPage;
    }

    private function getBaseQuery()
    {
        $query = Auth::user()->shop->bookings()
            ->with(['client', 'service', 'barber']);

        if ($this->tab === 'upcoming') {
            return $query->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed, BookingStatus::InProgress])
                ->whereDate('scheduled_at', '>=', today())
                ->orderBy('scheduled_at', 'asc');
        }

        if ($this->tab === 'completed') {
            return $query->where('status', BookingStatus::Completed)
                ->orderBy('scheduled_at', 'desc');
        }

        return $query->whereIn('status', [BookingStatus::Cancelled, BookingStatus::NoShow])
            ->orderBy('scheduled_at', 'desc');
    }

    public function render(): View
    {
        return view('livewire.dashboard.reservations');
    }
}
