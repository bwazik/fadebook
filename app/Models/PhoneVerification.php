<?php

namespace App\Models;

use App\Enums\OtpType;
use App\Services\SettingsService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'phone',
    'otp_code',
    'type',
    'expires_at',
    'verified_at',
    'attempts',
    'is_used',
    'ip_address',
    'user_agent',
])]
class PhoneVerification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Determine if the verification record is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Determine if the verification record is valid for use.
     */
    public function isValid(): bool
    {
        $maxAttempts = (int) app(SettingsService::class)->get('max_otp_attempts', 3);

        return ! $this->isExpired()
            && ! $this->is_used
            && $this->attempts < $maxAttempts;
    }

    /**
     * Mark the verification as used.
     */
    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Increment the verification attempts.
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Scope a query to only include active (not used and not expired) verifications.
     */
    public function scopeActive($query)
    {
        return $query->notUsed()->notExpired();
    }

    /**
     * Scope a query to only include expired verifications.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope a query to only include non-expired verifications.
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope a query to only include used verifications.
     */
    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    /**
     * Scope a query to only include unused verifications.
     */
    public function scopeNotUsed($query)
    {
        return $query->where('is_used', false);
    }

    /**
     * Scope a query to only include records older than a certain number of hours.
     */
    public function scopeOlderThanHours($query, int $hours)
    {
        return $query->where('created_at', '<', now()->subHours($hours));
    }

    /**
     * Scope a query to only include verifications for a specific phone and type.
     */
    public function scopeForPhone($query, string $phone, OtpType $type)
    {
        return $query->where('phone', $phone)
            ->where('type', $type);
    }

    /**
     * Scope a query to order by the most recent record.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => OtpType::class,
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'is_used' => 'boolean',
        ];
    }

    /**
     * Get the user this verification belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
