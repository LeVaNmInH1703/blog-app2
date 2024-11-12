<div class="feedbacks_container p-2" id="feedbacks_container" style="display:none;">
    @foreach ($feedbacks as $feedback)
        <a data-feedback_id="{{ $feedback->id }}" class="text-decoration-none p-1" onclick="handleClickATag(event)">
            <x-get-feedback-component :feedback=$feedback :size=35 />
        </a>
    @endforeach
</div>
