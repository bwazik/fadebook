<?php

namespace App\Enums;

enum OtpPurpose: int
{
    case PasswordReset = 1;
    case BookingConfirm = 2;
}
