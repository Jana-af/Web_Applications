<?php

namespace App\ThirdPartyServices;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FCMService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'));

        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification(array $tokens, $title, $body, $data = [])
    {
        $message = CloudMessage::new()
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ])
            ->withData($data);

        $response = $this->messaging->sendMulticast($message, $tokens);

        return $response;
    }
}
