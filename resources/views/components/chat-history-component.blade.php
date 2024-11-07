<div class="chat">
    @if (isset($group))
        <div class="chat-header clearfix">
            <div class="row">
                <div class="col-lg-6">

                    <img style="width:50px;height:50px" src="{{ $group->url_avatar }}" alt="avatar">

                    <div class="chat-about">
                        <h6 class="m-b-0 limit-text">{{ $group->name }}</h6>
                        @if (
                            (!$group->isChatWithSomeone && $group->last_activity_at > now()->subMinutes(5)) ||
                                ($group->isChatWithSomeone && Cache::has('user-is-online-' . $group->user_id)))
                            <small class="text-success">{{ __('public.online') }}</small>
                        @else
                            <small class="text-secondary text-nowrap">{{ __('public.Online') }}
                                {{ $group->timeAgo }}</small>
                        @endif
                        @if ($group->isChatWithSomeone)
                            <a href="{{ route('profile', $group->user_id) }}"
                                class="btn btn-outline-info">{{ __('public.Profile') }}</a>
                        @else
                            <a href="{{ route('aboutGroup', $group->id) }}"
                                class="btn btn-outline-info">{{ __('public.About') }}</a>
                        @endif
                        @if ($group->isBlocked)
                            <h3 class="text-danger">{{ __('public.You are blocked') }}</h3>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif
    <div class="chat-history"
        onscroll="handleScrollChatHistory(event, {{ isset($isCanContinueRender) && !$isCanContinueRender ? 'false' : 'true' }},{{ session('messageWithContact') ?? 'null' }})">
        @if (isset($group))
            @if (isset($isCanContinueRender) && $isCanContinueRender == false)
                <div class="text-center">
                    <img src="{{ $group->url_avatar }}" style="border-radius:999px; width: 50px; height: 50px;"
                        alt="">
                </div>
                <div class="text-center mb-4">
                    <h2>{{ $group->name }}</h2>

                </div>
                <hr>
            @endif
            <ul class="m-b-0" id="chat-history-container">
                @if (isset($group->messages))
                    @foreach ($group->messages as $message)
                        <div
                            id="{{ $loop->last || $loop->iteration == 19 ? 'newest-message' : ($loop->first ? 'oldest-message' : '') }}">
                            <x-chat-item-component :message=$message />
                        </div>
                    @endforeach
                @endif
            </ul>
    </div>
@else
    <h1>{{ __('public.Welcome') }}</h1>
    <h3>{{ __('public.Select someone to chat with them') }}</h3>
    @endif
    <div class="chat-message clearfix">
        @if (isset($group))
            @if (!$group->isBlocked)
                <div id="files-post-preview">
                    <div id="media"></div>
                    <div id="other"></div>
                </div>
                <form action="/message" id="message-form" method="POST" class="input-group mb-0"
                    onsubmit="handleSubmitNewMessage(event, {
                    sending: '{{ __('public.Sending') }}',
                    send: '{{ __('public.Send') }}',
                    contentTooLarge: '{{ __('public.Content too large') }}'
                })"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="inline-flex mx-1 justify-content-center align-content-center">
                        <label for="otherFiles" class="mx-1 inline-flex align-items-center"><i class="fa-solid fa-link"
                                style="font-size: 20px;"></i></label>
                        <input id="otherFiles" type="file" onchange="inputOtherFileOnChange(event);" multiple
                            name="otherFiles[]" hidden id="fileUpload">
                        <label for="mediaFiles" class="mx-1 inline-flex align-items-center"><i style="font-size: 20px;"
                                class="fa-regular fa-image "></i></label>
                        <input id="mediaFiles" type="file" onchange="inputMediaFileOnChange(event);"
                            accept="image/*,video/*" multiple name="mediaFiles[]" hidden id="fileUpload">
                    </div>
                    <input type="text" spellcheck="false" name="message" value="" onchange=""
                        autocomplete="off" class="form-control rounded-left col-md-10 col-sm-10" placeholder="Aa"
                        onkeydown="keyDownEvent(event);">
                    <button type="submit"
                        class="btn btn-primary col-md-2 col-sm-2 btn-send-message">{{ __('public.Send') }}</button>

                </form>
            @else
                <h3 class="text-danger">{{ __('public.You are blocked') }}</h3>
            @endif
        @else
            {{ __('public.If your chat is not rendered, please') }} <a
                style="text-decoration: underline;cursor: pointer;color: blue;"
                onclick="location.reload(true);">{{ __('public.Reload') }}</a>
        @endif
    </div>
</div>
<style>
    #files-post-preview {
        overflow-x: auto;
        margin-bottom: 5px;
    }

    #files-post-preview,
    #files-post-preview #media,
    #files-post-preview #other {
        display: flex;
        align-items: flex-end;
    }

    #files-post-preview #media>*,
    #files-post-preview #other>* {

        overflow: hidden;
        position: relative;
        margin-right: 5px;
        border-radius: 10px;
        border: 1px solid #fff;
        background-color: #666;
        width: 200px;
        height: fit-content;
        max-height: 300px;

        &>img,
        &>video {
            width: 100%;
            height: auto;
        }

        &>button {
            position: absolute;
            top: 0;
            right: 0;
        }
    }

    #files-post-preview #other>* {
        padding: 5px;
    }
</style>
<script>
    $(document).ready(function() {
        document.querySelector('.chat-history').scrollTop = document.querySelector('.chat-history')
            .scrollHeight;
    })
</script>
