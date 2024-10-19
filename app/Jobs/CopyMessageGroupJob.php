<?php

namespace App\Jobs;

use App\Models\Enums\TelegramUserChatStatus;
use App\Models\Enums\TelegramUserStatus;
use App\Models\TelegramNotification;
use App\Models\TelegramUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Collection;

class CopyMessageGroupJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Collection $notifications)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $notifications = $this->notifications;

        $chunkCount = 30 / $notifications->count();

        $botToken = config(key: 'telegram.bots.mybot.token');

        TelegramUser::where(column: 'chat_status', operator: TelegramUserChatStatus::ACTIVE)
            ->chunk(count: $chunkCount, callback: function (Collection $users) use ($notifications, $botToken): void {


                foreach ($notifications as $notification) {

                    $client = new Client();

                    // Collect promises from all users and notifications
                    $promises = collect(value: $users)->mapWithKeys(callback: function (TelegramUser $user) use ($client, $botToken, $notification) {
                        // Prepare a promise for each user in one step
                        return [
                            $user->chat_id => $client->postAsync(uri: "https://api.telegram.org/bot{$botToken}/copyMessage", options: [
                                'form_params' => [
                                    'from_chat_id' => $notification->from_chat_id,
                                    'chat_id' => $user->chat_id,
                                    'message_id' => $notification->message_id,
                                ],
                            ])
                        ];
                    })->toArray();

                    $responses = Utils::settle(promises: $promises)->wait();

                    $failedResponses = array_filter(array: $responses, callback: function ($response) {
                        return $response['state'] === 'rejected';
                    });

                    // Use array_map to process failed responses and extract necessary data
                    $failedData = array_map(function ($chat_id, $response): array {
                        $reason = $response['reason'];
                        $statusCode = ($reason instanceof RequestException && $reason->hasResponse())
                            ? $reason->getResponse()->getStatusCode()
                            : null;

                        // Extract the response body if available
                        $responseBody = ($reason instanceof RequestException && $reason->hasResponse())
                            ? (string) $reason->getResponse()->getBody()
                            : null;

                        return [
                            'chat_id' => $chat_id,
                            'status_code' => $statusCode,
                            'error_message' => $reason->getMessage(),
                            'response_body' => $responseBody,  // Add the response body here
                        ];
                    }, array_keys($failedResponses), $failedResponses);  // Pass chat_id as the key

                    // Only dispatch the job if there are failed responses
                    HandleTelegramUserFromGuzzleResponse::dispatch($failedData);

                }

            });

    }

}
