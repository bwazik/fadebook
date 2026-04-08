<?php

namespace App\Services;

use App\Contracts\WhatsAppNotificationChannel;
use Illuminate\Support\Facades\Log;

class LogWhatsAppNotifier implements WhatsAppNotificationChannel
{
    public function send(string $phone, string $message): bool
    {
        Log::info(sprintf('[WhatsApp to %s]: %s', $phone, $message));

        return true;
    }

    public function sendOtp(string $phone, string $code): bool
    {
        return $this->send($phone, sprintf('كود استرجاع كلمة السر بتاعك هو %s', $code));
    }
}
