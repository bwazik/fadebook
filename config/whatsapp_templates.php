<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Message Templates
    |--------------------------------------------------------------------------
    |
    | These templates are used by the WhatsApp service to format outgoing messages.
    | All messages MUST follow the project constitution regarding Egyptian Arabic.
    |
    */

    'otp_verification' => [
        'ar' => "كود التحقق الخاص بك هو: {otp_code}.\nالكود صالح لمدة {expires_in} دقيقة.\nمتشاركش الكود مع أي حد.",
    ],

    'phone_changed_notification' => [
        'ar' => "تنبيه: تم تغيير رقم موبايلك.\nالرقم القديم: {old_phone}\nالرقم الجديد: {new_phone}\nالوقت: {date}\nلو مش انت، تواصل مع الدعم فورًا.",
    ],

    'referral_reward_issued' => [
        'ar' => "مبروك! صاحبك عمل حجز باستخدام الكود بتاعك، وتقديراً ليك ضفنالك كود خصم ({discount_amount}) تقدر تستخدمه في حلاقتك الجاية.\n\nكود الخصم: {coupon_code}\n\nشكراً لثقتك في FadeBook!",
    ],

    // future FadeBook templates go here (booking_confirmed, booking_cancelled, etc.)
];
