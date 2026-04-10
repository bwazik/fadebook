<?php

namespace App\Services;

use App\Enums\OtpType;
use App\Exceptions\OtpException;
use App\Models\PhoneVerification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

// We should use WithToast trait with any return success or error response
class OtpService
{
    public function __construct(
        protected WhatsappService $whatsappService,
        protected SettingsService $settingsService
    ) {}

    public function generateAndSend(string $phone, OtpType $type, ?int $userId = null): bool
    {
        // Check rate limiting
        if (! $this->checkRateLimit($phone)) {
            throw new OtpException(__('messages.otp_rate_limit_exceeded'));
        }

        // Invalidate previous OTPs for this phone and type
        $this->invalidatePreviousOtps($phone, $type);

        // Generate OTP code
        $otpCode = $this->generateOtpCode();

        // Log OTP plaintext for local development
        Log::channel('otp')->info("OTP Code for {$phone} (Type: {$type->name}): {$otpCode}");

        // Calculate expiry (default 5 minutes)
        $expiresAt = now()->addMinutes((int) $this->settingsService->get('otp_expiry_minutes', 5));

        // Store OTP in database
        $verification = PhoneVerification::create([
            'user_id' => $userId,
            'phone' => $phone,
            'otp_code' => Hash::make($otpCode),
            'type' => $type,
            'expires_at' => $expiresAt,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Send OTP via WhatsApp using instant priority
        $sent = $this->whatsappService->sendMessage(
            phone: $phone,
            template: 'otp_verification',
            data: [
                'otp_code' => $otpCode,
                'expires_in' => (int) $this->settingsService->get('otp_expiry_minutes', 5),
            ],
            priority: 'instant', // INSTANT queue - no delay!
            userId: $userId
        );

        if (! $sent) {
            Log::channel('otp')->error('Failed to send OTP via WhatsApp', [
                'phone' => $phone,
                'type' => $type,
            ]);

            throw new OtpException(__('messages.otp_send_fail'));
        }

        // Update rate limit tracking
        $this->trackOtpRequest($phone, $userId);

        Log::channel('otp')->info('OTP sent successfully', [
            'phone' => $phone,
            'type' => $type,
            'verification_id' => $verification->id,
        ]);

        return true;
    }

    public function verify(string $phone, string $otpCode, OtpType $type): bool
    {
        // Fetch the latest active verification for this phone and type
        $verification = PhoneVerification::forPhone($phone, $type)
            ->active()
            ->latest()
            ->first();

        if (! $verification) {
            throw new OtpException(__('messages.otp_invalid'));
        }

        // Increment attempts immediately — persists even if the code is wrong
        $verification->incrementAttempts();

        // Check if max attempts exceeded
        if ($verification->attempts > (int) $this->settingsService->get('max_otp_attempts', 3)) {
            throw new OtpException(__('messages.otp_max_attempts_exceeded'));
        }

        return DB::transaction(function () use ($verification, $phone, $otpCode, $type) {
            // Lock the row to prevent concurrent verifications
            $verification->lockForUpdate();
            $verification->refresh();

            if (! Hash::check($otpCode, $verification->otp_code)) {
                $remaining = (int) $this->settingsService->get('max_otp_attempts', 3) - $verification->attempts;

                throw new OtpException(__('messages.otp_code_invalid_with_attempts', ['attempts' => $remaining]));
            }

            // Burn the OTP
            $verification->markAsUsed();

            // Auto-verify phone for registration and phone-verification flows
            if ($verification->user_id && in_array($type, [OtpType::Registration, OtpType::PhoneVerification])) {
                User::find($verification->user_id)->update(['phone_verified_at' => now()]);
            }

            $this->clearRateLimit($phone);

            Log::channel('otp')->info('OTP verified successfully', [
                'phone' => $phone,
                'type' => $type,
                'verification_id' => $verification->id,
            ]);

            return true;
        });
    }

    /**
     * Resend OTP with cooldown check
     *
     * @param  string  $phone  Phone number
     * @param  OtpType  $type  OTP type
     * @param  int|null  $userId  User ID if applicable
     * @return array Response array
     */
    public function resend(string $phone, OtpType $type, ?int $userId = null): bool
    {
        // Check if can resend (cooldown period)
        if (! $this->canResend($phone)) {
            $retryAfter = $this->getResendRetryAfter($phone);

            throw new OtpException(__('messages.otp_resend_wait_with_seconds', ['seconds' => $retryAfter]));
        }

        // Track resend request
        $this->trackResendRequest($phone);

        // Generate and send new OTP
        return $this->generateAndSend($phone, $type, $userId);
    }

    /**
     * Generate random 6-digit OTP code
     *
     * @return string 6-digit OTP
     */
    protected function generateOtpCode(): string
    {
        return str_pad(
            string: (string) random_int(100000, 999999),
            length: 6,
            pad_string: '0',
            pad_type: STR_PAD_LEFT
        );
    }

    /**
     * Invalidate all previous OTPs for same phone and type
     *
     * @param  string  $phone  Phone number
     * @param  OtpType  $type  OTP type
     */
    protected function invalidatePreviousOtps(string $phone, OtpType $type): void
    {
        PhoneVerification::forPhone($phone, $type)
            ->where('is_used', false)
            ->update(['is_used' => true]);
    }

    /**
     * Check rate limiting (max 3 requests per hour per phone)
     *
     * @param  string  $phone  Phone number
     * @return bool True if allowed, false if rate limit exceeded
     */
    protected function checkRateLimit(string $phone): bool
    {
        $key = $this->getRateLimitKey($phone);
        $maxRequests = (int) $this->settingsService->get('max_otp_requests_per_hour', 3);
        $attempts = Cache::get($key, 0);

        return $attempts < $maxRequests;
    }

    /**
     * Track OTP request for rate limiting
     *
     * @param  string  $phone  Phone number
     * @param  int|null  $userId  User ID
     */
    protected function trackOtpRequest(string $phone, ?int $userId): void
    {
        $key = $this->getRateLimitKey($phone);

        // Atomic: only sets to 0 if the key is missing. This prevents the "reset-to-zero" race.
        Cache::add($key, 0, 3600);

        // Atomic increment on the server side
        Cache::increment($key);

        // Also track in user model if user exists using atomic SQL increment
        if ($userId) {
            User::where('id', $userId)->increment('otp_request_count', 1, [
                'last_otp_sent_at' => now(),
            ]);
        }
    }

    /**
     * Check if can resend OTP (60 second cooldown)
     *
     * @param  string  $phone  Phone number
     * @return bool True if can resend
     */
    protected function canResend(string $phone): bool
    {
        $key = $this->getResendKey($phone);

        return ! Cache::has($key);
    }

    /**
     * Track resend request with cooldown
     *
     * @param  string  $phone  Phone number
     */
    protected function trackResendRequest(string $phone): void
    {
        $key = $this->getResendKey($phone);
        $cooldown = (int) $this->settingsService->get('otp_resend_cooldown_seconds', 60);
        $expiresAt = time() + $cooldown;

        Cache::put($key, true, $cooldown);
        Cache::put($key.'_expires', $expiresAt, $cooldown);
    }

    /**
     * Get retry after seconds for resend
     *
     * @param  string  $phone  Phone number
     * @return int Seconds until can resend
     */
    protected function getResendRetryAfter(string $phone): int
    {
        $key = $this->getResendKey($phone);
        $expiresAt = Cache::get($key.'_expires');

        if (! $expiresAt) {
            return 0;
        }

        return max(0, $expiresAt - time());
    }

    /**
     * Clear rate limit cache for phone
     *
     * @param  string  $phone  Phone number
     */
    protected function clearRateLimit(string $phone): void
    {
        Cache::forget($this->getRateLimitKey($phone));
        Cache::forget($this->getResendKey($phone));
        Cache::forget($this->getResendKey($phone).'_expires');
    }

    /**
     * Get retry after seconds for rate limit
     *
     * @param  string  $phone  Phone number
     * @return int Seconds until rate limit resets
     */
    protected function getRateLimitRetryAfter(string $phone): int
    {
        // Rate limit is per hour
        return 3600;
    }

    /**
     * Get rate limit cache key
     *
     * @param  string  $phone  Phone number
     * @return string Cache key
     */
    protected function getRateLimitKey(string $phone): string
    {
        return 'otp_rate_limit:'.$phone;
    }

    /**
     * Get resend cooldown cache key
     *
     * @param  string  $phone  Phone number
     * @return string Cache key
     */
    protected function getResendKey(string $phone): string
    {
        return 'otp_resend_cooldown:'.$phone;
    }
}
