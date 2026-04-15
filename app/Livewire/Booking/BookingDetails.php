<?php

declare(strict_types=1);

namespace App\Livewire\Booking;

use App\Enums\BookingStatus;
use App\Enums\CancelledBy;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use App\Services\SettingsService;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BookingDetails extends Component
{
    use WithRateLimiting, WithToast;

    public Booking $booking;

    public function mount(string $bookingUuid): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->booking = $user->bookings()
            ->with(['shop.area', 'service', 'barber'])
            ->where('uuid', $bookingUuid)
            ->firstOrFail();

        $this->dispatch('show-bottom-nav');
    }

    public function cancelBooking(BookingService $bookingService): void
    {
        if ($this->isRateLimited('cancel-booking', 3, 60)) {
            return;
        }

        if (! $this->canCancel) {
            $this->toastError(__('messages.booking_cancel_error'));

            return;
        }

        try {
            $bookingService->cancel($this->booking, CancelledBy::Client);
            $this->booking->refresh();
            $this->toastSuccess(__('messages.booking_cancel_success'));
        } catch (\Exception $e) {
            $this->toastError($e->getMessage());
        }
    }

    #[Computed]
    public function canCancel(): bool
    {
        // Status must be Pending or Confirmed
        if (! in_array($this->booking->status, [BookingStatus::Pending, BookingStatus::Confirmed])) {
            return false;
        }

        // Check cancellation window from settings service
        $thresholdHours = (int) app(SettingsService::class)->get('cancellation_window_hours', '1');
        $cancellationCutoff = $this->booking->scheduled_at->copy()->subHours($thresholdHours);

        return now()->lt($cancellationCutoff);
    }

    #[Computed]
    public function remainingAmount(): float
    {
        return (float) ($this->booking->final_amount - $this->booking->paid_amount);
    }

    public function render(): View
    {
        return view('livewire.booking.booking-details');
    }
}
