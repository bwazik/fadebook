<?php

namespace App\Notifications\Channels;

use App\Services\SettingsService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class FcmChannel
{
    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     */
    public function send($notifiable, Notification $notification): void
    {
        // Early exit if FCM notifications are disabled globally
        $isFcmEnabled = filter_var(app(SettingsService::class)->get('fcm_enabled', 'true'), FILTER_VALIDATE_BOOLEAN);
        if (! $isFcmEnabled) {
            return;
        }

        // Check if the user has an FCM token
        if (! $notifiable->fcm_token) {
            return;
        }

        // The notification class must implement a `toFcm` method (or fallback to toDatabase)

        /** @var mixed $mixedNotification */
        $mixedNotification = $notification;

        if (! method_exists($notification, 'toFcm')) {
            if (! method_exists($notification, 'toDatabase')) {
                return;
            }
            // Fallback logic using standard database structure if toFcm isn't defined explicitly
            $data = $mixedNotification->toDatabase($notifiable);

            $title = $data['title'] ?? 'GymZ';
            $body = $data['message'] ?? 'You have a new notification';
            $url = $data['url'] ?? '/';
            $type = $data['type'] ?? 'info';

        } else {
            // Alternatively, they can return custom array/object here
            $data = $mixedNotification->toFcm($notifiable);
            $title = $data['title'];
            $body = $data['body'];
            $url = $data['url'] ?? '/';
            $type = $data['type'] ?? 'info';
        }

        try {
            // Construct the push message
            $message = CloudMessage::new()
                ->withToken($notifiable->fcm_token)
                ->withNotification(FcmNotification::create($title, $body))
                ->withData([
                    'url' => $url,
                    'type' => $type,
                ]);

            $this->messaging->send($message);
        } catch (\Exception $e) {
            Log::error('FCM Push Notification failed: '.$e->getMessage(), [
                'user_id' => $notifiable->id,
                'fcm_token' => $notifiable->fcm_token,
            ]);
        }
    }
}
