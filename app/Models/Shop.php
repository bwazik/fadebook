<?php

namespace App\Models;

use App\Enums\BarberSelectionMode;
use App\Enums\PaymentMode;
use App\Enums\ShopStatus;
use App\Models\Concerns\HasPublicUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'uuid',
    'owner_id',
    'name',
    'slug',
    'description',
    'phone',
    'address',
    'area_id',
    'opening_hours',
    'average_rating',
    'total_reviews',
    'total_views',
    'total_bookings',
    'status',
    'is_online',
    'advance_booking_days',
    'barber_selection_mode',
    'payment_mode',
    'deposit_percentage',
    'commission_rate',
    'rejection_reason',
    'approved_at',
    'rejected_at',
])]
class Shop extends Model
{
    use HasFactory, HasPublicUuid, SoftDeletes;

    /**
     * Scope a query to only include active shops.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ShopStatus::Active);
    }

    /**
     * Scope a query to only include pending shops.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ShopStatus::Pending);
    }

    /**
     * Scope a query to only include online shops.
     */
    public function scopeOnline(Builder $query): Builder
    {
        return $query->where('is_online', true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
            'status' => ShopStatus::class,
            'barber_selection_mode' => BarberSelectionMode::class,
            'payment_mode' => PaymentMode::class,
            'is_online' => 'boolean',
            'average_rating' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'deposit_percentage' => 'decimal:2',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    /**
     * Get the owner of the shop.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the area of the shop.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Get the barbers belonging to this shop.
     */
    public function barbers(): HasMany
    {
        return $this->hasMany(Barber::class);
    }

    /**
     * Get the services offered by this shop.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the bookings for this shop.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the coupons for this shop.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Get the WhatsApp messages associated with this shop.
     */
    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    /**
     * Get all images for this shop (polymorphic).
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get all reviews for this shop (polymorphic).
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get all views for this shop (polymorphic).
     */
    public function views(): MorphMany
    {
        return $this->morphMany(View::class, 'viewable');
    }

    /**
     * Auto-generate slug from name before creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Shop $shop) {
            if (empty($shop->slug)) {
                $shop->slug = Str::slug($shop->name).'-'.Str::random(4);
            }
        });
    }
}
