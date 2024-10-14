<?php

namespace App\Http\Middleware;

use App\Models\Enums\TelegramUserChatStatus;
use App\Models\Enums\TelegramUserStatus;
use App\Models\Enums\TelegramUserType;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Models\TelegramUser;
use Closure;
use Illuminate\Support\Collection;

class TelegramUserLastUsedAtUpdatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|false
    {
        $chat = $this->getChat();

        $user = TelegramUser::where(column: 'chat_id', operator: $chat->id)->first();

        if (null === $user) {

            $user = $this->createChat(chat: $chat);
        }

        if (null === $user) {

            Log::error(message: "Unsupported Chat type is received :(");

            abort(code: 200);

            return false;
        }

        $this->setTelegramUserAsGlobalVariable(user: $user);

        if (TelegramUserStatus::INACTIVE === $user->status) {

            Log::error(message: "Inactive User is being accessed: ", context: $user->toArray());

            abort(code: 200);

            return false;
        }

        $this->updateTelegramUserLastUsedAt(user: $user);

        return $next($request);
    }

    private function getChat(): Collection
    {
        return getWebhookUpdate()->getChat();
    }

    private function createChat(Collection $chat): ?TelegramUser
    {
        // 'private', 'group', 'supergroup' or 'channel'.

        return match ($chat->getType() ?? 'uknown_type') {
            'channel' => $this->createTelegramUserAsChannel(chat: $chat),
            'supergroup' => $this->createTelegramUserAsSupergroup(chat: $chat),
            'group' => $this->createTelegramUserAsGroup(chat: $chat),
            'private' => $this->createTelegramUser(chat: $chat),
            default => null,
        };
    }

    private function createTelegramUserAsChannel(Collection $chat): TelegramUser
    {
        return TelegramUser::create(attributes: [
            'chat_id' => $chat->id,
            'first_name' => $chat->getTitle(),
            'username' => !empty($chat->username) ? "@$chat->username" : '',
            'chat_status' => TelegramUserChatStatus::ACTIVE,
            'status' => TelegramUserStatus::ACTIVE,
            'type' => TelegramUserType::CHANNEL,
        ]);
    }

    private function createTelegramUserAsSupergroup(Collection $chat): TelegramUser
    {
        return TelegramUser::create(attributes: [
            'chat_id' => $chat->id,
            'first_name' => $chat->getTitle(),
            'chat_status' => TelegramUserChatStatus::ACTIVE,
            'status' => TelegramUserStatus::ACTIVE,
            'type' => TelegramUserType::SUPERGROUP,
        ]);
    }

    private function createTelegramUserAsGroup(Collection $chat): TelegramUser
    {
        return TelegramUser::create(attributes: [
            'chat_id' => $chat->id,
            'first_name' => $chat->getTitle(),
            'chat_status' => TelegramUserChatStatus::ACTIVE,
            'status' => TelegramUserStatus::ACTIVE,
            'type' => TelegramUserType::GROUP,

        ]);
    }

    private function createTelegramUser(Collection $chat): TelegramUser
    {
        return TelegramUser::create(attributes: [
            'chat_id' => $chat->id,
            'first_name' => $chat->first_name,
            'last_name' => $chat->last_name,
            'username' => !empty($chat->username) ? "@$chat->username" : '',
            'chat_status' => TelegramUserChatStatus::ACTIVE,
            'status' => TelegramUserStatus::ACTIVE,
            'type' => TelegramUserType::PRIVATE ,

        ]);
    }

    private function updateTelegramUserLastUsedAt(TelegramUser $user): void
    {
        // we update in the background
        defer(callback: function () use ($user): void {

            $user->update(attributes: [
                'last_used_at' => now(),
            ]);

        });
    }

    private function setTelegramUserAsGlobalVariable(TelegramUser $user): void
    {
        App::singleton(abstract: 'telegramUser', concrete: function () use ($user): TelegramUser {
            return $user;
        });
    }
}
