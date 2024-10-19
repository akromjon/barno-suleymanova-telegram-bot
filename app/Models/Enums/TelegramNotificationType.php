<?php

namespace App\Models\Enums;

enum TelegramNotificationType: string
{
    case TEXT = 'text';
    case PHOTO = 'photo';
    case VIDEO = "video";
    case VOICE = "voice";
    case AUDIO = "audio";
    case DOCUMENT = "document";

    case COPY_MESSAGE = "copy_message";

}
