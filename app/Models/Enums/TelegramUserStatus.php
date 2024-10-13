<?php

namespace App\Models\Enums;

enum TelegramUserStatus: string
{
    case ACTIVE = "active";
    case INACTIVE = 'inactive';
}
