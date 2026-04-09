<?php

namespace App\Enums;

enum BarberSelectionMode: int
{
    case AnyAvailable = 1;
    case ClientPicks = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::AnyAvailable => 'أي حلاق متاح',
            self::ClientPicks => 'العميل يختار',
        };
    }
}
