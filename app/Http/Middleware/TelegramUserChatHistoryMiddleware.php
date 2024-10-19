<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use App\Models\Enums\TelegramUserStatus;
use App\Models\Enums\MessageSender;
use App\Models\Enums\ChatStatus;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Enums\MessageType;
use App\Models\Enums\TelegramUserChatStatus;
use App\Models\Enums\TelegramUserRole;
use App\Models\Enums\TelegramUserType;
use App\Telegram\Message;
use App\Models\Message as MessageModel;
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

        if ($this->checkUserAndTerminate(user: $user)) {
            return;
        }

        $chat = $this->getOrCreateChat(user: $user);

        $this->updateLastMessageAt(chat: $chat);

        $message = Message::handle(update: getWebhookUpdate());

        if (MessageType::UNSUPPORT === $message->getMessageType()) {


            return;
        }

        $this->createOrEditMessage(
            chat: $chat,
            user: $user,
            message: $message
        );
    }

    private function checkUserAndTerminate(?TelegramUser $user): bool
    {
        return null === $user || $this->shouldTerminate(user: $user);
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
            'type' => $user->type,
        ]);
    }

    private function updateLastMessageAt(Chat $chat): void
    {
        $chat->update(attributes: [
            'last_messaged_at' => now(),
        ]);
    }

    private function createOrEditMessage(Chat $chat, TelegramUser $user, Message $message): void
    {
        match ($message->getObjectType()) {
            'message', 'channel_post' => $this->createMessage(chat: $chat, user: $user, message: $message),
            'edited_message', 'edited_channel_post' => $this->editMessage(chat: $chat, user: $user, message: $message),
        };

    }

    private function createMessage(Chat $chat, TelegramUser $user, Message $message): void
    {
        $chat->messages()->create(attributes: [
            'telegram_user_id' => $user->id,
            'message_id' => $message->getMessageId(),
            'sender' => $message->getSender(),
            'type' => $message->getMessageType(),
            'text' => $message->getText(),
            'file' => $message->getFile(),
            'thumb' => $message->getThumb()

        ]);
    }

    private function editMessage(Chat $chat, TelegramUser $user, Message $message): void
    {

        $messageModel = $this->getMessage(
            chat: $chat,
            messageId: $message->getMessageId(),
            userId: $user->id
        );

        if (null === $message) {
            return;
        }

        $messageModel->update(attributes: [
            'type' => $message->getMessageType(),
            'text' => $message->getText(),
            'file' => $message->getFile(),
            'thumb' => $message->getThumb()
        ]);
    }

    private function getMessage(Chat $chat, int $messageId, int $userId): ?MessageModel
    {
        return $chat->messages()
            ->where(column: 'message_id', operator: $messageId)
            ->where(column: 'telegram_user_id', operator: $userId)
            ->first();
    }

    private function getTelegramUser(): ?TelegramUser
    {
        return app()->bound(abstract: 'telegramUser') ? app(abstract: 'telegramUser') : null;
    }

    private function shouldTerminate($user): bool
    {
        return $this->checkIfUserIsInactiveOrNotBlockedOrAdminRole(user: $user) || getWebhookUpdate()->objectType() === 'my_chat_member';
    }

    private function checkIfUserIsInactiveOrNotBlockedOrAdminRole(TelegramUser $user): bool
    {
        return TelegramUserStatus::INACTIVE === $user->status
            || TelegramUserChatStatus::BLOCKED === $user->chat_status;
    }

    private function getOrCreateChat($user): Chat
    {
        return $this->chatExists(chat: $user->chat) ? $user->chat : $this->createChat(user: $user);
    }





}
