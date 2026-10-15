<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sala multijugador')</title>
    @include('partials.assets', ['theme' => 'teacher'])
    @yield('head')
</head>

<body>
    @yield('content')
    @stack('scripts')
</body>

</html>
