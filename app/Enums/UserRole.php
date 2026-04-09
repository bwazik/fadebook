<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum UserRole: int
{
    case Client = 1;
    case BarberOwner = 2;
    case BarberStaff = 3;
    case SuperAdmin = 4;

    public static function fromKey(string $value): ?self
    {
        return match (Str::snake($value)) {
            'client' => self::Client,
            'barber_owner', 'barber-owner', 'owner' => self::BarberOwner,
            'barber_staff', 'barber-staff', 'staff' => self::BarberStaff,
            'super_admin', 'super-admin', 'admin' => self::SuperAdmin,
            default => null,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Client => 'عميل',
            self::BarberOwner => 'صاحب صالون',
            self::BarberStaff => 'طاقم الصالون',
            self::SuperAdmin => 'سوبر أدمن',
        };
    }
}
