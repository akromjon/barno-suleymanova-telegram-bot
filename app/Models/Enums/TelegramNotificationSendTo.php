<?php

namespace App\Models\Enums;

enum TelegramNotificationSendTo: string
{
    case SUBSCRIBED = 'subscribed';
    case ACTIVE = 'active';
    case INACTIVe='inactive';

}
