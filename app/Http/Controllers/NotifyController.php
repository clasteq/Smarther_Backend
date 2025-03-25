<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class NotifyController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function sendPushNotification()
    {
        $tokens = [
            'dbNG36-ZQjS2XP4EDUis9j:APA91bFrrHJhOkFNSOQiPv2eszeZHoaQWPzcduLMW3AESQEAITZCameO2Zo67RfU7GDwNdQ4ci54E6iHl7C8vOehv0oQuNRYmiyh5pd4Cx6yN1aENA9ks9Q', 
            'dStmFmJtQ56azzAn3WPoq3:APA91bFF17mW8QrcDzXlpyrlcgi78TcYzdNcUvldo5Rolzmn4wkZV2bizHjFURsD1080s5dHd0RpgYsA2n7_3CXw2z2i-DMyKWEO9yxd0Oi_DFnAoMeGbSk', 
            'doP-aLp_TbGLvSQfnUvHHi:APA91bHatetYbtkITF7NvElnHAzCAGn-zg9_dLGFXLbyxVAaAFFCKUPiO8MKat44xGLoMaHlm8XcWCeHyyheXDjTseZxWVeWZjSfHv4jmv9HxlzX7WB8AY4'
        ]; // Replace with actual FCM tokens

        $title = "New Update!";
        $body  = "Check out the latest features in our app.";

        $data = [
            "screen" => "home",
            "extra_info" => "value"
        ];

        // Send Notification
        $response = $this->firebaseService->sendNotification($tokens, $title, $body, $data);

        return response()->json([
            'success' => $response->successes()->count(),
            'failure' => $response->failures()->count(),
            'responses' => $response->jsonSerialize(),
        ]);
    }
}
