<?php

namespace App\Services;

use App\Enums\WhatsAppQueueType;
use App\Enums\WhatsAppStatus;
use App\Jobs\SendWhatsAppMessage;
use App\Models\User;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $allowMultipleTemplates = [
        'otp_verification',
        'phone_changed_notification',
        'referral_reward_issued',
        'user_registered',
        'new_booking_admin',
        'booking_status_changed_admin',
        'user_blocked_admin',
        'shop_applied',
        'user_security_changed_admin',
        'booking_created_barber',
        'booking_cancelled_owner',
        'account_blocked_cancellation',
        'account_blocked_no_show',
        'booking_cancelled_client',
        'booking_confirmed_client',
        'booking_reminder_client',
        'booking_review_request',
        'no_show_warning',
        'booking_arrived_client',
    ];

    public function sendMessage(string $phone, string $template, array $data, string $priority = 'default', ?int $userId = null, ?int $shopId = null)
    {
        // Clean phone number
        $phone = $this->formatPhoneNumber($phone);

        // Global Preference Check: Skip if user has disabled WhatsApp notifications
        // EXCEPTION: Always allow 'otp_verification' so users aren't locked out of their accounts
        if ($template !== 'otp_verification') {
            $user = $userId ? User::find($userId) : User::where('phone', $phone)->first();
            if ($user && !$user->whatsapp_notifications) {
                Log::channel('whatsapp')->info('WhatsApp message skipped: User disabled notifications', [
                    'phone' => $phone,
                    'template' => $template,
                    'user_id' => $user->id,
                ]);

                return false;
            }
        }

        // Determine if this is OTP or instant priority message
        $isInstant = ($priority === 'instant' || $template === 'otp_verification');
        $isUrgent = ($priority === 'urgent');

        // Skip cache lock and duplicate check for specific templates
        $allowMultiple = in_array($template, $this->allowMultipleTemplates);

        // INSTANT messages (OTP) skip ALL checks and locks
        if ($isInstant) {
            return $this->sendInstantMessage($phone, $template, $data, $userId, $shopId);
        }

        // Normal flow for urgent and default messages
        $lockKey = "whatsapp_lock_{$phone}_{$template}";
        $lockAcquired = $allowMultiple || Cache::lock($lockKey, 300)->get();

        if ($lockAcquired) {
            // Check for duplicates within 5 hours for non-allowed templates
            if (!$allowMultiple) {
                $recentMessage = WhatsAppMessage::where('phone', $phone)
                    ->where('template', $template)
                    ->whereIn('status', [WhatsAppStatus::Queued, WhatsAppStatus::Sent])
                    ->where('created_at', '>=', now()->subHours(5))
                    ->exists();

                if ($recentMessage) {
                    Log::channel('whatsapp')->warning('Duplicate message skipped', [
                        'phone' => $phone,
                        'template' => $template,
                    ]);
                    Cache::lock($lockKey)->release();

                    return false;
                }
            }

            // Determine queue type
            $queueType = $isUrgent ? WhatsAppQueueType::Urgent : WhatsAppQueueType::Default;

            // Create message record
            $message = WhatsAppMessage::create([
                'user_id' => $userId,
                'shop_id' => $shopId,
                'phone' => $phone,
                'template' => $template,
                'queue_type' => $queueType,
                'data' => $data,
                'status' => WhatsAppStatus::Queued,
                'attempts' => 0,
            ]);

            // Calculate delay based on priority
            if ($isUrgent) {
                $delayGap = random_int(60, 120); // 1-2 minutes for urgent
                $queue = 'urgent';
            } else {
                $delayGap = random_int(300, 600); // 5-10 minutes for default
                $queue = 'default';
            }

            // Sequential scheduling to avoid rate limits
            $lastScheduledKey = $isUrgent
                ? 'whatsapp_last_urgent_time'
                : 'whatsapp_last_normal_time';

            $lockSchedule = Cache::lock('whatsapp_schedule_lock_' . $isUrgent, 10);

            if ($lockSchedule->get()) {
                $lastScheduledTime = Cache::get($lastScheduledKey, now()->timestamp);
                $now = now()->timestamp;

                // If last scheduled time is in the past, start from now
                $baseTime = max($lastScheduledTime, $now);

                // Add the delay gap
                $scheduledTime = $baseTime + $delayGap;

                // Store the new scheduled time
                Cache::put($lastScheduledKey, $scheduledTime, 3600);
                $lockSchedule->release();

                // Calculate actual delay from now
                $delaySeconds = $scheduledTime - $now;
            } else {
                // Fallback: simple random delay
                $delaySeconds = $delayGap;
            }

            // Dispatch job with calculated delay
            $delay = now()->addSeconds($delaySeconds);
            SendWhatsAppMessage::dispatch($message)->onQueue($queue)->delay($delay);

            Log::channel('whatsapp')->info('WhatsApp message queued', [
                'message_id' => $message->id,
                'user_id' => $userId,
                'shop_id' => $shopId,
                'phone' => $phone,
                'template' => $template,
                'queue' => $queue,
                'delay_seconds' => $delaySeconds,
            ]);

            if (!$allowMultiple) {
                Cache::lock($lockKey)->release();
            }

            return true;
        }

        Log::channel('whatsapp')->warning('Message blocked by cache lock', [
            'phone' => $phone,
            'template' => $template,
        ]);

        return false;
    }

    protected function sendInstantMessage(string $phone, string $template, array $data, ?int $userId = null, ?int $shopId = null): bool
    {
        // Create message record
        $message = WhatsAppMessage::create([
            'user_id' => $userId,
            'shop_id' => $shopId,
            'phone' => $phone,
            'template' => $template,
            'queue_type' => WhatsAppQueueType::Instant,
            'data' => $data,
            'status' => WhatsAppStatus::Queued,
            'attempts' => 0,
        ]);

        // Dispatch to INSTANT queue with NO delay
        SendWhatsAppMessage::dispatch($message)
            ->onQueue('instant');

        Log::channel('whatsapp')->info('Instant WhatsApp message queued', [
            'message_id' => $message->id,
            'user_id' => $userId,
            'shop_id' => $shopId,
            'phone' => $phone,
            'template' => $template,
            'queue' => 'instant',
            'delay' => 0,
        ]);

        return true;
    }

    public function sendBulkMessages(array $recipients, string $template, callable $dataCallback)
    {
        $batchSize = 100; // Process 100 recipients per batch
        $batches = array_chunk($recipients, $batchSize);
        $baseDelay = 0; // Cumulative delay for sequential processing

        foreach ($batches as $index => $batch) {
            foreach ($batch as $recipientIndex => $recipient) {
                $phone = $this->formatPhoneNumber($recipient['student_phone']);
                $data = $dataCallback($recipient);

                // Cache lock for bulk messages (5-minute lock)
                $lockKey = "whatsapp_lock_{$phone}_{$template}";
                if (Cache::lock($lockKey, 300)->get()) {
                    // Check for duplicates within 24 hours
                    $recentMessage = WhatsAppMessage::where('phone', $phone)
                        ->where('template', $template)
                        ->whereIn('status', [WhatsAppStatus::Queued, WhatsAppStatus::Sent])
                        ->where('created_at', '>=', now()->subHours(24))
                        ->exists();

                    if ($recentMessage) {
                        Log::channel('whatsapp')->warning('Duplicate bulk message skipped', [
                            'phone' => $phone,
                            'template' => $template,
                        ]);
                        Cache::lock($lockKey)->release();

                        continue;
                    }

                    // Add is_urgent to data
                    $data['is_urgent'] = false;

                    $message = WhatsAppMessage::create([
                        'phone' => $phone,
                        'template' => $template,
                        'data' => $data,
                        'status' => WhatsAppStatus::Queued,
                        'attempts' => 0,
                    ]);

                    // Stagger delays: 5-10 minutes per message
                    $delayedSeconds = random_int(300, 600);
                    $delay = now()->addSeconds($baseDelay + $delayedSeconds);

                    // Dispatch with staggered delay
                    SendWhatsAppMessage::dispatch($message)
                        ->onQueue('default')
                        ->delay($delay);

                    Log::channel('whatsapp')->info('WhatsApp bulk message queued', [
                        'message_id' => $message->id,
                        'phone' => $phone,
                        'template' => $template,
                        'queue' => 'default',
                        'delay_seconds' => $baseDelay + $delayedSeconds,
                    ]);

                    Cache::lock($lockKey)->release();

                    // Increment base delay for next message
                    $baseDelay += $delayedSeconds;
                } else {
                    Log::channel('whatsapp')->warning('Bulk message blocked by cache lock', [
                        'phone' => $phone,
                        'template' => $template,
                    ]);
                }
            }
            // Add batch delay
            $baseDelay += 60;
        }

        Log::channel('whatsapp')->info('WhatsApp bulk messages queued', [
            'template' => $template,
            'recipient_count' => count($recipients),
        ]);

        return true;
    }

    protected function formatPhoneNumber(string $phone): string
    {
        $phone = str_replace([' ', '-', '(', ')'], '', $phone);

        if (preg_match('/^\+20[0-9]{10}$/', $phone)) {
            return $phone;
        }

        $phone = preg_replace('/^\+?20/', '', $phone);
        $phone = ltrim($phone, '0');

        if (strlen($phone) !== 10) {
            return $phone;
        }

        return '+20' . $phone;
    }
}
