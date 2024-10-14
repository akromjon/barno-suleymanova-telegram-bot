<?php

namespace App\Models\Enums;

enum MessageSender: string
{
    case BOT = 'bot';
    case TELEGRAM_USER = 'telegram_user';
}
