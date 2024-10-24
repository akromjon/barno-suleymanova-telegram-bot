<?php

declare(strict_types=1);

namespace App\Telegram\FSM;

use App\Models\TelegramNotification;
use App\Telegram\Menu\Menu;
use App\Telegram\Message;
use App\Telegram\Telegram;


class NotificationFSM extends Base
{
    public function route(): void
    {
        $message = Message::handle(update: getWebhookUpdate());

        $notification = TelegramNotification::create(attributes: [
            'name' => 'CopyMessage',
            'text' => $message->getText(),
            'file' => $message->getFile(),
            'from_chat_id' => $message->getChatId(),
            'message_id' => $message->getMessageId(),
            'type' => 'copy_message',
            'is_active' => true,
            'send_to' => ['subscribed', 'active'],
        ]);

        Telegram::copyMessage(params: Menu::copyMessage(params: [
            'from_chat_id' => $message->getChatId(),
            'chat_id' => $message->getChatId(),
            'message_id' => $message->getMessageId(),
            'notification_id'=>$notification->id
        ]));

        clearUserAction();
    }



}
