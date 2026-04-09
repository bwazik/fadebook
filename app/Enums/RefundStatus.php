<?php

namespace App\Enums;

enum RefundStatus: int
{
    case Pending = 0;
    case Processed = 1;
    case Failed = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'قيد الانتظار',
            self::Processed => 'تمت المعالجة',
            self::Failed => 'فشل',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processed => 'success',
            self::Failed => 'danger',
        };
    }
}
