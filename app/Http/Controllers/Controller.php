<?php

namespace App\Http\Controllers;

use Telegram\Bot\Objects\Update;

abstract class Controller
{
    protected array $commands = [
        '/start',
        '/help',
    ];
    protected function objectType(Update $update): ?string
    {
        $type = $update->objectType();

        return match ($type) {
            'message' => $this->findMessageOrCommand(update: $update, type: $type),
            'callback_query' => $type,
            default => null,
        };

    }

    private function findMessageOrCommand(Update $update, ?string $type): ?string
    {
        if (null === $type) {
            return null;
        }

        if ($type === 'message' && getCachedUserAction() === 'sending_notification') {
            return 'sending_notification';
        } else {
            clearUserAction();
        }

        if ($type === 'message' && in_array(needle: $update->getMessage()?->getText(), haystack: $this->commands)) {
            return 'command';
        }

        return 'message';

    }
}
