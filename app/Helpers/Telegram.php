<?php

use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;
use Filament\Facades\Filament;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Cache;

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


if (!function_exists(function: 'is_active')) {
    function is_active(string $path): string
    {
        return str_contains(haystack: request()->path(), needle: $path) ? 'active' : '';
    }
}

if (!function_exists(function: 'getUser')) {
    function getUser(): ?TelegramUser
    {
        return app()->bound(abstract: 'telegramUser') ? app(abstract: 'telegramUser') : null;
    }
}

if (!function_exists(function: 'cacheUserAction')) {
    function setCacheUserAction(string $value): void
    {
        $user = getUser();

        Cache::set(key: "user_action:{$user->chat_id}", value: $value, ttl: 600);
    }
}

if (!function_exists(function: 'getCachedUserAction')) {
    function getCachedUserAction(): ?string
    {
        $user = getUser();

        return Cache::get(key: "user_action:{$user->chat_id}", default: null);
    }
}

if (!function_exists(function: 'clearUserAction')) {
    function clearUserAction(): ?string
    {
        $user = getUser();

        return Cache::forget(key: "user_action:{$user->chat_id}");
    }
}

