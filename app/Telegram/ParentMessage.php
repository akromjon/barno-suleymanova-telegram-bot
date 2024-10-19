<?php

namespace App\Telegram;

use App\Models\Enums\MessageSender;
use App\Models\Enums\MessageType;
use Telegram\Bot\Objects\Update;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\File;
use Telegram\Bot\Objects\Message;

abstract class ParentMessage
{
    protected MessageType $messageType;
    protected ?string $text = null;
    protected ?string $file = null;
    protected ?string $thumb = null;
    protected ?string $objectType = null;
    protected ?int $messageId = null;
    protected ?int $chatId;
    protected MessageSender $sender = MessageSender::TELEGRAM_USER;
    private static ?self $instance = null;
    protected string $file_path = '/media/';
    protected Update $update;

    private function __construct(Update $update)
    {
        $this->update=$update;

        $this->chatId=$update->getChat()->getId();

        $this->messageId =$update->getMessage()->getMessageId();

        $this->init();

    }
    public static function handle(Update $update): static
    {
        if (static::$instance === null) {
            static::$instance = new static(update: $update);
        }

        return static::$instance;
    }

    protected function init(): void
    {
        $this->resolveType();
    }

    protected function resolveType(): void
    {
        $objectType = $this->update->objectType();

        $this->objectType = $objectType;

        $type = match ($objectType) {
            'message', 'edited_message','channel_post','edited_channel_post' => $this->resolveMessageType(
                message: $this->update->getMessage(),
                type: $objectType
            ),
            default => MessageType::UNSUPPORT,
        };

        $this->messageType = $type;

        if (MessageType::UNSUPPORT === $type) {
            return;
        }

        $this->setTextAndFileWithType(type: $type);
    }

    protected function setTextAndFileWithType(MessageType $type): void
    {
        match ($type) {
            MessageType::TEXT => $this->set(properties: [
                'text' => $this->update->getMessage()->getText(),
            ]),
            MessageType::PHOTO => $this->set(properties: [
                'text' => $this->update->getMessage()->getCaption(),
                'file' => $this->getFile(message: $this->update->getMessage()->getPhoto()),
            ]),
            MessageType::DOCUMENT => $this->set(properties: [
                'text' => $this->update->getMessage()->getCaption(),
                'file' => $this->getFile(message: $this->update->getMessage()->getDocument()),
            ]),
            MessageType::CONTACT => $this->set(properties: [
                'text' => $this->update->getMessage()->getContact()->getPhoneNumber(),
            ]),
            MessageType::LOCATION => $this->set(properties: [
                'text' => $this->update->getMessage()->getLocation()->getLatitude() . "x" . $this->update->getMessage()->getLocation()->getLongitude(),
            ]),
            MessageType::VIDEO_NOTE => $this->set(properties: [
                'file' => $this->getFile(message: $this->update->getMessage()->getVideoNote()),
                'thumb' => $this->getFile(message: $this->update->getMessage()->getVideoNote()->getThumb()),
            ]),
            MessageType::VOICE => $this->set(properties: [
                'text' => $this->update->getMessage()->getCaption(),
                'file' => $this->getFile(message: $this->update->getMessage()->getVoice()),
            ]),
            MessageType::VIDEO => $this->set(properties: [
                'text' => $this->update->getMessage()->getCaption(),
                'file' => $this->getFile(message: $this->update->getMessage()->getVideo()),
                'thumb' => $this->getFile(message: $this->update->getMessage()->getVideo()->getThumb()),
            ]),
            MessageType::AUDIO => $this->set(properties: [
                'text' => $this->update->getMessage()->getCaption(),
                'file' => $this->getFile(message: $this->update->getMessage()->getAudio()),
            ]),
            MessageType::STICKER => $this->set(properties: [
                'file' => $this->getFile(message: $this->update->getMessage()->getSticker()),
            ]),
            MessageType::ANIMATION => $this->set(properties: [
                'file' => $this->getFile(message: $this->update->getMessage()->getAnimation()),
            ]),
            default => MessageType::UNSUPPORT,
        };
    }

    private function getFile(Collection $message): ?string
    {
        $file = $this->getFileFromTelegram(message: $message);

        if (400 === $file) {
            return null;
        }

        return $this->downloadAndStoreFile(file: $file);
    }

    private function downloadAndStoreFile(File $file): ?string
    {
        $file_extension = strtolower(string: pathinfo($file->file_path, flags: PATHINFO_EXTENSION));

        $short_path = "$this->file_path{$this->chatId}_{$file->file_unique_id}.{$file_extension}";

        $full_path = storage_path(path: "app/public/{$short_path}");

        Telegram::downloadFile(file: $file->file_id, filename: $full_path);

        return $short_path;
    }

    private function getFileFromTelegram(Collection $message): File|int
    {
        $file_id = is_int(value: $message->last()) ? $message->file_id : $message->last()->file_id;

        try {

            return Telegram::getFile(params: ['file_id' => $file_id]);

        } catch (\Exception $e) {

            Log::error(message: $e->getMessage());

            return $e->getCode();
        }

    }


    private function set(array $properties): void
    {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }

    protected function resolveMessageType(Collection $message, string $type): MessageType
    {
        return match (true) {
            $message->has(key: 'text') => MessageType::TEXT,
            $message->has(key: 'photo') => MessageType::PHOTO,
            $message->has(key: 'video') => MessageType::VIDEO,
            $message->has(key: 'voice') => MessageType::VOICE,
            $message->has(key: 'location') => MessageType::LOCATION,
            $message->has(key: 'audio') => MessageType::AUDIO,
            $message->has(key: 'document') => MessageType::DOCUMENT,
            $message->has(key: 'contact') => MessageType::CONTACT,
            $message->has(key: 'sticker') => MessageType::STICKER,
            $message->has(key: 'dice') => MessageType::DICE,
            $message->has(key: 'video_note') => MessageType::VIDEO_NOTE,
            $message->has(key: 'channel_post') => MessageType::CHANNEL_POST,
            $message->has(key: 'media_group') => MessageType::MEDIA_GROUP,
            $message->has(key: 'poll') => MessageType::POLL,
            default => MessageType::UNSUPPORT,
        };

    }


}
