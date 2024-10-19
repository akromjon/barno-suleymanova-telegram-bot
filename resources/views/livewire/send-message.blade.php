
@php
use  App\Models\Enums\TelegramUserChatStatus;
 $disabled="";
 if($chat?->telegramUser?->chat_status===TelegramUserChatStatus::BLOCKED)
 {
     $disabled="disabled";
 }
@endphp
<div>
    <div class="tyn-chat-form-enter">
        <form wire:submit="sendMessage">
            <textarea {{$disabled}}  wire:model="text" class="tyn-chat-form-input" name="text" id="" cols="100" rows="2"></textarea>
            <ul class="tyn-list-inline me-n2 my-1">
                <li><button class="btn btn-icon btn-white btn-md btn-pill">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-mic-fill" viewBox="0 0 16 16">
                            <path d="M5 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0z" />
                            <path
                                d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5" />
                        </svg><!-- mic-fill -->
                    </button></li>
                <li><button type="submit" class="btn btn-icon btn-white btn-md btn-pill">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-send-fill" viewBox="0 0 16 16">
                            <path
                                d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z" />
                        </svg><!-- send-fill -->
                    </button></li>
                @error('text') <span class="error">{{ $message }}</span> @enderror
        </form>
        </ul>
    </div><!-- .tyn-chat-form-enter -->
</div>
