<?php

namespace App\Livewire;

use App\Models\Chat;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class ChatList extends Component
{
    use WithPagination;
    public int $perPage = 15;

    public function loadMore()
    {
        $this->perPage += 10;
    }
    public function render(): Factory|View
    {
        $chats = Chat::orderBy(column: 'last_messaged_at', direction: 'desc')
            ->paginate($this->perPage);

        return view('livewire.chat-list', ['chats' => $chats]);
    }
}
