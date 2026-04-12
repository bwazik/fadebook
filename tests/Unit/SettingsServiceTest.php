<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns the setting value from the database', function () {
    Setting::create(['key' => 'test_key', 'value' => 'test_value']);

    $service = new SettingsService;
    Cache::flush(); // ensure cold cache

    expect($service->get('test_key'))->toBe('test_value');
});

it('returns the default when key does not exist', function () {
    $service = new SettingsService;
    Cache::flush();

    expect($service->get('non_existent_key', 'fallback'))->toBe('fallback');
});

it('caches the setting value on first access', function () {
    Setting::create(['key' => 'cached_key', 'value' => 'cached_value']);

    $service = new SettingsService;
    Cache::flush();

    $service->get('cached_key'); // first call — populates cache

    expect(Cache::has('setting_cached_key'))->toBeTrue();
});

it('returns value from cache on second access', function () {
    Setting::create(['key' => 'cache_hit_key', 'value' => 'original']);

    $service = new SettingsService;
    Cache::flush();

    $service->get('cache_hit_key'); // prime cache

    // Update DB directly — cache should still return old value
    Setting::where('key', 'cache_hit_key')->update(['value' => 'updated']);

    expect($service->get('cache_hit_key'))->toBe('original');
});

it('invalidates the cache when set() is called', function () {
    Setting::create(['key' => 'invalidate_key', 'value' => 'old']);

    $service = new SettingsService;
    Cache::flush();

    $service->get('invalidate_key'); // prime cache
    $service->set('invalidate_key', 'new_value'); // should invalidate

    expect(Cache::has('setting_invalidate_key'))->toBeFalse();
    expect($service->get('invalidate_key'))->toBe('new_value');
});
