<?php

namespace App\Enums;

enum CancelledBy: int
{
    case Client = 1;
    case Shop = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Client => 'العميل',
            self::Shop => 'المحل',
        };
    }
}
