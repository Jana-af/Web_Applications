<?php

namespace App\ThirdPartyServices;

class NotificationService
{
    public static function send(array $deviceTokens, $title, $body, $data)
    {
        $firebaseService = new FCMService();

        $response = $firebaseService->sendNotification(
            $deviceTokens,
            $title,
            $body,
            $data // Optional additional data
        );

        response()->json(['success' => $response]);
    }
}
