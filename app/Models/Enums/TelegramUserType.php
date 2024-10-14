<?php

namespace App\Models\Enums;

enum TelegramUserType: string
{
    case PRIVATE = 'private';
    case GROUP = 'group';
    case SUPERGROUP = "supergroup";
    case CHANNEL = "channel";
}
