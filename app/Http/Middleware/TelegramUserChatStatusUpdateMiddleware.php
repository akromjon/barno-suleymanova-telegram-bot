<?php

namespace App\Http\Middleware;


use Symfony\Component\HttpFoundation\Response;
use App\Models\Enums\TelegramUserChatStatus;
use Telegram\Bot\Objects\ChatMemberUpdated;
use Telegram\Bot\Objects\Update;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Closure;

class TelegramUserChatStatusUpdateMiddleware
{

    public function handle(Request $request, Closure $next): Response|false
    {
        $type = $this->processWebhookUpdate(update: getWebhookUpdate());

        if ($this->shouldProceed(type: $type)) {

            return $next($request);

        }

        abort(code: 200);

        return false;
    }

    private function processWebhookUpdate(Update $update): ?TelegramUserChatStatus
    {
        return match ($update->objectType()) {
            'my_chat_member' => $this->updateTelegramUser(myChatMember: $update->myChatMember),
            default => null,
        };
    }

    private function updateTelegramUser(?ChatMemberUpdated $myChatMember): ?TelegramUserChatStatus
    {
        if (null === $myChatMember) {
            return null;
        }

        $user = $this->getTelegramUser(chatId: $myChatMember->chat->id);

        if (null === $user) {
            return null;
        }

        $chatStatus = $this->determineChatStatus(status: $myChatMember->new_chat_member->status);

        $this->updateUserStatusAndTimestamp(user: $user, status: $chatStatus);

        return $chatStatus;
    }

    private function getTelegramUser(?int $chatId): ?TelegramUser
    {
        return TelegramUser::where(column: 'chat_id', operator: $chatId)->first();
    }

    private function determineChatStatus(string $status): TelegramUserChatStatus
    {
        return match ($status) {
            'kicked', 'left' => TelegramUserChatStatus::BLOCKED,
            'member', 'administrator' => TelegramUserChatStatus::ACTIVE,
            default => TelegramUserChatStatus::ACTIVE,
        };
    }

    private function updateUserStatusAndTimestamp(TelegramUser $user, TelegramUserChatStatus $status): void
    {
        $user->update(attributes: [
            'chat_status' => $status,
            'last_used_at' => now(),
        ]);
    }

    private function shouldProceed(?TelegramUserChatStatus $type): bool
    {
        return is_null(value: $type) || $type === TelegramUserChatStatus::ACTIVE ||  $type !== TelegramUserChatStatus::BLOCKED;
    }




}
