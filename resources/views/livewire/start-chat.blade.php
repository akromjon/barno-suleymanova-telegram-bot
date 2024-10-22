<div>
    <div class="modal fade" tabindex="-1" id="newChat"  wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0">
                <div class="modal-body p-4">
                    <h4 class="pb-2">Search</h4>
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <div class="form-control-icon start">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                                </svg><!-- search -->
                            </div>
                            <input wire:model.live="search" type="text" class="form-control form-control-solid" id="search-contact" placeholder="name, lastname, ID, username">
                        </div>
                    </div><!-- .form-group -->
                    <ul class="tyn-media-list gap gap-3 pt-4">
                        @foreach($users as $key => $user)
                            <li>
                                <div class="tyn-media-group">
                                    <div class="tyn-media">
                                        <img src="{{ asset('images/user.svg') }}" alt="">
                                    </div><!-- .tyn-media -->
                                    <div class="tyn-media-col">
                                        <div class="tyn-media-row">
                                            <h6 class="name">{{$user->first_name}} {{$user->last_name}}</h6>
                                        </div>
                                        <div class="tyn-media-row">
                                            <p class="content">{{$user->username}}</p>
                                        </div>
                                    </div><!-- .tyn-media-col -->
                                    <ul class="tyn-media-option-list me-n1">
                                        <li class="dropdown">
                                            <button class="btn btn-icon btn-white btn-pill dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,0" data-bs-auto-close="outside">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots" viewBox="0 0 16 16">
                                                    <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3" />
                                                </svg><!-- three-dots -->
                                            </button><!-- .dropdown-toggle -->
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="tyn-list-links">
                                                    <li>
                                                        <button type="button" wire:click="createChat({{$user}})">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-text" viewBox="0 0 16 16">
                                                                <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                                                                <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6m0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
                                                            </svg><!-- chat-left-text -->
                                                            <span>Message</span>
                                                        </button>
                                                    </li><!-- li -->
                                                </ul><!-- .tyn-list-links -->
                                            </div><!-- .dropdown-menu -->
                                        </li><!-- li -->
                                    </ul><!-- .tyn-media-option-list -->
                                </div><!-- .tyn-media-group -->
                            </li><!-- li -->
                        @endforeach

                    </ul><!-- .tyn-media-list -->
                </div><!-- .modal-body -->
                <button class="btn btn-md btn-icon btn-pill btn-white shadow position-absolute top-0 end-0 mt-n3 me-n3" data-bs-dismiss="modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                    </svg><!-- x-lg -->
                </button><!-- modal-close -->
            </div><!-- .modal-content -->
        </div><!-- .modal-dialog -->
    </div><!-- .modal -->
</div>
