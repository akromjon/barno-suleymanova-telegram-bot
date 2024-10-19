<?php

namespace App\Livewire;

use App\Models\Chat;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;


class Message extends Component
{
    use WithPagination;
    public int $perPage = 10;
    public Chat $chat;

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function mount(Chat $chat): void
    {
        $this->chat = $chat;
    }

    public function render(): Factory|View
    {
        $messages = $this->chat->messages()
            ->orderBy(column: 'id', direction: 'desc')
            ->paginate(perPage: $this->perPage);

        return view(view: 'livewire.message', data: [
            'messages' => $messages,
        ]);
    }

    protected function getListeners(): array
    {
        return ['message-created' => 'render'];
    }
}
