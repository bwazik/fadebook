<?php

use App\Models\Booking;
use App\Models\Shop;
use App\Models\User;

$shop = Shop::find(1);
if (! $shop) {
    echo "Shop 1 not found\n";
    exit(1);
}

$barber = $shop->barbers()->first();
if (! $barber) {
    echo "No barber found for shop 1\n";
    exit(1);
}

$users = User::all();
if ($users->isEmpty()) {
    echo "No users found\n";
    exit(1);
}

$reviews = [
    ['rating' => 5.0, 'comment' => 'حلاقة عالمية بجد والتزام بالمواعيد'],
    ['rating' => 4.5, 'comment' => 'المكان نظيف جدا والخدمة ممتازة'],
    ['rating' => 5.0, 'comment' => 'أحسن حلاق في المنطقة من غير كلام'],
    ['rating' => 4.0, 'comment' => 'تجربة كويسة جدا والناس ذوق أوي'],
    ['rating' => 5.0, 'comment' => 'تسلم إيديكوا يا شباب بجد شغل عالي'],
];

$service = $shop->services()->first();
if (! $service) {
    echo "No service found for shop 1\n";
    exit(1);
}

foreach ($reviews as $index => $data) {
    $user = $users[$index % $users->count()];

    // Create a dummy booking
    $booking = Booking::create([
        'client_id' => $user->id,
        'shop_id' => $shop->id,
        'barber_id' => $barber->id,
        'service_id' => $service->id,
        'booking_code' => strtoupper(Str::random(6)),
        'status' => 3,
        'scheduled_at' => now()->subDays(rand(1, 10)),
        'service_price' => 150,
        'final_amount' => 150,
        'policy_accepted' => true,
    ]);

    $shop->reviews()->create([
        'user_id' => $user->id,
        'booking_id' => $booking->id,
        'rating' => $data['rating'],
        'comment' => $data['comment'],
        'reviewable_id' => $shop->id,
        'reviewable_type' => Shop::class,
    ]);
}

for ($i = 1; $i <= 5; $i++) {
    $shop->images()->create([
        'path' => "shops/1/gallery_$i.jpg",
        'disk' => 'public',
        'collection' => 'gallery',
        'sort_order' => $i,
    ]);
}

echo "Successfully added reviews and gallery placeholders for Shop ID 1\n";
