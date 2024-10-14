<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use App\Models\Enums\TelegramUserStatus;
use App\Models\Enums\MessageSender;
use App\Models\Enums\ChatStatus;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use App\Models\Chat;
use Closure;
class TelegramUserChatHistoryMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $user = $this->getTelegramUser();

        if (!$user || $this->shouldTerminate(user: $user)) return;

        $chat = $this->getOrCreateChat(user: $user);

        $this->updateLastMessageAt(chat: $chat);

        $this->createMessage(chat: $chat, user: $user);
    }

    private function checkIfUserIsInactive(TelegramUser $user): bool
    {
        return TelegramUserStatus::INACTIVE === $user->status;
    }

    private function chatExists(?Chat $chat): bool
    {
        return null !== $chat;
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
            'message_id' => getWebhookUpdate()->getMessage()->message_id ?? 0,
            'sender' => MessageSender::BOT,
            'type' => 'text',
            'text' => getWebhookUpdate()->getMessage()->getText(),
        ]);
    }

    private function getTelegramUser(): ?TelegramUser
    {
        return app()->bound(abstract: 'telegramUser') ? app(abstract: 'telegramUser') : null;
    }

    private function shouldTerminate($user): bool
    {
        return $this->checkIfUserIsInactive(user: $user) || getWebhookUpdate()->objectType() === 'my_chat_member';
    }

    private function getOrCreateChat($user): Chat
    {
        return $this->chatExists(chat: $user->chat) ? $user->chat : $this->createChat(user: $user);
    }





}
