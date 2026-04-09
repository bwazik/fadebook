<?php

namespace App\Enums;

enum DiscountType: int
{
    case Percentage = 1;
    case Fixed = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Percentage => 'نسبة مئوية',
            self::Fixed => 'مبلغ ثابت',
        };
    }
}
