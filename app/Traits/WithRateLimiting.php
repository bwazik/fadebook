<?php

namespace App\Traits;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

trait WithRateLimiting
{
    /**
     * Check if the user is rate limited for a specific action.
     * Automatically dispatches a toast message if limited.
     *
     * @param  string  $actionName  (e.g., 'manage-request')
     * @return bool True if limited, False if allowed to proceed.
     */
    public function isRateLimited(string $actionName, int $maxAttempts = 5, int $decaySeconds = 60): bool
    {
        $settingsService = app(SettingsService::class);

        // Fetch dynamic settings from DB, fallback to the parameters passed if not found.
        $dynamicAttempts = (int) $settingsService->get("rate_limit_{$actionName}_attempts", $maxAttempts);
        $dynamicSeconds = (int) $settingsService->get("rate_limit_{$actionName}_seconds", $decaySeconds);

        $identifier = Auth::id() ?? request()->ip();
        $key = "{$actionName}:{$identifier}";

        if (RateLimiter::tooManyAttempts($key, $dynamicAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            $message = __('messages.rate_limit_exceeded_with_seconds', ['seconds' => $seconds]);

            if (method_exists($this, 'toastError')) {
                $this->toastError($message);
            } else {
                $this->dispatch('toast', message: $message, type: 'error');
            }

            return true;
        }

        RateLimiter::hit($key, $dynamicSeconds);

        return false;
    }

    /**
     * Get the configured duration for a rate limited action.
     */
    public function getRateLimitDuration(string $actionName, int $defaultSeconds = 60): int
    {
        return (int) app(SettingsService::class)->get("rate_limit_{$actionName}_seconds", $defaultSeconds);
    }
}
