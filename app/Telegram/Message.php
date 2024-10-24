<?php

declare(strict_types=1);


namespace App\Telegram;

use App\Models\Enums\MessageSender;
use App\Models\Enums\MessageType;

final class Message extends ParentMessage
{
    public function getMessageType(): MessageType
    {
        return $this->messageType;
    }
    public function getText(): ?string
    {
        return $this->text;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function getChatId(): ?int
    {
        return $this->chatId;
    }

    public function getThumb(): ?string
    {
        return $this->thumb;
    }

    public function getObjectType(): ?string
    {
        return $this->objectType;
    }

    public function getMessageId(): ?int
    {
        return $this->messageId;
    }

    public function getSender():MessageSender
    {
        return $this->sender;
    }



}
