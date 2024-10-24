<?php

declare(strict_types=1);


namespace App\Telegram\FSM;

use App\Telegram\Menu\Menu;
use Illuminate\Support\Facades\Log;
use App\Telegram\Telegram;

class MessageFSM extends Base
{
    public function route(): void
    {
        match ($this->message) {
            '📢 Bildirishnoma Yuborish' => $this->handleNotification(),
            default => Log::error(message: 'Unknown message type returned from MessageFSM'),
        };
    }

    protected function handleNotification(): void
    {
        setCacheUserAction(value: 'sending_notification');

        Telegram::sendMessage(params: Menu::sendingNotification(chatId: getUser()->chat_id));
    }

}
