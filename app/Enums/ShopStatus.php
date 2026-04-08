<?php

namespace App\Enums;

enum ShopStatus: int
{
    case Pending = 1;
    case Active = 2;
    case Rejected = 3;
    case Suspended = 4;

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'تحت المراجعة',
            self::Active => 'مفعل',
            self::Rejected => 'مرفوض',
            self::Suspended => 'موقوف',
        };
    }
}
