<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for sending messages via the Noti-Fire.com WhatsApp API.
    |
    */
    'api_url' => env('WHATSAPP_API_URL', 'https://noti-fire.com/api/send/message'),

    'device_id' => env('WHATSAPP_DEVICE_ID', '6d967866-cafb-46d7-9986-6fcf0797df60'),
];
