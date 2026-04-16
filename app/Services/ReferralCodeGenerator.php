<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReferralCodeGenerator
{
    /**
     * Charset excludes ambiguous characters (0, O, I, 1, L).
     * ABCDEFGHJKMNPQRSTUVWXYZ23456789
     */
    protected const CHARSET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    /**
     * Loop until unique against users.referral_code
     */
    public function generate(): string
    {
        do {
            $code = $this->generateRandomCode(8);
        } while (DB::table('users')->where('referral_code', $code)->exists());

        return $code;
    }

    protected function generateRandomCode(int $length): string
    {
        $code = '';
        $charsetLength = strlen(self::CHARSET);

        for ($i = 0; $i < $length; $i++) {
            $code .= self::CHARSET[random_int(0, $charsetLength - 1)];
        }

        return $code;
    }
}
