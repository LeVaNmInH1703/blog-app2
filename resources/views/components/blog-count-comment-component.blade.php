@if ($number>0)
    
<span class="text-secondary"><i class="fas fa-comment"></i> {{ $number }}
    {{ __('public.comment', ['isPlural' => $number > 1 ? 's' : '']) }}</span>
    @endif