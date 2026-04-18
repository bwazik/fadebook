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
        'ar' => "مبروك! صاحبك عمل حجز باستخدام الكود بتاعك، وتقديراً ليك ضفنالك كود خصم ({discount_amount}) تقدر تستخدمه في حلاقتك الجاية.\n\nكود الخصم: {coupon_code}\n\nشكراً لثقتك في BanhaFade!",
    ],

    // Admin Templates
    'user_registered' => [
        'ar' => "تنبيه للمدير: في مستخدم جديد سجل في المنصة!\nالاسم: {user_name}\nالموبايل: {phone}\nالتاريخ: {date}",
    ],

    'new_booking_admin' => [
        'ar' => "تنبيه للمدير: في حجز جديد تم!\nالصالون: {shop_name}\nالعميل: {client_name}\nالخدمة: {service}\nالميعاد: {time}\nالمبلغ: {total}",
    ],

    'booking_status_changed_admin' => [
        'ar' => "تنبيه للمدير: حالة الحجز اتغيرت!\nالكود: {booking_code}\nالصالون: {shop_name}\nالعميل: {client_name}\nالحالة الجديدة: {status_label}\nالميعاد: {time}",
    ],

    'user_blocked_admin' => [
        'ar' => "تنبيه للمدير: تم حظر مستخدم تلقائيًا!\nالعميل: {user_name}\nالموبايل: {phone}\nالسبب: {reason}",
    ],

    'shop_applied' => [
        'ar' => "تنبيه للمدير: في صالون جديد قدم طلب انضمام!\nالاسم: {shop_name}\nصاحب المحل: {owner_name}\nالموبايل: {phone}\nالمنطقة: {area}",
    ],

    // Barbershop Templates
    'booking_created_barber' => [
        'ar' => "جالك حجز جديد!\nالعميل: {client_name}\nالخدمة: {service}\nالميعاد: {time}\nطريقة الدفع: {payment_method}\nرقم التحويل: {payment_ref}\n\nراجع الحجز من لوحة التحكم لتأكيده.",
    ],

    'booking_cancelled_owner' => [
        'ar' => "تنبيه: العميل ألغى الحجز.\nالعميل: {client_name}\nالخدمة: {service}\nالميعاد: {time}\n\nالخانة دي بقت متاحة دلوقتي للحجز.",
    ],

    // User Templates
    'account_blocked_cancellation' => [
        'ar' => "للأسف تم حظر حسابك مؤقتًا بسبب تكرار إلغاء الحجوزات في وقت متأخر. تقدر تتواصل مع الدعم الفني لحل المشكلة.\n\nلو عايز تعدل إعدادات التنبيهات: {settings_url}",
    ],

    'account_blocked_no_show' => [
        'ar' => "للأسف تم حظر حسابك مؤقتًا بسبب عدم الحضور للمواعيد (No-Show) بشكل متكرر. تقدر تتواصل مع الدعم الفني.\n\nلو عايز تعدل إعدادات التنبيهات: {settings_url}",
    ],

    'booking_cancelled_client' => [
        'ar' => "تم إلغاء حجزك في {shop_name} لميعاد {time} بناءً على طلب الصالون. لو عندك أي استفسار تقدر تتواصل معاهم.\n\nلو عايز تقفل التنبيهات: {settings_url}",
    ],

    'booking_confirmed_client' => [
        'ar' => "خبر سعيد! حجزك في {shop_name} اتأكد بنجاح ✅\nالخدمة: {service}\nالميعاد: {time}\nكود الحجز: {booking_code}\nرقم العملية: {payment_ref}\n\nنتمى لك تجربة سعيدة! \n\nلو عايز تقفل التنبيهات: {settings_url}",
    ],

    'booking_reminder_client' => [
        'ar' => "تذكير: فاضل ساعة على ميعاد حجزك في {shop_name} ⏰\nالميعاد: {time}\nكود الحجز: {booking_code}\n\nمستنيينك في الميعاد!\n\nلو عايز تقفل التنبيهات: {settings_url}",
    ],

    'booking_review_request' => [
        'ar' => "نتمنى تكون استمتعت بتجربتك في {shop_name}! ❤️\nيهمنا جداً نعرف رأيك، ممكن تقييم الحلاق والخدمة من هنا: {review_url}\n\nشكراً ليك!\n\nلو عايز تقفل التنبيهات: {settings_url}",
    ],

    'no_show_warning' => [
        'ar' => "تنبيه: سجلنا إنك محظرتش لميعاد حجزك في {shop_name}. دي المرة رقم ({count})، خلي بالك إن تكرار ده ممكن يعرض حسابك للحظر التلقائي.\n\nلو عندك مشكلة، ياريت تبلغ الصالون قبل الميعاد.\n\nلو عايز تقفل التنبيهات: {settings_url}",
    ],

    'booking_arrived_client' => [
        'ar' => "نورتنا في {shop_name}! بدأنا حجزك دلوقتي لميعاد {time}. نتمى لك تجربة سعيدة.\nكود الحجز: {booking_code}\n\nلو عايز تقفل التنبيهات: {settings_url}",
    ],
];
