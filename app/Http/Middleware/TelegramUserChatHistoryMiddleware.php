<?php

namespace App\Http\Middleware;

use App\Models\Chat;
use App\Models\Enums\ChatStatus;
use App\Models\Enums\MessageSender;
use App\Models\Enums\TelegramUserStatus;
use App\Models\TelegramUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramUserChatHistoryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    private function checkIfUserIsInactive(TelegramUser $user): bool
    {
        return TelegramUserStatus::INACTIVE === $user->status;
    }

    private function chatExists(?Chat $chat): bool
    {
        return null!==$chat;
    }

    private function createChat(TelegramUser $user): Chat
    {
        return $user->chat()->create(attributes: [
            'status' => ChatStatus::ACTIVE,
            'last_messaged_at' => now(),
        ]);
    }

    private function updateLastMessageAt(Chat $chat): void
    {
        $chat->update(attributes: [
            'last_messaged_at' => now(),
        ]);

    }

    private function createMessage(Chat $chat, TelegramUser $user): void
    {
        $chat->messages()->create(attributes: [
            'telegram_user_id' => $user->id,
            'message_id'=>getWebhookUpdate()->getMessage()->getMessageId(),
            'sender' => MessageSender::BOT,
            'type' => 'text',
            'text' => getWebhookUpdate()->getMessage()->getText(),
        ]);
    }

    public function terminate(Request $request, Response $response): void
    {

        $user = app(abstract: 'telegramUser');

        if ($this->checkIfUserIsInactive(user: $user))
            return;

        $chat = $user->chat;

        if (false === $this->chatExists(chat: $chat)) {

            $chat = $this->createChat(user: $user);

            return;
        }

        $this->updateLastMessageAt(chat: $chat);

        $this->createMessage(chat: $chat, user: $user);

    }




}
