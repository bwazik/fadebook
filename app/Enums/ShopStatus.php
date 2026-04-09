<?php

namespace App\Enums;

enum ShopStatus: int
{
    case Pending = 0;
    case Active = 1;
    case Suspended = 2;
    case Rejected = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'قيد المراجعة',
            self::Active => 'نشط',
            self::Suspended => 'معلق',
            self::Rejected => 'مرفوض',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Active => 'success',
            self::Suspended => 'danger',
            self::Rejected => 'danger',
        };
    }
}
