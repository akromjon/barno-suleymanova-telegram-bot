<?php

namespace App\Http\Middleware;

use App\Models\Enums\TelegramUserChatStatus;
use App\Models\Enums\TelegramUserStatus;
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
    public function handle(Request $request, Closure $next): Response
    {
        $chat = $this->getChat();

        $user = TelegramUser::where(column: 'chat_id', operator: $chat->id)->first();

        if (null === $user) {

            $user = $this->createTelegramUser(chat: $chat);
        }

        $this->setTelegramUserAsGlobalVariable(user: $user);

        $this->updateTelegramUserLastUsedAt(user: $user);

        if (TelegramUserStatus::INACTIVE === $user->status) {

            Log::error(message: "Inactive User is being accessed: ", context: $user->toArray());

            return response()->json(data: [
                'message' => 'You are unauthorized!',
            ], status: 200);
        }

        return $next($request);
    }

    private function getChat(): Collection
    {
        return getWebhookUpdate()->getChat();
    }

    private function createTelegramUser(Collection $chat): TelegramUser
    {
        return TelegramUser::create(attributes: [
            'chat_id' => $chat->id,
            'first_name' => $chat->first_name,
            'last_name' => $chat->last_name,
            'username' => !empty($chat->username) ? "@$chat->username" : '',
            'chat_status' => TelegramUserChatStatus::ACTIVE,
            'status' => TelegramUserStatus::ACTIVE
        ]);
    }

    private function updateTelegramUserLastUsedAt(TelegramUser $user): void
    {
        // we update in the background
        defer(callback: function () use ($user): void {

            $user->update([
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
