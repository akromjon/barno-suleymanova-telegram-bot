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
    public int $perPage = 5;

    public string $search='';

    public function loadMore()
    {
        $this->perPage += 10;
    }
    public function render(): Factory|View
    {
        $search=trim($this->search);

        $chats=Chat::orderBy(column: 'last_messaged_at', direction: 'desc');


        if(''!==$search){

            $chats=$chats->whereHas('telegramUser',function($query)use($search){
                $query->where('first_name','like',"%$search%")
                ->orWhere('last_name','like',"%$search%")
                ->orWhere('username','like',"%$search%")
                ->orWhere('chat_id','like',"%$search%")
                ->orWhere('chat_status','like',"%$search%")
                ->orWhere('status','like',"%$search%")
                ->orWhere('role','like',"%$search%")
                ->orWhere('tags','like',"%$search%")
                ->orWhere('type','like',"%$search%");
            });
        }

        $chats=$chats->paginate($this->perPage);;

        return view('livewire.chat-list', ['chats' => $chats]);
    }
}
