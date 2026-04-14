<?php

namespace App\Models;

use App\Models\Concerns\HasPublicUuid;
use App\Observers\BarberObserver;
use App\Traits\HasImages;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(BarberObserver::class)]
#[Fillable([
    'uuid',
    'shop_id',
    'user_id',
    'name',
    'phone',
    'days_off',
    'average_rating',
    'total_reviews',
    'is_active',
])]
class Barber extends Model
{
    use HasFactory, HasImages, HasPublicUuid, SoftDeletes;

    /**
     * Scope a query to only include active barbers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'days_off' => 'array',
            'is_active' => 'boolean',
            'average_rating' => 'decimal:2',
        ];
    }

    /**
     * Get the shop this barber belongs to.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the user account linked to this barber (optional).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the services provided by this barber.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    /**
     * Get the bookings assigned to this barber.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the unavailability records for this barber.
     */
    public function unavailabilities(): HasMany
    {
        return $this->hasMany(BarberUnavailability::class);
    }

    /**
     * Check if a barber is available on a specific date.
     */
    public function isAvailableOn(Carbon $date): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // 1. Check Weekly Days Off (e.g. ['sunday', 'monday'])
        $dayName = strtolower($date->englishDayOfWeek);
        if ($this->days_off && in_array($dayName, $this->days_off)) {
            return false;
        }

        // 2. Check Specific Dates (Unavailability table)
        return ! $this->unavailabilities()
            ->whereDate('unavailable_date', $date->toDateString())
            ->exists();
    }

    /**
     * Get all reviews for this barber (polymorphic).
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
