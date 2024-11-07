@if (isset($feeling))
    <img height="{{ $size }}" width="{{ $size }}" alt='{{ __('public.' . $feeling->alt) }}'
        src='{{ $feeling->src }}'>
    @if ($isShowName)
        <span class="text-nowrap" style="color: {{ $feeling->color_text }}">{{ __('public.' . $feeling->alt) }}</span>
    @endif
@elseif ($isShowNull)
    <i class="fa-regular fa-heart"></i>
    @if ($isShowName)
        <span class="text-nowrap text-white">{{ __('public.Love') }}</span>
    @endif
@endif