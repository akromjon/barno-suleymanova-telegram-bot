<?php

declare(strict_types=1);


namespace App\Livewire;

use App\Models\Chat;
use App\Models\Enums\ChatStatus;
use App\Models\Enums\TelegramUserType;
use App\Models\TelegramUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;


class StartChat extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public string $search="";

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function render(): Factory|View
    {
        $search=trim($this->search);

        $users=TelegramUser::orderBy(column: 'last_used_at',direction: 'desc');

        if(''!==$search){

            $users = $users->where('first_name', 'like', "%$search%")
                ->orWhere('last_name','like',"%$search%")
                ->orWhere('username','like',"%$search%")
                ->orWhere('chat_id','like',"%$search%")
                ->orWhere('chat_status','like',"%$search%")
                ->orWhere('status','like',"%$search%")
                ->orWhere('role','like',"%$search%")
                ->orWhere('tags','like',"%$search%")
                ->orWhere('type','like',"%$search%");
        }

        $users=$users->paginate(perPage: $this->perPage);

        return view(view: 'livewire.start-chat',data: ['users'=>$users]);
    }

    public function createChat(TelegramUser $user)
    {
       $chat=$user->chat;

       if(null===$chat){
            $chat=$this->createChatByUser(user: $user);
       }

       return $this->redirectRoute( 'chats.show',$chat->id);
    }

    private function createChatByUser(TelegramUser $user): Chat
    {
        return $user->chat()->create(attributes: [
            'status' => ChatStatus::ACTIVE,
            'last_messaged_at' => now(),
            'type' => $user->type,
        ]);
    }


}
