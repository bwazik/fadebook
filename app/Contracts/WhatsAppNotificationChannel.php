<?php

namespace App\Contracts;

interface WhatsAppNotificationChannel
{
    public function send(string $phone, string $message): bool;

    public function sendOtp(string $phone, string $code): bool;
}
