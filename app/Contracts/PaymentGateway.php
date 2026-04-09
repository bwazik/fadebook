<?php

declare(strict_types=1);

namespace App\Contracts;

interface PaymentGateway
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createPayment(array $payload): array;

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function processRefund(string $transactionReference, float $amount, array $payload = []): array;

    /**
     * @param  array<string, string>  $headers
     */
    public function verifyWebhook(string $payload, array $headers = []): bool;
}
