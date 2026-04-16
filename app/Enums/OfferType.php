<?php

declare(strict_types=1);

namespace App\Enums;

enum OfferType: int
{
    case Discount = 1;
    case Referral = 2;
    case Announcement = 3;
}
