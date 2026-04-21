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
            'terms_content' => implode("\n\n", [
                '١. الشروط العامة',
                'باستخدامك لموقع بنها فيد، فأنت بتوافق على كل الشروط دي. التطبيق ده معمول عشان يسهل عليك حجز مواعيد الحلاقة، وإحنا بنحاول دايماً نوفرلك أحسن تجربة.',
                '٢. الحجوزات والمواعيد',
                'المواعيد اللي بتتحجز لازم تلتزم بيها. لو مش هتقدر تيجي، يا ريت تلغي الحجز قبلها بوقت كافي عشان تدي فرصة لغيرك، والاكونت بتاعك ممكن يتأثر لو غبت كتير من غير ما تلغي.',
                '٣. الدفع والأسعار',
                'الأسعار الموضحة هي أسعار المحلات نفسها. بنها فيد مش بيزود مصاريف خفية، لكن ممكن يكون في رسوم حجز بسيطة في بعض الحالات.',
                '٤. السلوك العام',
                'إحنا بنهتم بالاحترام المتبادل بين الزباين والباربرز. أي سلوك مش لائق ممكن يؤدي لحظر الحساب فوراً.',
                '٥. التعديلات',
                'بنها فيد حقها تعدل الشروط دي في أي وقت، وهنبلغكم بأي تغييرات مهمة.',
            ]),
            'privacy_content' => implode("\n\n", [
                '١. بنجمع إيه من بياناتك؟',
                'بنجمع اسمك، رقم موبايلك، وتاريخ ميلادك (عشان الهدايا). والبيانات دي بنستخدمها بس عشان رحلة الحجز بتاعتك تكون كملة.',
                '٢. حماية البيانات',
                'بياناتك في أمان تام ومعمولة بأحدث طرق التشفير. مش بنشارك رقمك مع أي حد غير المحل اللي إنت حاجز فيه بس.',
                '٣. إشعارات الواتساب',
                'بنستخدم رقمك عشان نبعتلك تأكيدات الحجز ومواعيدك عن طريق الواتساب، ودي الطريقة الأساسية للتواصل معانا.',
                '٤. حقك في مسح البيانات',
                'تقدر في أي وقت تطلب مسح حسابك وكل بياناتك من إعدادات البروفايل أو تكلمنا وهنساعدك فوراً.',
            ]),
            'contact_developer_content' => implode("\n\n", [
                'أهلاً بيك! أنا المطور اللي شغال على بنها فيد.',
                'لو عندك أي اقتراح، واجهتك مشكلة تقنية، أو حتى عايز تدردش في فكرة جديدة ممكن نطور بيها التطبيق، أنا هكون مبسوط جداً لو سمعت منك.',
                'تقدر تواصل معايا مباشرة على الواتساب، وهحاول أرد عليك في أسرع وقت ممكن.',
            ]),
            'developer_whatsapp' => '201098617164',
            'default_commission_rate' => '10.00',
            'platform_whatsapp_number' => '+201000000000',
            'otp_expiry_minutes' => '5',
            'max_otp_attempts' => '3',
            'max_otp_requests_per_hour' => '5',
            'max_pending_bookings_per_client' => '3',
            'no_show_grace_period_minutes' => '15',
            'cancellation_window_hours' => '2',
            'max_cancellation_limit' => '3',
            'referral_reward_enabled' => 'false',
            'referral_reward_unlimited_mode' => 'false',
            'referral_reward_discount_type' => '2',
            'referral_reward_discount_value' => '20',
            'referral_reward_coupon_expiry_days' => '7',
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
            'fcm_enabled' => 'false',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
