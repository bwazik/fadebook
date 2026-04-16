<?php

declare(strict_types=1);

namespace App\Enums;

enum ReferralStatus: int
{
    case Pending = 0;
    case Rewarded = 1;
    case Skipped = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'قيد الانتظار',
            self::Rewarded => 'تمت المكافأة',
            self::Skipped => 'تم التخطي',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Rewarded => 'success',
            self::Skipped => 'gray',
        };
    }
}
