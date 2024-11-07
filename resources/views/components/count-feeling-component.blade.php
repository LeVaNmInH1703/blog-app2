@if ($obj->countFeeling > 0)
    <li>
        <div class="likes heart" title="Like/Dislike">
            <x-get-feeling-component :feeling=$firstFeeling :size=$size :isShowNull=false />
            <x-get-feeling-component :feeling=$secondFeeling :size=$size :isShowNull=false />
            <x-get-feeling-component :feeling=$thirdFeeling :size=$size :isShowNull=false />
            @if (isset($obj->feelings))
                <span>
                    {{ $obj->countFeeling }}
                </span>
            @endif
        </div>
    </li>
@endif
