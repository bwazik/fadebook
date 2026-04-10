<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Get a setting value by key with caching.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("settings:{$key}", 300, function () use ($key, $default) {
            return Setting::where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Set or update a setting value and invalidate cache.
     */
    public function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget("settings:{$key}");
    }
}
