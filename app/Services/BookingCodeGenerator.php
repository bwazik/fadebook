<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;

class BookingCodeGenerator
{
    /**
     * Charset excluding ambiguous characters (O, 0, I, 1).
     */
    private const CHARSET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * Length of the generated booking code.
     */
    private const LENGTH = 6;

    /**
     * Generate a unique booking code.
     *
     * Loops until a code is generated that does not already exist
     * in the bookings table.
     */
    public function generate(): string
    {
        do {
            $code = $this->generateCode();
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }

    /**
     * Generate a random code from the charset.
     */
    private function generateCode(): string
    {
        $charset = self::CHARSET;
        $length = strlen($charset);
        $code = '';

        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= $charset[random_int(0, $length - 1)];
        }

        return $code;
    }
}
