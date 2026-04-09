<?php

namespace App\Enums;

enum WhatsAppStatus: int
{
    case Queued = 1;
    case Sent = 2;
    case Failed = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::Queued => 'في الانتظار',
            self::Sent => 'مُرسل',
            self::Failed => 'فشل',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Queued => 'warning',
            self::Sent => 'success',
            self::Failed => 'danger',
        };
    }
}
