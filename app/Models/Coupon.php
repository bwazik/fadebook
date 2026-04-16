<?php

namespace App\Models;

use App\Enums\DiscountType;
use App\Models\Concerns\HasPublicUuid;
use App\Observers\CouponObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(CouponObserver::class)]
#[Fillable([
    'uuid',
    'shop_id',
    'user_id',
    'code',
    'discount_type',
    'discount_value',
    'start_date',
    'end_date',
    'is_active',
    'usage_limit',
    'used_count',
    'usage_limit_per_user',
    'minimum_amount',
    'apply_to',
    'except',
])]
class Coupon extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    /**
     * Scope a query to only include percentage discount coupons.
     */
    public function scopePercentageDiscount(Builder $query): Builder
    {
        return $query->where('discount_type', DiscountType::Percentage);
    }

    /**
     * Scope a query to only include fixed discount coupons.
     */
    public function scopeFixedDiscount(Builder $query): Builder
    {
        return $query->where('discount_type', DiscountType::Fixed);
    }

    /**
     * Scope a query to only include active coupons.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('usage_limit')
                    ->orWhereRaw('used_count < usage_limit');
            });
    }

    /**
     * Scope a query to only include inactive coupons.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false)
            ->orWhere(function (Builder $query) {
                $query->whereNotNull('start_date')
                    ->where('start_date', '>', now());
            })
            ->orWhere(function ($query) {
                $query->whereNotNull('end_date')
                    ->where('end_date', '<', now());
            });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
            'discount_value' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
            'is_active' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'apply_to' => 'array',
            'except' => 'array',
        ];
    }

    /**
     * Get the shop this coupon belongs to.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the user this coupon is locked to (referral coupons only).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bookings that used this coupon.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the per-user usage records for this coupon.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
}
