<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Cache key prefix.
     */
    private const CACHE_PREFIX = 'setting_';

    /**
     * Cache TTL in minutes.
     */
    private const CACHE_TTL = 60;

    /**
     * Get a setting value by key with caching.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember(
            self::CACHE_PREFIX.$key,
            now()->addMinutes(self::CACHE_TTL),
            fn () => Setting::get($key, $default)
        );
    }

    /**
     * Set or update a setting value and invalidate cache.
     */
    public function set(string $key, mixed $value): void
    {
        Setting::set($key, $value);

        Cache::forget(self::CACHE_PREFIX.$key);
    }
}
