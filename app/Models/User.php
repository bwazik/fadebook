<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Concerns\HasPublicUuid;
use App\Services\ReferralCodeGenerator;
use App\Traits\HasImages;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'uuid',
    'name',
    'email',
    'phone',
    'referral_code',
    'birthday',
    'otp_request_count',
    'last_otp_sent_at',
    'phone_verified_at',
    'password',
    'role',
    'no_show_count',
    'cancellation_count',
    'is_blocked',
    'whatsapp_notifications',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasImages, HasPublicUuid, Notifiable, SoftDeletes;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (! $user->referral_code) {
                $user->referral_code = app(ReferralCodeGenerator::class)->generate();
            }
        });
    }

    /**
     * Scope a query to only include active (non-blocked) users.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_blocked', false);
    }

    /**
     * Scope a query to only include clients.
     */
    public function scopeClients(Builder $query): Builder
    {
        return $query->where('role', UserRole::Client);
    }

    /**
     * Scope a query to only include shop owners.
     */
    public function scopeOwners(Builder $query): Builder
    {
        return $query->where('role', UserRole::BarberOwner);
    }

    /**
     * Scope a query to only include blocked users.
     */
    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('is_blocked', true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_otp_sent_at' => 'datetime',
            'birthday' => 'date',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_blocked' => 'boolean',
            'whatsapp_notifications' => 'boolean',
        ];
    }

    /**
     * Get the shop owned by this user (a barber owner has one shop).
     */
    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class, 'owner_id');
    }

    /**
     * Get the bookings made by this user as a client.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    /**
     * Get the phone verifications for this user.
     */
    public function phoneVerifications(): HasMany
    {
        return $this->hasMany(PhoneVerification::class);
    }

    /**
     * Get the phone change history for this user.
     */
    public function phoneChangeHistory(): HasMany
    {
        return $this->hasMany(PhoneChangeHistory::class);
    }

    /**
     * Get the WhatsApp messages sent to this user.
     */
    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    /**
     * Get the reviews written by this user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the referrals given by this user (where user is referrer).
     */
    public function referralsGiven(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get the referral received by this user (where user is invitee).
     */
    public function referralReceived(): HasOne
    {
        return $this->hasOne(Referral::class, 'invitee_id');
    }

    /**
     * Determine if the user is the super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    /**
     * Determine if the user is a shop owner.
     */
    public function isShopOwner(): bool
    {
        return $this->role === UserRole::BarberOwner;
    }

    /**
     * Determine if the user is a client.
     */
    public function isClient(): bool
    {
        return $this->role === UserRole::Client;
    }

    /**
     * Determine if the user can change their phone number (e.g., once every 7 days).
     */
    public function canChangePhone(): bool
    {
        $lastChange = $this->phoneChangeHistory()->latest()->first();

        if (! $lastChange) {
            return true;
        }

        return $lastChange->created_at->addDays(7)->isPast();
    }

    /**
     * Get the next date the user can change their phone number.
     */
    public function getNextPhoneChangeDate(): Carbon
    {
        $lastChange = $this->phoneChangeHistory()->latest()->first();

        if (! $lastChange) {
            return now();
        }

        return $lastChange->created_at->addDays(7);
    }
}
