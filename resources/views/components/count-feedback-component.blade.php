@if ($obj->countFeedback > 0)
    <li>
        <div class="likes heart" title="Like/Dislike">
            <x-get-feedback-component :feedback=$firstFeedback :size=$size :isShowNull=false />
            <x-get-feedback-component :feedback=$secondFeedback :size=$size :isShowNull=false />
            <x-get-feedback-component :feedback=$thirdFeedback :size=$size :isShowNull=false />
            @if (isset($obj->feedbacks))
                <span>
                    {{ $obj->countFeedback }}
                </span>
            @endif
        </div>
    </li>
@endif
