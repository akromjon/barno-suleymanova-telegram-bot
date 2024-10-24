<?php

declare(strict_types=1);


namespace App\Jobs;

use App\Models\Enums\TelegramUserChatStatus;
use App\Models\Enums\TelegramUserRole;
use App\Models\Enums\TelegramUserStatus;
use App\Models\TelegramNotification;
use App\Models\TelegramUser;
use App\Telegram\Telegram;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use React\EventLoop\Factory;
use React\EventLoop\Loop;
use Fiber;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Collection;

class CopyMessageJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected TelegramNotification $notification)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notification = $this->notification;

        $sendTo = $this->notification->send_to;

        $query = TelegramUser::where(column: 'chat_status', operator: TelegramUserChatStatus::ACTIVE);

        if (in_array(needle: 'subscribed', haystack: $sendTo)) {
            $query = $query->where(column: 'subscribed', operator: true);
        }

        $query = $query->whereNot(column: 'chat_id', operator: $notification->from_chat_id);

        $query->where(column: 'status', operator: TelegramUserStatus::ACTIVE)
            ->chunk(count: 30, callback: function (Collection $users) use ($notification): void {

                $client = new Client();

                $botToken = config(key: 'telegram.bots.mybot.token');

                // Collect all user data for promises in one go
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

                // Resolve all promises at once
                $responses = Utils::settle(promises: $promises)->wait();

                $failedResponses = array_filter(array: $responses, callback: function ($response) {
                    return $response['state'] === 'rejected';
                });

                // Use array_map to process failed responses and extract necessary data
                $failedData = array_map( function ($chat_id, $response): array {
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

                // Dispatch the job to handle the responses
                echo "Batch of 30 messages processed.\n";
            });
    }

}
