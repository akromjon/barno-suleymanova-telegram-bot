<?php

namespace App\Livewire;

use App\Models\Chat;
use Livewire\Component;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use App\Telegram\Telegram;

class SendMessage extends Component
{

    public ?int $chat_id=null;

    public string $text;

    public $chat;

    public function mount(Chat $chat): void
    {
        $this->chat = $chat;
        $this->chat_id=$chat->id;
    }

    protected function rules(): array
    {
        return [
            'text' => 'required|max:4096|min:1'
        ];
    }
    public function render(): Factory|View
    {

        return view('livewire.send-message',["chat"=>$this->chat]);
    }



    public function sendMessage(): void
    {
        // dd($this->chat);

        $this->validate();

        $this->chat = Chat::find($this->chat_id);

        if ($this->chat === null) {
            // Handle case where chat is not found
            session()->flash('error', 'Chat not found.');

            return;
        }

        Telegram::sendMessage(params: [
            'chat_id' => $this->chat->telegramUser->chat_id,
            'text' => $this->text,
        ]);

        $this->reset('text');

        $this->dispatch(event: 'message-created');
    }
}
