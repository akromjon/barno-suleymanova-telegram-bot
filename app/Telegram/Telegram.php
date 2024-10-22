<?php

namespace App\Telegram;

use App\Models\Chat;
use App\Models\Enums\ChatStatus;
use App\Models\Enums\MessageSender;
use App\Models\Enums\MessageType;
use App\Models\Enums\TelegramUserChatStatus;
use App\Models\TelegramUser;
use Telegram\Bot\Objects\Message as MessageObject;
use Telegram\Bot\Laravel\Facades\Telegram as TelegramObject;
use Illuminate\Support\Str;
use App\Models\Message as MessageModel;
use Exception;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Exceptions\TelegramResponseException;

final class Telegram
{
    public static function sendMessage(array $params): MessageObject|false
    {
        return self::send(method: 'sendMessage', params: $params, type: MessageType::TEXT);
    }

    public static function sendVideo(array $params): MessageObject|false
    {
        return self::send(method: 'sendVideo', params: $params, type: MessageType::VIDEO);
    }

    public static function sendPhoto(array $params): MessageObject|false
    {
        return self::send(method: 'sendPhoto', params: $params, type: MessageType::PHOTO);
    }

    public static function sendLocation(array $params): MessageObject|false
    {
        return self::send(method: 'sendLocation', params: $params, type: MessageType::LOCATION);
    }

    public static function sendVoice(array $params): MessageObject|false
    {
        return self::send(method: 'sendVoice', params: $params, type: MessageType::VOICE);
    }

    public static function sendAudio(array $params): MessageObject|false
    {
        return self::send(method: 'sendAudio', params: $params, type: MessageType::AUDIO);
    }

    public static function sendContact(array $params): MessageObject|false
    {
        return self::send(method: 'sendContact', params: $params, type: MessageType::CONTACT);
    }

    public static function sendVideoNote(array $params): MessageObject|false
    {
        return self::send(method: 'sendVideoNote', params: $params, type: MessageType::VIDEO_NOTE);
    }

    public static function sendSticker(array $params): MessageObject|false
    {
        return self::send(method: 'sendSticker', params: $params, type: MessageType::STICKER);
    }

    public static function sendDocument(array $params): MessageObject|false
    {
        return self::send(method: 'sendDocument', params: $params, type: MessageType::DOCUMENT);
    }

    public static function forwardMessage(array $params): MessageObject|false
    {
        return self::send(method: 'forwardMessage', params: $params, type: MessageType::TEXT);
    }

    public static function copyMessage(array $params): MessageObject|false
    {
        return self::sendCopyMessage(method: 'copyMessage', params: $params);
    }

    private static function sendCopyMessage(string $method, array $params): MessageObject|false
    {
        $response = TelegramObject::{$method}($params);

        self::storeSendCopyMessage(params: $params, messageId: $params['message_id']);

        return $response;
    }

    public static function storeSendCopyMessage(array $params, int $messageId)
    {
        $user = TelegramUser::where(column: 'chat_id', operator: $params['from_chat_id'])->first();

        if (null === $user) {
            return;
        }

        $message = MessageModel::where(column: 'telegram_user_id', operator: $user->id)
            ->where(column: 'message_id', operator: $params['message_id'])
            ->first();

        if (null === $message) {
            return;
        }

        $user = TelegramUser::where(column: 'chat_id', operator: $params['chat_id'])->first();

        $params = [
            'type' => $message->type,
            'file' => $message->file,
            'text' => $message->text,
            'thumb' => $message->thumb,
        ];

        $chat = $user->chat ?? $user->chat()->create(attributes: [
            'status' => ChatStatus::ACTIVE,
            'last_messaged_at' => now(),
        ]);

        self::createMessage(attributes: [
            ...$params,
            ...[
                'telegram_user_id' => $user->id,
                'chat_id' => $chat->id,
                'message_id' => $messageId,
                'sender' => MessageSender::BOT,
            ]
        ], chat: $chat);

    }


    private static function send(string $method, array $params, MessageType $type): MessageObject|false
    {
        try {

            $response = TelegramObject::{$method}($params);

            defer(callback: function () use ($type, $params, $response): void {
                self::storeMessage(type: $type, params: $params, messageId: $response->getMessageId());
            });

            return $response;

        } catch (TelegramResponseException $e) {

            logger($e->getCode());

            if (in_array($e->getCode(),[400,403])) {

                $chat_id = null;

                $data = $e->getResponse()->getRequest()->getParams();

                if (array_key_exists('multipart', $data)) {

                    foreach ($data['multipart'] as $part) {

                        if ($part['name'] === 'chat_id') {
                            $chat_id = $part['contents'];
                            break;
                        }
                    }
                }

                if (array_key_exists('form_params', $data)) {
                    $chat_id = $data['form_params']['chat_id'];
                }

                if (is_int($chat_id)) {

                    TelegramUser::where('chat_id', $chat_id)
                        ->update(['chat_status' => TelegramUserChatStatus::BLOCKED]);
                }

                Log::error("User $chat_id is blocked the bot");

            }

            return false;
        }



    }

    private static function storeMessage(MessageType $type, array $params, int $messageId): void
    {
        $user = TelegramUser::where(column: 'chat_id', operator: $params['chat_id'])
            ->with(relations: 'chat')
            ->first();

        $chat = $user->chat ?? $user->chat()->create(attributes: [
            'status' => ChatStatus::ACTIVE,
            'last_messaged_at' => now(),
        ]);

        $attributes = self::matchMessageType(type: $type, params: $params);

        if (!empty($attributes)) {

            self::createMessage(attributes: [
                ...$attributes,
                ...[
                    'telegram_user_id' => $user->id,
                    'chat_id' => $chat->id,
                    'message_id' => $messageId,
                    'type' => $type,
                    'sender' => MessageSender::BOT,
                ]
            ], chat: $chat);
        }
    }

    private static function matchMessageType(MessageType $type, array $params): array
    {
        return match ($type) {
            MessageType::TEXT => ['text' => $params['text']],
            MessageType::VIDEO, MessageType::PHOTO, MessageType::DOCUMENT,
            MessageType::VOICE => [
                'text' => $params['caption'] ?? null,
                'file' => self::extractFilePath(file: $params[strtolower(string: $type->value)]->getFile()),
            ],
            MessageType::AUDIO => [
                'text' => $params['title'] ?? null,
                'file' => self::extractFilePath(file: $params['audio']->getFile()),
            ],
            MessageType::VIDEO_NOTE, MessageType::STICKER => [
                'file' => self::extractFilePath(file: $params[strtolower(string: $type->value)]->getFile()),
            ],
            MessageType::CONTACT => [
                'text' => "{$params['first_name']}: {$params['phone_number']}",
            ],
            MessageType::LOCATION => [
                'text' => "{$params['latitude']}x{$params['longitude']}",
            ],
            default => [],
        };
    }

    private static function extractFilePath(string $file): string
    {
        return Str::after(subject: $file, search: 'storage/app/public');
    }

    private static function createMessage(array $attributes, Chat $chat): void
    {
        $chat->messages()->create(attributes: $attributes);
    }
}
