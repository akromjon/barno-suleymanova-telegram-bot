<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    use HasFactory;


    public function messages(): HasMany
    {
        return $this->hasMany(related: Message::class, foreignKey: 'chat_id', localKey: 'id');
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(related: TelegramUser::class, foreignKey: 'telegram_user_id', ownerKey: 'id');
    }

    public function unReadMessages(): HasMany
    {
        return $this->hasMany(related: Message::class, foreignKey: 'chat_id', localKey: 'id')->where(column: 'is_read', operator: false);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(related: Message::class)->latestOfMany();
    }
}
