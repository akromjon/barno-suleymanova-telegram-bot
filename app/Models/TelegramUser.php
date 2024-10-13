<?php

namespace App\Models;

use App\Models\Enums\TelegramUserChatStatus;
use App\Models\Enums\TelegramUserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'chat_status' => TelegramUserChatStatus::class,
            'status' => TelegramUserStatus::class
        ];
    }
}
