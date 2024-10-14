<?php

namespace App\Http\Middleware;

use App\Models\Enums\TelegramUserChatStatus;
use App\Models\TelegramUser;
use Closure;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Telegram\Bot\Objects\ChatMemberUpdated;

class TelegramUserChatStatusUpdateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $type = match (getWebhookUpdate()->objectType()) {
            'my_chat_member' => $this->updateTelegramUser(
                myChatMember: getWebhookUpdate()->myChatMember
            ),
            default => null,
        };

        if (null === $type || TelegramUserChatStatus::ACTIVE === $type) {

            return $next($request);

        }

        return response()->json(data: [
            'message' => 'Bot is blocked',
            'status' => 'ok',
        ], status: 200);


    }

    private function updateTelegramUser(?ChatMemberUpdated $myChatMember): ?TelegramUserChatStatus
    {
        if (null === $myChatMember) {
            return null;
        }

        $user = $this->getTelegramUser(chat_id: $myChatMember->chat->id);

        if (null === $user) {
            return null;
        }

        $chat_status = $this->getChatStatus(status: $myChatMember->new_chat_member->status);

        $this->updateTelegramUserLastUsedAt(user: $user, status: $chat_status);

        return $chat_status;
    }

    private function getTelegramUser(?int $chat_id): ?TelegramUser
    {
        return TelegramUser::where(column: 'chat_id', operator: $chat_id)->first();
    }

    private function getChatStatus(?string $status): ?TelegramUserChatStatus
    {
        return match ($status) {
            'kicked' => TelegramUserChatStatus::BLOCKED,
            'left' => TelegramUserChatStatus::BLOCKED,
            'member' => TelegramUserChatStatus::ACTIVE,
            'administrator' => TelegramUserChatStatus::ACTIVE,
            default => TelegramUserChatStatus::ACTIVE,
        };
    }


    private function updateTelegramUserLastUsedAt(TelegramUser $user, TelegramUserChatStatus $status): void
    {
        // we update in the background
        defer(callback: function () use ($user, $status): void {

            $user->update(attributes: [
                'chat_status' => $status,
                'last_used_at' => now()
            ]);

        });
    }



}
