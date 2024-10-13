<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class TelegramBotController extends Controller
{
    public function handleWebhook(): JsonResponse
    {

        /**
         * we response 200 for every request, and keep any errors in the log
         */
        return response()->json(data: [
            'status' => 'ok'
        ]);
    }
}
