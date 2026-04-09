<?php

namespace App\Enums;

enum PaymentMode: int
{
    case NoPayment = 0;
    case PartialDeposit = 1;
    case FullPayment = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::NoPayment => 'بدون دفع',
            self::PartialDeposit => 'دفعة مقدمة جزئية',
            self::FullPayment => 'دفع كامل',
        };
    }
}
