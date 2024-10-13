<?php

use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;
use Filament\Facades\Filament;
use App\Models\TelegramUser;



if (!function_exists(function: 'getWebhookUpdate')) {
    function getWebhookUpdate(): Update
    {
        return Telegram::getWebhookUpdate();
    }
}

if (!function_exists(function: 'adminCheck')) {
    function adminCheck(): bool
    {
        return Filament::auth()->check();
    }
}

if (!function_exists(function: 'telegramUser')) {
    function telegramUser(): TelegramUser
    {
        return app(abstract: 'telegramUser');
    }
}
