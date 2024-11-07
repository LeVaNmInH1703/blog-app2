@foreach ($errors->get($name) as $error) 
    <span class="text-info">{{ $error }}</span>
@endforeach