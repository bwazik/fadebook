<?php

namespace App\Enums;

enum BookingStatus: int
{
    case Pending = 0;
    case Confirmed = 1;
    case InProgress = 2;
    case Completed = 3;
    case Cancelled = 4;
    case NoShow = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'قيد الانتظار',
            self::Confirmed => 'مؤكد',
            self::InProgress => 'جاري التنفيذ',
            self::Completed => 'مكتمل',
            self::Cancelled => 'ملغي',
            self::NoShow => 'لم يحضر',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'info',
            self::InProgress => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
            self::NoShow => 'danger',
        };
    }
}
