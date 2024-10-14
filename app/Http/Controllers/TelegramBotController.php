<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    public function handleWebhook(): JsonResponse
    {

        Telegram::sendMessage(params: [
            'chat_id' => app(abstract: 'telegramUser')->chat_id,
            'text' => 'you will receive message after 5 seconds',
        ]);

        /**
         * we response 200 for every request, and keep any errors in the log
         */
        return response()->json(data: [
            'status' => 'ok'
        ]);
    }
}
