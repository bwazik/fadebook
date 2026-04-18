<?php

namespace App\Services;

class WhatsAppTemplateRenderer
{
    /**
     * Render a WhatsApp message template with the provided data.
     */
    public function render(string $template, array $data, string $locale = 'ar'): string
    {
        $raw = config("whatsapp_templates.{$template}.{$locale}", '');

        if (empty($raw)) {
            return "تنبيه: رسالة افتراضية ({$template})";
        }

        foreach ($data as $key => $value) {
            $raw = str_replace("{{$key}}", (string) $value, $raw);
        }

        $raw = preg_replace('/\{[a-zA-Z0-9_]+\}/', '', $raw) ?? $raw;
        $raw = preg_replace("/\n{3,}/", "\n\n", $raw) ?? $raw;

        return trim($raw);
    }
}
