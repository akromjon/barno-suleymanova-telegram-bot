<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <div class="tyn-aside-body" data-simplebar>
        <div class="tyn-aside-search">
            <div class="form-group tyn-pill">
                <div class="form-control-wrap">
                    <div class="form-control-icon start">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-search" viewBox="0 0 16 16">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg><!-- search -->
                    </div>
                    <input type="text" class="form-control form-control-solid" id="search"
                        placeholder="Search contact / chat">
                </div>
            </div>
        </div><!-- .tyn-aside-search -->
        <div class="tab-content" id="chatList">

            <div class="tab-pane show active" id="all-chats" tabindex="0" role="tabpanel">
                <ul class="tyn-aside-list">
                    @foreach($chats as $ch)
                    <a href="{{ route('chats.show', $ch->id) }}">
                        <li class="tyn-aside-item {{ is_active(" chats/$ch->id/messages") }}">
                            <div class="tyn-media-group">
                                <div class="tyn-media tyn-size-lg">
                                    <img src="{{ asset('images/user.svg') }}" alt="">
                                </div><!-- .tyn-media -->
                                <div class="tyn-media-col">
                                    <div class="tyn-media-row">
                                        <h6 class="name">{{ $ch->telegramUser->first_name }} {{
                                            $ch->telegramUser?->last_name }}</h6>
                                    </div>
                                    <div class="tyn-media-row has-dot-sap">
                                        <p class="content">{{ $ch->lastMessage?->text }}</p>
                                        <span class="meta">{{ $ch->telegramUser?->last_used_at?->diffForHumans() }}</span>
                                    </div>
                                </div><!-- .tyn-media-col -->
                            </div><!-- .tyn-media-group -->
                        </li><!-- .tyn-aside-item -->
                    </a>
                    @endforeach
                    <div x-intersect="$wire.loadMore()"></div>
                </ul>
                <!-- Show loading spinner when more chats are being loaded -->

            </div><!-- .tab-pane -->
        </div><!-- .tab-content -->
    </div><!-- .tyn-aside-body -->
</div>

