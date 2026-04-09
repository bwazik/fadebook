<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'terms_content' => '<p>شروط الاستخدم الخاصة بمنصة فيدبوك...</p>',
            'privacy_content' => '<p>سياسة الخصوصية الخاصة بمنصة فيدبوك...</p>',
            'default_commission_rate' => '10.00',
            'platform_whatsapp_number' => '+201000000000',
            'otp_expiry_minutes' => '5',
            'max_otp_attempts' => '3',
            'max_otp_requests_per_hour' => '5',
            'max_pending_bookings_per_client' => '3',
            'no_show_grace_period_minutes' => '15',
            'cancellation_window_hours' => '2',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
