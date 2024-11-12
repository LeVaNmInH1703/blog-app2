@if (isset($feedback))
    <img height="{{ $size }}" width="{{ $size }}" alt='{{ __('public.' . $feedback->alt) }}'
        src='{{ $feedback->src }}'>
    @if ($isShowName)
        <span class="text-nowrap" style="color: {{ $feedback->color_text }}">{{ __('public.' . $feedback->alt) }}</span>
    @endif
@elseif ($isShowNull)
    <i class="fa-regular fa-heart"></i>
    @if ($isShowName)
        <span class="text-nowrap text-white">{{ __('public.Love') }}</span>
    @endif
@endif