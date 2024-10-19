<?php

namespace App\Telegram\FSM;

use App\Jobs\CopyMessageJob;

use App\Models\TelegramNotification;
use App\Telegram\Menu\Menu;
use App\Telegram\Telegram;
use Illuminate\Support\Facades\Log;

class CallbackQueryFSM extends Base
{
    protected function route(): void
    {
        match ($this->message->m) {
            'N' => $this->handleCallBackQueryNotification(message: $this->message),
            default => Log::error('Unknown CallbackQuery type returned'),
        };
    }

    protected function handleCallBackQueryNotification(object $message): void
    {
        $notificationId = $message->i;

        $sendTo = match ($message->s) {
            'all' => ['active'],
            'sub' => ['subscribed'],
        };

        $notification = TelegramNotification::where(column: 'id', operator: $notificationId)
            ->first();

        if (null === $notification) {

            Telegram::sendMessage(
                params: Menu::notificationDeletedOrNotExist(
                    chatId: getUser()->chat_id
                )
            );

            return;
        }

        $notification->update([
            'send_to' => $sendTo,
        ]);

        CopyMessageJob::dispatch($notification);

        Telegram::sendMessage(
            params: Menu::sendNotificationMessage(
                chatId: getUser()->chat_id
            )
        );
    }

}
