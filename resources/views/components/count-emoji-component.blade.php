@if ($obj->countEmoji > 0)
    <li>
        <div class="likes heart" title="Like/Dislike">
            <x-get-emoji-component :emoji=$firstEmoji :size=$size :isShowNull=false />
            <x-get-emoji-component :emoji=$secondEmoji :size=$size :isShowNull=false />
            <x-get-emoji-component :emoji=$thirdEmoji :size=$size :isShowNull=false />
            @if (isset($obj->emojis))
                <span>
                    {{ $obj->countEmoji }}
                </span>
            @endif
        </div>
    </li>
@endif
