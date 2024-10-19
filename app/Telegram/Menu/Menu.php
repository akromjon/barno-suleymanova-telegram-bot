<?php

namespace App\Telegram\Menu;

use Telegram\Bot\Keyboard\Keyboard;
use App\Telegram\Telegram;

class Menu
{
    private static function makeKeyboardButton(): Keyboard
    {
        return Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false);
    }

    private static function makeInlineKeyboard(): Keyboard
    {
        return Keyboard::make()
            ->inline()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false);
    }
    public static function main(int $chatId): array
    {
        $text = null;

        $keyboard = self::makeKeyboardButton()
            ->row(buttons: [
                Keyboard::button(params: '📢 Bildirishnoma Yuborish'),
            ]);

        return [
            'chat_id' => $chatId,
            'type' => 'message',
            'reply_markup' => $keyboard,
            'text' => $text ?? '🏠 Asosiy Menyu 👇',
            'parse_mode' => 'HTML',
        ];
    }

    public static function sendingNotification(int $chatId): array
    {
        return [
            'text' => '👇 Bildirishnomani Qoldiring!',
            'chat_id' => $chatId,
        ];
    }

    public static function copyMessage(array $params): array
    {
        $inlineButton = self::makeInlineKeyboard();

        $inlineButton->row(buttons: [
            Keyboard::inlineButton(params: [
                'text' => '👥 Barchaga Yuborish',
                'callback_data' => json_encode(value: [
                    'm' => 'N',
                    's' => 'all',
                    'i' => $params['notification_id']
                ]),
            ]),
        ])->row(buttons: [
            Keyboard::inlineButton(params: [
                'text' => '💎 Obunachilarga Yuborish',
                'callback_data' => json_encode(value: [
                    'm' => 'N',
                    's' => 'sub',
                    'i' => $params['notification_id'],
                ]),
            ]),
        ]);

        return [
            ...$params,
            'reply_markup' => $inlineButton,
        ];
    }

    public static function notificationDeletedOrNotExist(int $chatId): array
    {
        return [
            'chat_id' => $chatId,
            'text' => 'Message was deleted or does not exists in the DB',
        ];
    }

    public static function sendNotificationMessage(int $chatId): array
    {
        return [
            'chat_id' => $chatId,
            'text' => '▶️ Bildirishnomani Yuborish Boshlandi!',
        ];
    }
}
