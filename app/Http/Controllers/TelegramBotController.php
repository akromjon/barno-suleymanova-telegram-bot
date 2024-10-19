<?php

namespace App\Http\Controllers;

use App\Models\Enums\TelegramUserRole;
use App\Models\TelegramUser;
use App\Telegram\FSM\CallbackQueryFSM;
use App\Telegram\FSM\CommandFSM;
use App\Telegram\FSM\MessageFSM;
use App\Telegram\FSM\NotificationFSM;
use App\Telegram\Telegram;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;



class TelegramBotController extends Controller
{
    public function handleWebhook(): JsonResponse
    {
        $user = getUser();

        if ($user !== null && TelegramUserRole::ADMIN === $user->role) {
            $this->handleAdmin(user: $user);
        }
        // $path = storage_path("app/public/media/346133224_AgADRmQAAtY-eEg.mp4");

        // $input = InputFile::create(file: $path);

        // $params = [
        //     'video_note' => InputFile::create(file: $path),
        //     'caption' => $input->getFile(),
        //     'chat_id' => app(abstract: 'telegramUser')->chat_id,
        // ];

        // Telegram::copyMessage(params: [
        //     'chat_id' => app(abstract: 'telegramUser')->chat_id,
        //     'from_chat_id'=>app(abstract: 'telegramUser')->chat_id,
        //     'message_id'=>app(abstract: 'telegramUser')->lastMessage->message_id,
        //     // 'text' => 'you will receive message after 5 seconds',
        // ]);

        // Telegram::sendMessage(params: [
        //     'chat_id' => app(abstract: 'telegramUser')->chat_id,
        //     // 'from_chat_id'=>app(abstract: 'telegramUser')->chat_id,
        //     // 'message_id'=>app(abstract: 'telegramUser')->lastMessage->message_id,
        //     'text' => 'you will receive message after 5 seconds'.app(abstract: 'telegramUser')->lastMessage->message_id,
        // ]);

        // Telegram::sendVideoNote(params: $params);

        /**
         * we response 200 for every request, and keep any errors in the log
         */
        return response()->json(data: [
            'status' => 'ok'
        ]);
    }

    protected function handleAdmin(TelegramUser $user): void
    {
        $type = $this->objectType(update: getWebhookUpdate());

        match ($type) {
            'message' => MessageFSM::handle($type),
            'callback_query' => CallbackQueryFSM::handle($type),
            'command' => CommandFSM::handle($type),
            'sending_notification' => NotificationFSM::handle($type),
            default => Log::error(message: 'Unknown message type returned from TelegramBotController'),
        };


    }
}
