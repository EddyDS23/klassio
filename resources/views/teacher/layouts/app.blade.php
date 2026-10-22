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
                <div class="hidden items-center gap-3 sm:flex">
                    @yield('header-context')
                </div>
            @endif

            {{-- Botón hamburguesa móvil --}}
            <button type="button"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 md:hidden"
                    id="mobile-nav-open"
                    aria-label="Abrir menú de navegación"
                    aria-expanded="false"
                    aria-controls="mobile-nav-panel">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    @include('partials.mobile-navigation', [
        'navItems' => [
            ['route' => 'teacher.dashboard', 'label' => 'Inicio', 'icon' => '🏠', 'pattern' => 'teacher.dashboard'],
            ['route' => 'teacher.classes.index', 'label' => 'Mis clases', 'icon' => '📚', 'pattern' => 'teacher.classes.*'],
            ['route' => 'teacher.game-sessions.create', 'label' => 'Crear sala', 'icon' => '🎮', 'pattern' => 'teacher.game-sessions.create'],
        ],
        'userName' => auth()->user()->name ?? 'Profesor',
        'userRole' => 'Maestro',
        'logoutUrl' => url('/logout'),
        'avatarLetter' => strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)),
        'avatarBg' => 'bg-emerald-600',
        'brandTitle' => 'Klassio',
        'brandSubtitle' => 'Panel del profesor',
        'brandIcon' => 'K',
        'brandIconBg' => 'bg-emerald-600',
    ])

    @stack('scripts')
</body>
</html>