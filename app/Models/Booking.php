<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\CancelledBy;
use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'uuid',
    'booking_code',
    'shop_id',
    'client_id',
    'barber_id',
    'service_id',
    'coupon_id',
    'scheduled_at',
    'status',
    'service_price',
    'discount_amount',
    'paid_amount',
    'final_amount',
    'notes',
    'policy_accepted',
    'confirmed_at',
    'arrived_at',
    'completed_at',
    'cancelled_at',
    'cancelled_by',
    'payment_method_id',
    'payment_reference',
    'deposit_amount',
    'commission_amount',
    'payment_verified_at',
])]
class Booking extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    /**
     * Scope a query to only include pending bookings.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::Pending);
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::Confirmed);
    }

    /**
     * Scope a query to only include in-progress bookings.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::InProgress);
    }

    /**
     * Scope a query to only include completed bookings.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::Completed);
    }

    /**
     * Scope a query to only include cancelled bookings.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::Cancelled);
    }

    /**
     * Scope a query to only include no-show bookings.
     */
    public function scopeNoShow(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::NoShow);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'arrived_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'status' => BookingStatus::class,
            'cancelled_by' => CancelledBy::class,
            'policy_accepted' => 'boolean',
            'service_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'payment_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the shop for this booking.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the client who made this booking.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the barber assigned to this booking.
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Get the service booked.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the coupon applied to this booking.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(ShopPaymentMethod::class, 'payment_method_id');
    }

    /**
     * Get the refund for this booking.
     */
    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    /**
     * Get the reviews for this booking.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
