<?php

namespace App\Livewire;

use App\Models\Chat;
use Livewire\Component;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use App\Telegram\Telegram;

class SendMessage extends Component
{

    public string $text;

    public $chat;

    public function mount(Chat $chat): void
    {
        $this->chat = $chat;
    }

    protected function rules(): array
    {
        return [
            'text' => 'required|max:4096|min:1'
        ];
    }
    public function render(): Factory|View
    {
        return view(view: 'livewire.send-message');
    }



    public function sendMessage(): void
    {
        $this->validate();

        Telegram::sendMessage(params: [
            'chat_id' => $this->chat->telegramUser->chat_id,
            'text' => $this->text,
        ]);

        $this->reset();

        $this->dispatch(event: 'message-created');
    }
}
