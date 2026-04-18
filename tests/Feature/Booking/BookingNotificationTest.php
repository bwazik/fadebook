<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentMethodType;
use App\Enums\PaymentMode;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Service;
use App\Models\Shop;
use App\Models\ShopPaymentMethod;
use App\Models\User;
use App\Notifications\Admin\BookingStatusChangedAdminNotification;
use App\Notifications\Admin\NewBookingAdminNotification;
use App\Notifications\Barbershop\BookingCreatedNotification;
use App\Services\BookingService;
use App\Services\SlotCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('create notifications include full booking details and money breakdown', function () {
    $owner = User::factory()->shopOwner()->create();
    $shop = Shop::factory()->create([
        'owner_id' => $owner->id,
        'payment_mode' => PaymentMode::PartialDeposit,
        'deposit_percentage' => 25,
    ]);
    $client = User::factory()->create([
        'name' => 'أحمد علي',
        'phone' => '01012345678',
    ]);
    $service = Service::factory()->create([
        'shop_id' => $shop->id,
        'name' => 'حلاقة شعر',
        'price' => 200,
    ]);
    $barber = Barber::factory()->create([
        'shop_id' => $shop->id,
        'name' => 'محمد',
    ]);
    $coupon = Coupon::factory()->create([
        'shop_id' => $shop->id,
        'code' => 'SAVE20',
    ]);
    $paymentMethod = ShopPaymentMethod::factory()->create([
        'shop_id' => $shop->id,
        'type' => PaymentMethodType::InstaPay,
    ]);

    $booking = Booking::factory()->create([
        'shop_id' => $shop->id,
        'client_id' => $client->id,
        'barber_id' => $barber->id,
        'service_id' => $service->id,
        'coupon_id' => $coupon->id,
        'payment_method_id' => $paymentMethod->id,
        'payment_reference' => '123456789012',
        'booking_code' => 'A10746',
        'service_price' => 200,
        'discount_amount' => 20,
        'final_amount' => 180,
        'deposit_amount' => 45,
        'paid_amount' => 45,
        'notes' => 'ياريت الالتزام بالميعاد',
        'scheduled_at' => now()->addDay()->setTime(21, 0),
    ]);

    $adminDetails = (new NewBookingAdminNotification($booking))->getWhatsAppData()['booking_details'];
    $ownerDetails = (new BookingCreatedNotification($booking))->getWhatsAppData()['booking_details'];

    expect($adminDetails)
        ->toContain('كود الحجز: A10746')
        ->toContain('الصالون: '.$shop->name)
        ->toContain('العميل: أحمد علي')
        ->toContain('رقم الموبايل: 01012345678')
        ->toContain('الحلاق: محمد')
        ->toContain('الخدمة: حلاقة شعر')
        ->toContain('سعر الخدمة: 200 ج.م')
        ->toContain('الضريبة: 0 ج.م')
        ->toContain('الخصم: -20 ج.م')
        ->toContain('الإجمالي النهائي: 180 ج.م')
        ->toContain('المقدم المطلوب: 45 ج.م')
        ->toContain('المدفوع: 45 ج.م')
        ->toContain('المتبقي: 135 ج.م')
        ->toContain('كوبون الخصم: SAVE20')
        ->toContain('طريقة الدفع: '.PaymentMethodType::InstaPay->getLabel())
        ->toContain('الرقم المرجعي: 123456789012')
        ->toContain('ملاحظات العميل: ياريت الالتزام بالميعاد');

    expect($ownerDetails)
        ->toContain('كود الحجز: A10746')
        ->toContain('العميل: أحمد علي')
        ->toContain('رقم الموبايل: 01012345678')
        ->toContain('الحلاق: محمد')
        ->toContain('الخدمة: حلاقة شعر')
        ->toContain('الإجمالي النهائي: 180 ج.م')
        ->toContain('كوبون الخصم: SAVE20')
        ->toContain('طريقة الدفع: '.PaymentMethodType::InstaPay->getLabel());
});

test('create notifications omit nullable booking lines cleanly', function () {
    $shop = Shop::factory()->create();
    $client = User::factory()->create([
        'name' => 'مصطفى',
    ]);
    $service = Service::factory()->create([
        'shop_id' => $shop->id,
        'name' => 'حلاقة لحية',
        'price' => 120,
    ]);

    $booking = Booking::factory()->create([
        'shop_id' => $shop->id,
        'client_id' => $client->id,
        'barber_id' => null,
        'service_id' => $service->id,
        'coupon_id' => null,
        'payment_method_id' => null,
        'payment_reference' => null,
        'notes' => null,
        'booking_code' => 'B20481',
        'service_price' => 120,
        'discount_amount' => 0,
        'final_amount' => 120,
        'deposit_amount' => 0,
        'paid_amount' => 0,
    ]);

    $adminDetails = (new NewBookingAdminNotification($booking))->getWhatsAppData()['booking_details'];

    expect($adminDetails)
        ->toContain('كود الحجز: B20481')
        ->not->toContain('الحلاق:')
        ->not->toContain('كوبون الخصم:')
        ->not->toContain('طريقة الدفع:')
        ->not->toContain('الرقم المرجعي:')
        ->not->toContain('ملاحظات العميل:')
        ->not->toContain('undefined')
        ->not->toContain('null')
        ->not->toContain('{');
});

test('booking service clears payment fields for no payment shops', function () {
    Notification::fake();

    $client = User::factory()->create();
    $shop = Shop::factory()->create([
        'payment_mode' => PaymentMode::NoPayment,
    ]);
    $service = Service::factory()->create([
        'shop_id' => $shop->id,
    ]);
    $paymentMethod = ShopPaymentMethod::factory()->create([
        'shop_id' => $shop->id,
    ]);

    $slotCalculator = Mockery::mock(SlotCalculatorService::class);
    $slotCalculator->shouldReceive('getAvailableSlots')
        ->once()
        ->andReturn(['10:00']);

    app()->instance(SlotCalculatorService::class, $slotCalculator);

    $booking = app(BookingService::class)->initiate($client, $shop, [
        'service_id' => $service->id,
        'barber_id' => null,
        'date' => now()->addDay()->format('Y-m-d'),
        'time' => '10:00',
        'coupon_code' => null,
        'payment_method_id' => $paymentMethod->id,
        'payment_reference' => '123456789012',
    ]);

    expect($booking->payment_method_id)->toBeNull()
        ->and($booking->payment_reference)->toBeNull()
        ->and((float) $booking->deposit_amount)->toBe(0.0);
});

test('admin booking status changes use the timestamp of the new status', function () {
    Notification::fake();

    $admin = User::factory()->superAdmin()->create();
    $booking = Booking::factory()->create([
        'status' => BookingStatus::Pending,
        'confirmed_at' => null,
    ]);

    app(BookingService::class)->confirm($booking);
    $booking->refresh();

    Notification::assertSentTo(
        $admin,
        BookingStatusChangedAdminNotification::class,
        function (BookingStatusChangedAdminNotification $notification) use ($booking) {
            return $notification->getWhatsAppData()['booking_code'] === $booking->booking_code
                && $notification->getWhatsAppData()['status_time_label'] === 'وقت التأكيد'
                && $notification->getWhatsAppData()['status_time'] === $booking->confirmed_at->translatedFormat('Y-m-d H:i');
        }
    );
});
