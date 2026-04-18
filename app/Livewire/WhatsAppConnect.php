<?php

namespace App\Livewire;

use App\Traits\WithToast;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

class WhatsAppConnect extends Component
{
    use WithToast;

    public $qr = null;

    public $connected = false;

    public function fetchQr()
    {
        $this->qr = null;

        $url = config('services.evolution.url');
        $apikey = config('services.evolution.key');
        $instanceName = config('services.evolution.instance');

        $headers = [
            'apikey' => $apikey,
        ];

        try {
            $stateResponse = Http::withHeaders($headers)
                ->get("{$url}/instance/connectionState/{$instanceName}");

            $isNotFound = $stateResponse->status() === 404 ||
                (isset($stateResponse->json()['error']) && str_contains(strtolower($stateResponse->json()['error']), 'not found'));

            if (!$isNotFound && $stateResponse->successful()) {
                $stateData = $stateResponse->json();
                $state = $stateData['instance']['state'] ?? $stateData['state'] ?? 'unknown';

                if ($state === 'open') {
                    $this->connected = true;
                    $this->toastSuccess('متصل بنجاح');

                    return;
                }

                $connectResponse = Http::withHeaders($headers)
                    ->get("{$url}/instance/connect/{$instanceName}");

                if ($connectResponse->successful()) {
                    $this->qr = $connectResponse->json('base64');
                    $this->toastSuccess('تم الإتصال بالخادم لجلب الكود');

                    return;
                }

                $this->toastError('فشل الاتصال بالواتساب الخاص بك');

                return;
            }

            $createResponse = Http::withHeaders($headers)
                ->post("{$url}/instance/create", [
                    'instanceName' => $instanceName,
                    'qrcode' => true,
                    'integration' => 'WHATSAPP-BAILEYS',
                ]);

            if ($createResponse->successful()) {
                $createData = $createResponse->json();
                $this->qr = $createData['qrcode']['base64'] ?? $createData['base64'] ?? null;
                $this->toastSuccess('تم الإتصال بالخادم لجلب الكود');

                return;
            }

            $this->toastError('فشل إنشاء الإتصال');

        } catch (\Exception $e) {
            Log::error('WhatsApp connection error: ' . $e->getMessage());
            $this->toastError('حدث خطأ أثناء محاولة الاتصال بالخادم');
        }
    }

    #[Layout('components.layout.app')]
    public function render()
    {
        return view('livewire.whatsapp-connect');
    }
}
