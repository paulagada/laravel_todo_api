<?php

// app/Services/FCMService.php
namespace App\Services;

use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;

class FCMService
{
    /**
     * Send a notification to a list of device tokens.
     * Returns an array with summary: ['sent'=>int,'failed'=>int,'invalid'=> array]
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $result = ['sent' => 0, 'failed' => 0, 'invalid' => []];

        if (empty($tokens)) {
            return $result;
        }

        $messaging = Firebase::messaging();

        // Build message (notification + data)
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            // FCM requires string type for data values: cast
            ->withData(array_map('strval', $data));

        // FCM allows up to 500 tokens per multicast request — chunk to 500
        $chunks = array_chunk($tokens, 500);
        foreach ($chunks as $chunk) {
            try {
                $report = $messaging->sendMulticast($message, $chunk);

                $result['sent'] += $report->successes()->count();
                $result['failed'] += $report->failures()->count();

                // Clean up invalid tokens returned by the SDK
                $invalidTokens = $report->invalidTokens(); // array of tokens
                if (!empty($invalidTokens)) {
                    DeviceToken::whereIn('token', $invalidTokens)->delete();
                    $result['invalid'] = array_merge($result['invalid'], $invalidTokens);
                }

                // Optionally handle unknownTokens() or other reports:
                $unknown = $report->unknownTokens();
                if (!empty($unknown)) {
                    // tokens registered to other projects or stale — handle if needed
                    Log::info('FCM unknown tokens', $unknown);
                }
            } catch (\Throwable $e) {
                Log::error('FCM send error: '.$e->getMessage(), ['exception' => $e]);
            }
        }

        return $result;
    }
}
