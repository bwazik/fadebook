<?php

namespace App\Models;

use App\Enums\ReferralStatus;
use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'uuid',
    'referrer_id',
    'invitee_id',
    'booking_id',
    'coupon_id',
    'status',
    'rewarded_at',
])]
class Referral extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ReferralStatus::class,
            'rewarded_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to only include pending referrals.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ReferralStatus::Pending);
    }

    /**
     * Scope a query to only include rewarded referrals.
     */
    public function scopeRewarded(Builder $query): Builder
    {
        return $query->where('status', ReferralStatus::Rewarded);
    }

    /**
     * Scope a query to only include skipped referrals.
     */
    public function scopeSkipped(Builder $query): Builder
    {
        return $query->where('status', ReferralStatus::Skipped);
    }

    /**
     * Get the referrer user.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the invitee user.
     */
    public function invitee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invitee_id');
    }

    /**
     * Get the booking that triggered the reward.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the coupon issued as reward.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
}
