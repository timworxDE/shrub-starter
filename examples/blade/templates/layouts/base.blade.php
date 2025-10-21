<html>
<head>
    <title>SHRUB 🌿 @yield('title')</title>
    @vite(['styles/app.css', 'scripts/app.js'], 'assets')
</head>
<body>
    @section('header')
        @include('partials.header')
    @show

    @section('main')
        <main class="container">
            @yield('content')
        </main>
    @show

    @section('footer')
        @include('partials.footer')
    @show
</body>
</html>