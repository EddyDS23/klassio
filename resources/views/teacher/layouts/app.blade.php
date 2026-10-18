<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel del profesor') | Klassio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="@yield('brand-url', route('teacher.dashboard'))" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-xl font-black text-white shadow-lg shadow-emerald-200">K</div>
                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-900">Klassio</h1>
                    <p class="text-xs font-medium text-slate-500">@yield('brand-caption', 'Panel del profesor')</p>
                </div>
            </a>

            @hasSection('header-context')
                <div class="flex items-center gap-3">
                    @yield('header-context')
                </div>
            @endif
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>
