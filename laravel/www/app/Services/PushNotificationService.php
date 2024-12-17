<?php

namespace app\Services;

use App\DTOs\CustomNotificationDTO;
use App\Models\User;
use App\Notifications\PushNotification;

/*
 * Example Usage
 * $user = User::find(1); // Find a user by ID
 * PushNotificationService::sendToUser($user, 'request_accepted');
*/

class PushNotificationService
{
    public static function sendToUser(User $user, string $notificationType)
    {
        $notifications = [
            'new_message' => [
                'title' => 'New Message Received',
                'body' => 'You have a new message in your inbox.',
                //'icon' => url('/icons/message.png'),
            ],
            'request_accepted' => [
                'title' => 'Request Accepted',
                'body' => 'Your request has been accepted.',
                //'icon' => url('/icons/request_accepted.png'),
            ],
            'default' => [
                'title' => 'Notification',
                'body' => 'You have a new notification.',
                //'icon' => url('/icons/default.png'),
            ],
        ];

        // Determine notification data based on $notificationType
        $notificationData = $notifications[$notificationType] ?? $notifications['default'];

        // Send notifications if the user has subscriptions
        if ($user->pushSubscriptions()->count()) {
            $user->notify(new PushNotification(
                $notificationData['title'],
                $notificationData['body'],
                //$notificationData['icon']
            ));
        }
    }

    public static function sendCustomNotification(User $user, CustomNotificationDTO $notificationData)
    {
        if ($user->pushSubscriptions()->count()) {
            $user->notify(new PushNotification(
                $notificationData->title,
                $notificationData->message,
                $notificationData->icon ?? null
            ));
        }
    }
}
