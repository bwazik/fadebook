<?php

namespace App\Enums;

enum OtpType: int
{
    case Registration = 1;
    case PhoneVerification = 2;
    case PasswordReset = 3;
    case ChangePassword = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::Registration => 'تسجيل',
            self::PhoneVerification => 'تحقق من الرقم',
            self::PasswordReset => 'استعادة كلمة المرور',
            self::ChangePassword => 'تغيير كلمة المرور',
        };
    }
}
