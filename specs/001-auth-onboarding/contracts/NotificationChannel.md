# Contract: WhatsApp Notification Channel

**Type**: PHP Interface Adapter (Backend Internal)

## Purpose
Abstracts the concrete WhatsApp API provider (e.g. Meta Graph API, Twilio, external webhook service) away from the business logic.

## Usage
Used during registration, onboarding approval/rejection, and password reset flows for Phase 1.

## Interface Definition

```php
namespace App\Contracts;

interface WhatsAppNotificationChannel
{
    /**
     * Send a plain text message to a WhatsApp number.
     *
     * @param string $phone Must be a normalized Egyptian number (e.g. 01012345678)
     * @param string $message The text message in Egyptian Arabic to send
     * @return bool True on success, false on failure
     */
    public function send(string $phone, string $message): bool;
    
    /**
     * Send an OTP code.
     * 
     * @param string $phone
     * @param string $code The 6-digit numeric OTP code
     * @return bool
     */
    public function sendOtp(string $phone, string $code): bool;
}
```

## Logging Placeholder
For Phase 1, since the concrete WhatsApp API (Phase 6) is not yet implemented, a `LogWhatsAppNotifier` implements this interface and simply logs the `[WhatsApp to {phone}]: {message}` into the Laravel log file for testing and verification purposes.
