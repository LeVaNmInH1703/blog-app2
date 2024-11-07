<div class="feelings_container p-2" id="feelings_container" style="display:none;">
    @foreach ($feelings as $feeling)
        <a data-feeling_id="{{ $feeling->id }}" class="text-decoration-none p-1" onclick="handleClickATag(event)">
            <x-get-feeling-component :feeling=$feeling :size=35 />
        </a>
    @endforeach
</div>
