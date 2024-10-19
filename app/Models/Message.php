<?php

namespace App\Models;

use App\Models\Enums\MessageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => MessageType::class,
        ];
    }

}
