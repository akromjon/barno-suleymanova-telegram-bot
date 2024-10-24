<?php

declare(strict_types=1);


namespace App\Telegram\FSM;

use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Collection;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\Message;

abstract class Base
{
    protected Collection $file;
    protected string $chat_id;
    protected int|null $message_id;
    protected string|null|object $message;
    protected Update $update;
    protected Message $user_message;
    abstract protected function route(): void;

    public static function handle(...$arg): self
    {
        $instance = new static(...$arg);

        $instance->run();

        return $instance;
    }

    public function run(): void
    {
        $this->route();
    }
    protected function __construct(protected string $message_type)
    {
        $this->update = getWebhookUpdate();

        match ($this->message_type) {
            'callback_query' => $this->handleCallbackQuery(),
            'message' => $this->handleMessage(),
            'command' => $this->handleCommand(),
            'sending_notification' => $this->handleNotification(),
            default => null,
        };
    }

    protected function handleNotification(): void
    {
        $this->chat_id = $this->update->getMessage()->getChat()->getId();
    }


    protected function handleCommand(): void
    {
        $this->message = $this->update->getMessage()->getText();

        $this->chat_id = $this->update->getMessage()->getChat()->getId();
    }

    protected function handleCallbackQuery(): void
    {
        $this->message = json_decode($this->update->getCallbackQuery()->getData());

        $this->chat_id = $this->update->getCallbackQuery()->getMessage()->getChat()->getId();

        $this->message_id = $this->update->getCallbackQuery()->getMessage()->message_id;

        $this->user_message=$this->update->getCallbackQuery()->getMessage();
    }

    protected function handleMessage(): void
    {
        $this->message = $this->update->getMessage()->getText();

        $this->chat_id = $this->update->getMessage()->getChat()->getId();

        $this->message_id = $this->update->getMessage()->message_id;
    }

}
