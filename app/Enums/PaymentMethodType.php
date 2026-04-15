<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethodType: int
{
    case VodafoneCash = 1;
    case InstaPay = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::VodafoneCash => __('messages.booking_payment_vf_cash'),
            self::InstaPay => __('messages.booking_payment_instapay'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::VodafoneCash => 'wallet',
            self::InstaPay => 'banknotes',
        };
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::VodafoneCash => 'text-red-500 bg-red-500/10 border-red-500/20',
            self::InstaPay => 'text-fadebook-accent bg-fadebook-accent/10 border-fadebook-accent/20',
        };
    }
}
