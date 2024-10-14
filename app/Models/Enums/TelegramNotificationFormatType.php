<?php

namespace App\Models\Enums;

enum TelegramNotificationFormatType: string
{
    case HTML = 'HTML';
    case MARKDOWN = 'Markdown';
}
