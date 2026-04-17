<?php

namespace App\Jobs;

use App\Enums\WhatsAppStatus;
use App\Models\WhatsappMessage;
use App\Services\WhatsAppTemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;

    public $tries = 3; // Retry up to 3 times

    public $backoff = [30, 60, 120]; // Delay retries by 30s, 60s, 120s

    public function __construct(WhatsappMessage $message)
    {
        $this->message = $message;
    }

    public function handle(WhatsAppTemplateRenderer $renderer)
    {
        $url = config('services.evolution.url');
        $apiKey = config('services.evolution.key');
        $instanceName = config('services.evolution.instance');

        if (! $url || ! $apiKey || ! $instanceName) {
            $this->message->update([
                'status' => WhatsAppStatus::Failed,
                'error_message' => 'WhatsApp Evolution API configuration missing',
                'attempts' => $this->message->attempts + 1,
            ]);
            Log::channel('whatsapp')->error('WhatsApp Evolution API configuration missing', [
                'message_id' => $this->message->id,
                'queue' => $this->queue,
            ]);
            $this->fail();

            return;
        }

        // Format message based on template using the renderer
        $content = $renderer->render(
            template: $this->message->template,
            data: $this->message->data ?? [],
            locale: 'ar' // Defaulting to Arabic per constitution
        );

        try {
            // Send message via Evolution API with resilient timeout and retry
            $response = Http::timeout(25)
                ->connectTimeout(10)
                ->retry(2, 1000)
                ->withHeaders(['apikey' => $apiKey])
                ->post("{$url}/message/sendText/{$instanceName}", [
                    'number' => $this->message->phone,
                    'options' => [
                        'delay' => 1200,
                        'presence' => 'composing',
                        'linkPreview' => false,
                    ],
                    'textMessage' => [
                        'text' => $content,
                    ],
                ]);

            if ($response->successful()) {
                $this->message->update([
                    'status' => WhatsAppStatus::Sent,
                    'sent_at' => now(),
                    'attempts' => $this->message->attempts + 1,
                ]);
                Log::channel('whatsapp')->info('WhatsApp message sent', [
                    'message_id' => $this->message->id,
                    'phone' => $this->message->phone,
                    'template' => $this->message->template,
                    'queue' => $this->queue,
                ]);
                $this->delete(); // Explicitly delete job from queue
            } else {
                $error = $response->json('error', $response->body());
                $this->message->update([
                    'status' => WhatsAppStatus::Failed,
                    'error_message' => $error,
                    'attempts' => $this->message->attempts + 1,
                ]);
                Log::channel('whatsapp')->error('WhatsApp message failed', [
                    'message_id' => $this->message->id,
                    'phone' => $this->message->phone,
                    'response' => $error,
                    'queue' => $this->queue,
                ]);
                $this->fail();
            }
        } catch (\Exception $e) {
            $this->message->update([
                'status' => WhatsAppStatus::Failed,
                'error_message' => $e->getMessage(),
                'attempts' => $this->message->attempts + 1,
            ]);
            Log::channel('whatsapp')->error('WhatsApp message exception', [
                'message_id' => $this->message->id,
                'phone' => $this->message->phone,
                'error' => $e->getMessage(),
                'queue' => $this->queue,
            ]);
            $this->fail();
        }
    }

    public function failed(?\Throwable $exception = null): void
    {
        $this->message->update([
            'status' => WhatsAppStatus::Failed,
            'error_message' => 'Max retries exceeded: '.($exception ? $exception->getMessage() : 'Unknown error'),
        ]);

        Log::channel('whatsapp')->critical('WhatsApp message permanently failed', [
            'message_id' => $this->message->id,
            'phone' => $this->message->phone,
            'queue' => $this->queue,
        ]);
    }
}
