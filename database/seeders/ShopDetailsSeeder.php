<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\DiscountType;
use App\Models\Booking;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shop = Shop::find(1);

        if (! $shop) {
            $this->command->error('Shop 1 not found');

            return;
        }

        $barber = $shop->barbers()->first();
        if (! $barber) {
            $this->command->error('No barber found for shop 1');

            return;
        }

        $service = $shop->services()->first();
        if (! $service) {
            $this->command->error('No service found for shop 1');

            return;
        }

        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->error('No users found');

            return;
        }

        // 1. Add Reviews & Dummy Bookings
        $reviewsData = [
            ['rating' => 5.0, 'comment' => 'حلاقة عالمية بجد والتزام بالمواعيد'],
            ['rating' => 4.5, 'comment' => 'المكان نظيف جدا والخدمة ممتازة'],
            ['rating' => 5.0, 'comment' => 'أحسن حلاق في المنطقة من غير كلام'],
            ['rating' => 4.0, 'comment' => 'تجربة كويسة جدا والناس ذوق أوي'],
            ['rating' => 5.0, 'comment' => 'تسلم إيديكوا يا شباب بجد شغل عالي'],
        ];

        foreach ($reviewsData as $index => $data) {
            $user = $users[$index % $users->count()];

            // Create a dummy booking marked as completed (3)
            $booking = Booking::create([
                'client_id' => $user->id,
                'shop_id' => $shop->id,
                'barber_id' => $barber->id,
                'service_id' => $service->id,
                'booking_code' => strtoupper(Str::random(6)),
                'status' => BookingStatus::Completed,
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

        // 2. Add Gallery Placeholders
        for ($i = 1; $i <= 5; $i++) {
            $shop->images()->updateOrCreate(
                ['collection' => 'gallery', 'sort_order' => $i],
                [
                    'path' => "shops/1/gallery_$i.jpg",
                    'disk' => 'public',
                ]
            );
        }

        // 3. Add Banner Placeholder
        $shop->images()->updateOrCreate(
            ['collection' => 'banner'],
            [
                'path' => 'shops/1/banner.jpg',
                'disk' => 'public',
                'sort_order' => 0,
            ]
        );

        // 4. Add Logo Placeholder
        $shop->images()->updateOrCreate(
            ['collection' => 'logo'],
            [
                'path' => 'shops/1/logo.jpg',
                'disk' => 'public',
                'sort_order' => 0,
            ]
        );

        $this->command->info('Successfully seeded Shop ID 1 details (Reviews, Gallery, Banner, Logo).');

        // 5. Add Coupons
        $shop->coupons()->updateOrCreate(
            ['code' => 'FADE20'],
            [
                'discount_type' => DiscountType::Percentage,
                'discount_value' => 20,
                'is_active' => true,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'minimum_amount' => 50,
                'start_date' => now(),
                'end_date' => now()->addMonths(1),
            ]
        );

        $shop->coupons()->updateOrCreate(
            ['code' => 'SAVE50'],
            [
                'discount_type' => DiscountType::Fixed,
                'discount_value' => 50,
                'is_active' => true,
                'usage_limit' => 50,
                'usage_limit_per_user' => 1,
                'minimum_amount' => 100,
                'start_date' => now(),
                'end_date' => now()->addMonths(1),
            ]
        );

        $this->command->info('Successfully seeded Shop ID 1 Coupons (FADE20, SAVE50).');
    }
}
