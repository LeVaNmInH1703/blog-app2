<!DOCTYPE html>
<html lang="en">


<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    
    <title>@yield('title','Social network')</title>
    @yield('style')
    @vite('resources/js/app.js')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body>
    @yield('content')
    <script src=" {{ asset('js/main.min.js') }}"></script>
    <script src=" {{ asset('js/script.js') }}"></script>
    @yield('script')
</body>

</html>
