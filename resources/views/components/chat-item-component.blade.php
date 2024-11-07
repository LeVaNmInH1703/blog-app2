@if ($message->files->count() > 0 || $message->content != '')
    @if ($message->isShowTime)
        <div class="text-secondary text-center w-100" style="font-size: 12px;">{{ $message->created_at }}</div>
    @endif
    @if ($message->type == 'info')
        <div class="text-white text-center w-100" style="font-size: 16px;">{{ $message->content }}</div>
    @else
        <li class="d-flex justify-content-{{ $message->user_id_send == Auth::id() ? 'end' : 'start' }} align-items-center"
            data-id="{{ $message->id }}">
            <div class="message {{ $message->user_id_send == Auth::id() ? 'bg-white' : '' }}">
                <div class="message-file-wrap">
                    @foreach ($message->files as $file)
                        @if (in_array(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                            <img class="mb-1"
                                src="{{ asset((file_exists(public_path('images_resize/' . $file->file_name)) ? 'images_resize/' : 'images/') . $file->file_name) }}"
                                alt="Message Image"
                                onclick="window.open(this.src.replace('/images_resize/', '/images/'), '_blank');" />
                        @elseif (in_array(strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)), ['mp4', 'mov', 'avi', 'wmv']))
                            <video class="mb-1" src="{{ asset('videos/' . $file->file_name) }}" alt="Blog Image"
                                controls='true'></video>
                        @else
                            <a href="{{ route('download.file', ['fileName' => $file->file_name, 'oldName' => $file->old_name]) }}"
                                class="btn btn-secondary mb-1" title="{{ __('public.Download') }}">
                                <i class="fa-regular fa-file"></i> <small>{{ $file->old_name }}</small>
                                <small>{{ number_format(filesize(public_path('files/' . $file->file_name)) / 1024, 2) }}
                                    KB</small>
                            </a>
                        @endif
                    @endforeach
                </div>
                {!! preg_replace('/(http|https):\/\/[^\s]+/', '<a href="$0" target="_blank">$0</a>', $message->content) !!}
            </div>
            @if ($message->user_id_send != Auth::id() && !$message->group->isChatWithSomeone)
                <img src="{{ $message->user->url_avatar }}"
                    style="border-radius: 999px;height: 24px;width: 24px; margin-left:8px;" alt="">
                <small class="text-secondary mx-1" style="font-size: 12px;">{{ $message->user->name }}</small>
            @endif
        </li>
    @endif
@endif
