<?php

use App\Contracts\WhatsAppNotificationChannel;
use App\Enums\ShopStatus;
use App\Models\Area;
use App\Models\Shop;
use App\Models\User;
use App\Services\ShopReviewService;

test('super admin approval activates shop and sends whatsapp message', function () {
    $owner = User::factory()->owner()->create();
    $shop = Shop::factory()->for($owner, 'owner')->create(['area_id' => Area::factory()]);

    $notifier = new class implements WhatsAppNotificationChannel
    {
        public array $messages = [];

        public function send(string $phone, string $message): bool
        {
            $this->messages[] = compact('phone', 'message');

            return true;
        }

        public function sendOtp(string $phone, string $code): bool
        {
            return true;
        }
    };

    app()->instance(WhatsAppNotificationChannel::class, $notifier);

    app(ShopReviewService::class)->approve($shop);

    $shop->refresh();

    expect($shop->status)->toBe(ShopStatus::Active);
    expect($notifier->messages)->toHaveCount(1);
});

test('super admin rejection stores reason and sends whatsapp message', function () {
    $owner = User::factory()->owner()->create();
    $shop = Shop::factory()->for($owner, 'owner')->create(['area_id' => Area::factory()]);

    $notifier = new class implements WhatsAppNotificationChannel
    {
        public array $messages = [];

        public function send(string $phone, string $message): bool
        {
            $this->messages[] = compact('phone', 'message');

            return true;
        }

        public function sendOtp(string $phone, string $code): bool
        {
            return true;
        }
    };

    app()->instance(WhatsAppNotificationChannel::class, $notifier);

    app(ShopReviewService::class)->reject($shop, 'الصور مش واضحة');

    $shop->refresh();

    expect($shop->status)->toBe(ShopStatus::Rejected);
    expect($shop->rejection_reason)->toBe('الصور مش واضحة');
    expect($notifier->messages)->toHaveCount(1);
});

test('active shop owner home route resolves to owner dashboard', function () {
    $owner = User::factory()->owner()->create();

    Shop::factory()->active()->for($owner, 'owner')->create([
        'area_id' => Area::factory(),
    ]);

    expect($owner->fresh()->homeRouteName())->toBe('owner.dashboard');
});
