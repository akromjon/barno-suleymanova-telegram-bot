<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Enums\MessageType;
use App\Models\Enums\TelegramUserType;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): Factory|View
    {
        return view(view: 'pages.chat.index');
    }

    public function show(int $chatId): Factory|View
    {


        $chat = Chat::where(column: 'chats.id', operator: $chatId)
            ->firstOrFail();


        return view(view: 'pages.message.show', data: [
            'chat' => $chat,
        ]);
    }


}
