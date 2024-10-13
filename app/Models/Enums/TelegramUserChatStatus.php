<?php

namespace App\Models\Enums;

enum TelegramUserChatStatus: string
{
    case ACTIVE = "active";
    case BLOCKED = 'blocked';
}
