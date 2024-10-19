<?php

namespace App\Jobs;

use App\Models\Enums\TelegramUserChatStatus;
use App\Models\TelegramUser;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class HandleTelegramUserFromGuzzleResponse implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $responses)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        /**
         *  'chat_id' => $chat_id,
         *  'status_code' => $statusCode,
         *  'error_message' => $reason->getMessage(),
         *  'response_body'
         */
        foreach ($this->responses as $response) {

            if ($this->checkIfKeysExist(response: $response)) {
                continue;
            }

            $user = TelegramUser::where(column: 'chat_id', operator: $response['chat_id'])->first();

            if (null === $user) {
                continue;
            }

            match ($response['status_code']) {
                403 => $this->handle403(user: $user),
                400 => $this->handle400(user: $user, response: $response),
                default => $this->handleDefault(response: $response),
            };
        }
    }

    private function checkIfKeysExist(array $response): bool
    {
        return !array_key_exists(key: 'chat_id', array: $response)
            && !array_key_exists(key: 'status_code', array: $response);
    }

    private function handle400(TelegramUser $user, array $response): void
    {
        $res = json_decode(json: $response['response_body']);

        $newChatId = $res?->parameters?->migrate_to_chat_id;

        if (null === $newChatId) {
            return;
        }


        $checkedUser = TelegramUser::where(column: 'chat_id', operator: $newChatId)->first();

        if (null !== $checkedUser) {

            $user->delete();

            return;
        }

        $user->update(attributes: [
            'chat_id' => $newChatId
        ]);
    }

    private function handle403(TelegramUser $user): void
    {
        $user->update(attributes: [
            'chat_status' => TelegramUserChatStatus::BLOCKED
        ]);
    }

    private function handleDefault(array $response): void
    {
        $response['line'] = '92: handleDefault';

        Log::error(message: json_encode(value: $response));
    }
}
