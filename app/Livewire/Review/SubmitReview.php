<?php

declare(strict_types=1);

namespace App\Livewire\Review;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use App\Services\RatingService;
use App\Traits\WithRateLimiting;
use App\Traits\WithToast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SubmitReview extends Component
{
    use WithRateLimiting, WithToast;

    public Booking $booking;

    public int $shopRating = 0;

    public int $barberRating = 0;

    public string $comment = '';

    public function mount(string $bookingUuid): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->booking = $user->bookings()
            ->with(['shop', 'barber'])
            ->where('uuid', $bookingUuid)
            ->firstOrFail();

        if ($this->booking->status !== BookingStatus::Completed) {
            $this->flashToastError(__('messages.review_error_incomplete'));
            $this->redirectRoute('booking.show', ['bookingUuid' => $this->booking->uuid], navigate: true);

            return;
        }

        $existingReview = $this->booking->reviews()->where('user_id', Auth::id())->exists();
        if ($existingReview) {
            $this->flashToastError(__('messages.review_error_already_submitted'));
            $this->redirectRoute('booking.show', ['bookingUuid' => $this->booking->uuid], navigate: true);

            return;
        }

        $this->dispatch('hide-bottom-nav');
    }

    public function setShopRating(int $rating): void
    {
        $this->shopRating = $rating;
    }

    public function setBarberRating(int $rating): void
    {
        $this->barberRating = $rating;
    }

    public function submit(RatingService $ratingService): void
    {
        if ($this->isRateLimited('submit_review', 5, 60)) {
            return;
        }

        try {
            $this->validate([
                'shopRating' => 'required|integer|min:1|max:5',
                'barberRating' => 'nullable|integer|min:0|max:5',
                'comment' => 'nullable|string|max:1000',
            ], [
                'shopRating.required' => __('messages.validation_shop_rating_required'),
                'shopRating.min' => __('messages.validation_shop_rating_min'),
            ]);
        } catch (ValidationException $e) {
            $this->toastError($e->validator->errors()->first());

            return;
        }

        try {
            DB::transaction(function () use ($ratingService) {
                // Shop Review
                $this->booking->shop->reviews()->create([
                    'user_id' => Auth::id(),
                    'booking_id' => $this->booking->id,
                    'rating' => $this->shopRating,
                    'comment' => $this->comment ?: null,
                ]);

                $ratingService->recalculateShopRating($this->booking->shop);

                // Barber Review (Optional)
                if ($this->booking->barber_id && $this->barberRating > 0) {
                    $this->booking->barber->reviews()->create([
                        'user_id' => Auth::id(),
                        'booking_id' => $this->booking->id,
                        'rating' => $this->barberRating,
                        'comment' => $this->comment ?: null,
                    ]);

                    $ratingService->recalculateBarberRating($this->booking->barber);
                }
            });

            $this->flashToastSuccess(__('messages.review_success'));
            $this->redirectRoute('booking.show', ['bookingUuid' => $this->booking->uuid], navigate: true);
        } catch (\Exception $e) {
            $this->toastError(__('messages.review_error_generic'));
        }
    }

    public function render(): View
    {
        return view('livewire.review.submit-review');
    }
}
