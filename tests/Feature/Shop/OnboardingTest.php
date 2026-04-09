<?php

use App\Enums\ShopStatus;
use App\Livewire\Auth\Login;
use App\Livewire\Shop\OnboardingWizard;
use App\Models\Area;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('owner can complete onboarding and lands on pending status screen', function () {
    Storage::fake('public');

    $owner = User::factory()->owner()->create();
    $area = Area::factory()->create();

    Livewire::actingAs($owner)->test(OnboardingWizard::class)
        ->set('shop_name', 'Fade Cut')
        ->set('address', 'شارع عباس العقاد')
        ->set('area_id', $area->id)
        ->call('nextStep')
        ->set('phone', '01012345678')
        ->set('logo', UploadedFile::fake()->image('logo.jpg'))
        ->call('nextStep')
        ->set('basic_services', 'حلاقة, دقن, تنظيف بشرة')
        ->set('barbers_count', 3)
        ->call('save')
        ->assertRedirect(route('owner.pending'));

    $shop = Shop::query()->with('openingHours')->first();

    expect($shop)->not()->toBeNull();
    expect($shop->status)->toBe(ShopStatus::Pending);
    expect($shop->openingHours)->toHaveCount(7);
    expect($shop->basic_services)->toBe(['حلاقة', 'دقن', 'تنظيف بشرة']);
});

test('logo larger than two megabytes is rejected', function () {
    $owner = User::factory()->owner()->create();
    $area = Area::factory()->create();

    Livewire::actingAs($owner)->test(OnboardingWizard::class)
        ->set('shop_name', 'Fade Cut')
        ->set('address', 'عنوان')
        ->set('area_id', $area->id)
        ->call('nextStep')
        ->set('phone', '01012345678')
        ->set('logo', UploadedFile::fake()->image('logo.png')->size(3000))
        ->call('nextStep')
        ->assertHasErrors(['logo']);
});

test('required fields must be completed before continuing', function () {
    $owner = User::factory()->owner()->create();

    Livewire::actingAs($owner)->test(OnboardingWizard::class)
        ->set('address', 'عنوان')
        ->call('nextStep')
        ->assertHasErrors(['shop_name', 'area_id']);
});

test('owner with completed onboarding sees pending screen on next login', function () {
    $owner = User::factory()->owner()->create([
        'phone' => '01012345678',
        'password' => 'password',
    ]);

    Shop::factory()->for($owner, 'owner')->create([
        'area_id' => Area::factory(),
        'status' => ShopStatus::Pending,
    ]);

    Livewire::test(Login::class)
        ->set('phone', '01012345678')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('owner.pending'));
});

test('rejected shop owner can edit and resubmit', function () {
    $owner = User::factory()->owner()->create();
    $area = Area::factory()->create();
    $shop = Shop::factory()->for($owner, 'owner')->rejected('عدّل البيانات')->create([
        'area_id' => $area->id,
    ]);

    $shop->openingHours()->createMany(
        collect(range(0, 6))->map(fn (int $day) => [
            'day_of_week' => $day,
            'is_closed' => false,
            'open_time' => '10:00:00',
            'close_time' => '22:00:00',
        ])->all(),
    );

    Livewire::actingAs($owner)->test(OnboardingWizard::class)
        ->set('shop_name', 'Fade Cut Updated')
        ->set('address', 'عنوان جديد')
        ->set('area_id', $area->id)
        ->set('phone', '01012345678')
        ->set('basic_services', 'حلاقة, دقن')
        ->set('barbers_count', 4)
        ->call('save')
        ->assertRedirect(route('owner.pending'));

    $shop->refresh();

    expect($shop->status)->toBe(ShopStatus::Pending);
    expect($shop->rejection_reason)->toBeNull();
});
