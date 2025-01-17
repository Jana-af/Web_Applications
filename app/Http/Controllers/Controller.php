<?php

namespace App\Http\Controllers;

use App\ThirdPartyServices\FCMService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test()
    {
        $firebaseService = new FCMService();
        $deviceToken = 'fdCR6fK_VJDUgRkTyTUc7a:APA91bFoxIP1l4cxdgiZSBeljy9sUcJZAi4Yw7HdIjgDeo3xxDLdcvPVEY9gH2xRTjTLmCjBpeM5VRgoXjrWhP-kHhx9m9PcoID5AS7exxtL0ZXCd3aH5OI';
        $response = $firebaseService->sendNotification(
            [$deviceToken, $deviceToken],
            'Test Notification',
            'This is a test push notification!',
            ['key' => 'value'] // Optional additional data
        );

        return response()->json(['success' => $response]);
    }
}
