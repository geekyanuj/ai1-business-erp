<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public static function notify($userIds, $type, $message)
    {
        foreach ((array) $userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type'    => $type,
                'message' => $message,
            ]);
        }
    }
}
