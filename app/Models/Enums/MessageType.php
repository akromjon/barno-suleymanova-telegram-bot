<?php

namespace App\Models\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case PHOTO = 'photo';
    case AUDIO = 'audio';
    case VOICE = 'voice';
    case VIDEO = 'video';
    case VIDEO_NOTE='video_note';
    case ANIMATION = 'animation';
    case DOCUMENT = 'document';
    case STICKER = 'sticker';
    case LOCATION = 'location';
    case VENUE = 'venue';
    case CONTACT = 'contact';
    case POLL = 'poll';
    case INVOICE = 'invoice';
    case DICE = 'dice';
    case MEDIA_GROUP = 'media_group';
    case GAME = 'game';
    case CALLBACK_QUERY = 'callback_query';
    case EDITED_MESSAGE = 'edited_message';
    case CHANNEL_POST = 'channel_post';
    case UNSUPPORT='unsupport';

    case COPY_MESSAGE='copy_message';

}
