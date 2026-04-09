<?php

namespace App\Enums;

enum UserRole: int
{
    case Client = 1;
    case BarberOwner = 2;
    case SuperAdmin = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::Client => 'عميل',
            self::BarberOwner => 'صاحب محل',
            self::SuperAdmin => 'مدير النظام',
        };
    }
}
