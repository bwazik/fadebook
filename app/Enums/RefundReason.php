<?php

namespace App\Enums;

enum RefundReason: int
{
    case ClientCancelEarly = 1;
    case ShopCancel = 2;
    case Other = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::ClientCancelEarly => 'إلغاء العميل مبكراً',
            self::ShopCancel => 'إلغاء المحل',
            self::Other => 'سبب آخر',
        };
    }
}
