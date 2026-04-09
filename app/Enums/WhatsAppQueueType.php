<?php

namespace App\Enums;

enum WhatsAppQueueType: int
{
    case Instant = 1;
    case Urgent = 2;
    case Default = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::Instant => 'فوري',
            self::Urgent => 'عاجل',
            self::Default => 'عادي',
        };
    }
}
