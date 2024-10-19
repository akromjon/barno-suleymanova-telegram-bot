<?php

namespace App\Models\Enums;

enum TelegramUserRole: string
{
    case USER = 'user';
    case ADMIN = 'admin';
    case SUPERADMIN = 'superadmin';
}
