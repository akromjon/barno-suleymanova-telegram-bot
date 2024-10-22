@extends('components.main')
@section('message')

    <div class="tyn-main tyn-chat-content" id="tynMain">
        <div class="tyn-chat-head">
            <ul class="tyn-list-inline d-md-none ms-n1">
                <li><button class="btn btn-icon btn-md btn-pill btn-transparent js-toggle-main">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                        </svg><!-- arrow-left -->
                    </button></li>
            </ul>
            <div class="tyn-media-group">
                <div class="tyn-media tyn-size-lg d-none d-sm-inline-flex">
                    <img src="{{asset('images/user.svg')}}" alt="">
                </div><!-- .tyn-media -->
                <div class="tyn-media tyn-size-rg d-sm-none">
                    <img src="{{asset('images/user.svg')}}" alt="">
                </div><!-- .tyn-media -->
                <div class="tyn-media-col">
                    <div class="tyn-media-row">
                        <h4 class="name">{{$chat->telegramUser->first_name}} <span class="d-none d-sm-inline-block">{{$chat->telegramUser->last_name}} | {{$chat->telegramUser->chat_id}} |  {{$chat->telegramUser->chat_status}} </span></h4>
                    </div>
                    <div class="tyn-media-row">
                        <h6 class="name">{{$chat->telegramUser->username}} </h6>
                    </div>

                    <div class="tyn-media-row has-dot-sap">
                        <span class="meta">{{$chat->telegramUser?->last_used_at?->diffForHumans()}}</span>
                    </div>
                </div><!-- .tyn-media-col -->
            </div><!-- .tyn-media-group -->
            <ul class="tyn-list-inline gap gap-3 ms-auto">
                <li class="d-none d-sm-block"><button class="btn btn-icon btn-light js-toggle-chat-search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg><!-- search -->
                    </button></li>
            </ul><!-- .tyn-list-inline -->
            <div class="tyn-chat-search" id="tynChatSearch">
                <div class="flex-grow-1">
                    <div class="form-group">
                        <div class="form-control-wrap form-control-plaintext-wrap">
                            <div class="form-control-icon start">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                                </svg><!-- search -->
                            </div>
                            <input type="text" class="form-control form-control-plaintext" id="searchInThisChat" placeholder="Search in this chat">
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap gap-3">
                    <ul class="tyn-list-inline ">
                        <li><button class="btn btn-icon btn-sm btn-transparent">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-up" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z" />
                                </svg><!-- chevron-up -->
                            </button></li>
                        <li><button class="btn btn-icon btn-sm btn-transparent">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708" />
                                </svg><!-- chevron-down -->
                            </button></li>
                    </ul>
                    <ul class="tyn-list-inline ">
                        <li><button class="btn btn-icon btn-md btn-light js-toggle-chat-search">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                                </svg><!-- x-lg -->
                            </button></li>
                    </ul>
                </div>
            </div><!-- .tyn-chat-search -->
        </div><!-- .tyn-chat-head -->

        <div class="tyn-chat-body js-scroll-to-end" id="tynChatBody">
            @livewire('message',['chat'=>$chat])
        </div><!-- .tyn-chat-body -->
        <div class="tyn-chat-form">
            <div class="tyn-chat-form-insert">
                <ul class="tyn-list-inline gap gap-3">
                    <li class="dropup">
                        <button class="btn btn-icon btn-light btn-md btn-pill" data-bs-toggle="dropdown" data-bs-offset="0,10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
                            </svg><!-- plus-lg -->
                        </button>
                        <div class="dropdown-menu">
                            <ul class="tyn-list-links">
                                <li><a href="index.html#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-video2" viewBox="0 0 16 16">
                                            <path d="M10 9.05a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                                            <path d="M2 1a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zM1 3a1 1 0 0 1 1-1h2v2H1zm4 10V2h9a1 1 0 0 1 1 1v9c0 .285-.12.543-.31.725C14.15 11.494 12.822 10 10 10c-3.037 0-4.345 1.73-4.798 3zm-4-2h3v2H2a1 1 0 0 1-1-1zm3-1H1V8h3zm0-3H1V5h3z" />
                                        </svg><!-- person-video2 -->
                                        <span>New Group</span></a></li>
                                <li><a href="index.html#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-mic" viewBox="0 0 16 16">
                                            <path d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5" />
                                            <path d="M10 8a2 2 0 1 1-4 0V3a2 2 0 1 1 4 0zM8 0a3 3 0 0 0-3 3v5a3 3 0 0 0 6 0V3a3 3 0 0 0-3-3" />
                                        </svg><!-- mic -->
                                        <span>Voice Clip</span></a></li>
                            </ul>
                        </div>
                    </li><!-- li -->
                    <li class="d-none d-sm-block"><button class="btn btn-icon btn-light btn-md btn-pill">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16">
                                <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                                <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54L1 12.5v-9a.5.5 0 0 1 .5-.5z" />
                            </svg><!-- card-image -->
                        </button></li><!-- li -->
                    <li class="d-none d-sm-block"><button id="emojiButton" class="btn btn-icon btn-light btn-md btn-pill">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-smile-fill" viewBox="0 0 16 16">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5M4.285 9.567a.5.5 0 0 1 .683.183A3.5 3.5 0 0 0 8 11.5a3.5 3.5 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683M10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8" />
                            </svg><!-- emoji-smile-fill -->
                        </button></li><!-- li -->
                </ul>
            </div><!-- .tyn-chat-form-insert -->
            @livewire('send-message',['chat'=>$chat])
        </div><!-- .tyn-chat-form -->

    </div><!-- .tyn-main -->
@endsection
