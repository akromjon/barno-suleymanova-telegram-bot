<?php

namespace App\Telegram\FSM;

use App\Models\TelegramUser;
use App\Telegram\FSM\Base;
use App\Telegram\Menu\Menu;
use App\Telegram\Telegram;
class CommandFSM extends Base
{
    protected function route(): void
    {
        match ($this->message) {
            '/start' => $this->handleStart(),
            default => null
        };
    }

    protected function handleStart(): void
    {
        $user = getUser();

        Telegram::sendMessage(params: Menu::main(chatId: $user->chat_id));
    }



}
