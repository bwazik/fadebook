<?php

namespace App\Support;

final class EgyptianPhoneNumber
{
    public static function normalize(string $value): ?string
    {
        $phone = preg_replace('/\D+/', '', trim($value)) ?? '';

        if (str_starts_with($phone, '0020')) {
            $phone = '0'.substr($phone, 4);
        } elseif (str_starts_with($phone, '20') && strlen($phone) === 12) {
            $phone = '0'.substr($phone, 2);
        }

        return preg_match('/^01\d{9}$/', $phone) === 1 ? $phone : null;
    }

    public static function isValid(string $value): bool
    {
        return self::normalize($value) !== null;
    }
}
