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
            'max_cancellation_limit' => '5',

            // Rate Limiting Settings
            'rate_limit_login-attempt_attempts' => '5',
            'rate_limit_login-attempt_seconds' => '60',

            'rate_limit_register-next-step_attempts' => '5',
            'rate_limit_register-next-step_seconds' => '60',

            'rate_limit_register-submit_attempts' => '3',
            'rate_limit_register-submit_seconds' => '60',

            'rate_limit_verify-otp_attempts' => '5',
            'rate_limit_verify-otp_seconds' => '60',

            'rate_limit_resend-otp_attempts' => '3',
            'rate_limit_resend-otp_seconds' => '120',

            'rate_limit_change-phone-send_attempts' => '5',
            'rate_limit_change-phone-send_seconds' => '120',

            'rate_limit_change-phone-verify_attempts' => '5',
            'rate_limit_change-phone-verify_seconds' => '60',

            'rate_limit_change-phone-resend_attempts' => '3',
            'rate_limit_change-phone-resend_seconds' => '120',

            'rate_limit_forgot-password-send_attempts' => '3',
            'rate_limit_forgot-password-send_seconds' => '120',

            'rate_limit_forgot-password-verify_attempts' => '5',
            'rate_limit_forgot-password-verify_seconds' => '60',

            'rate_limit_forgot-password-resend_attempts' => '3',
            'rate_limit_forgot-password-resend_seconds' => '120',

            'rate_limit_forgot-password-reset_attempts' => '5',
            'rate_limit_forgot-password-reset_seconds' => '60',

            'rate_limit_change-password-send_attempts' => '3',
            'rate_limit_change-password-send_seconds' => '120',

            'rate_limit_change-password-verify_attempts' => '5',
            'rate_limit_change-password-verify_seconds' => '60',

            'rate_limit_change-password-resend_attempts' => '3',
            'rate_limit_change-password-resend_seconds' => '120',

            'rate_limit_change-password-update_attempts' => '5',
            'rate_limit_change-password-update_seconds' => '60',

            'otp_resend_cooldown_seconds' => '60',
            'otp_cleanup_hours' => '24',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
