<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\TelegramBotController;
use App\Http\Middleware\AdminCheckMiddle;
use App\Http\Middleware\TelegramUserChatHistoryMiddleware;
use App\Http\Middleware\TelegramUserChatStatusUpdateMiddleware;
use App\Http\Middleware\TelegramUserLastUsedAtUpdatorMiddleware;
use App\Http\Middleware\TelegramWebhookAccessMiddleware;
use Illuminate\Contracts\View\Factory;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Telegram\Bot\Objects\WebhookInfo;

Route::get(uri: '/set-webhook', action: function (): JsonResponse {

    if (!(adminCheck())) {

        return response()->json(data: [
            'message' => 'unauthorized',
        ], status: 403);
    }

    $res = Telegram::setWebhook(params: [
        'drop_pending_updates' => true,
        'url' => config(key: 'app.url') . config(key: 'telegram.bots.mybot.webhook_url'),
        'secret_token' => config(key: 'telegram.bots.mybot.secret_token'),
        'max_connections' => config(key: 'telegram.bots.mybot.max_connections'),
    ]);


    return response()->json(data: [
        'res' => $res
    ]);
});

Route::get(uri: '/get-me', action: function (): JsonResponse {

    if (!(adminCheck())) {

        return response()->json(data: [
            'message' => 'unauthorized',
        ], status: 403);
    }

    return response()->json(
        data: [
            'me' => Telegram::getMe()
        ]
    );
});

Route::get(uri: '/get-webhook-info', action: function (): JsonResponse|WebhookInfo {

    if (!(adminCheck())) {

        return response()->json(data: [
            'message' => 'unauthorized',
        ], status: 403);
    }

    return response()->json([
        'info' => Telegram::getWebhookInfo()
    ]);
});



Route::prefix("chats")->middleware([AdminCheckMiddle::class])->group(callback: function (): void {

    Route::get(uri: '/', action: [ChatController::class, 'index'])->name(name: 'chats.index');

    Route::get(uri: '/{chat_id}/messages', action: [ChatController::class, 'show'])->name(name: 'chats.show');

});

Route::post(
    uri: config(key: 'telegram.bots.mybot.webhook_url'),
    action: [TelegramBotController::class, 'handleWebhook']
)
    ->withoutMiddleware(middleware: ['web'])
    ->middleware(middleware: [
        TelegramWebhookAccessMiddleware::class,
        TelegramUserChatStatusUpdateMiddleware::class,
        TelegramUserLastUsedAtUpdatorMiddleware::class,
        TelegramUserChatHistoryMiddleware::class,
    ]);




