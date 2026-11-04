<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Panel del profesor') | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <div class="flex min-h-screen">

        <!-- Barra lateral -->
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-emerald-200 bg-white md:flex">

            <a href="{{ route('teacher.dashboard') }}"
               class="flex items-center gap-3 border-b border-emerald-100 px-6 py-6">

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-xl font-black text-white shadow-lg shadow-emerald-200">
                    K
                </div>

                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-900">
                        Klassio
                    </h1>

                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">
                        Panel del maestro
                    </p>
                </div>

            </a>

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">

                @php
                    $navItems = [
                        ['route' => 'teacher.dashboard', 'label' => 'Inicio', 'icon' => '🏠', 'pattern' => 'teacher.dashboard'],
                        ['route' => 'teacher.classes.index', 'label' => 'Mis clases', 'icon' => '📚', 'pattern' => 'teacher.classes.*'],
                        ['route' => 'teacher.game-sessions.create', 'label' => 'Crear sala', 'icon' => '🎮', 'pattern' => 'teacher.game-sessions.create'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition
                             {{ request()->routeIs($item['pattern']) ? 'bg-emerald-50 text-emerald-700 shadow-inner' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                        <span class="text-lg">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach

            </nav>

            <div class="border-t border-emerald-100 px-4 py-5">
                <p class="mb-3 truncate px-4 text-xs font-bold text-slate-400">
                    {{ auth()->user()->name }}
                </p>

                <form action="{{ url('/logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-xl border border-emerald-100 bg-white px-4 py-3 text-sm font-bold text-emerald-700 transition hover:bg-emerald-50 hover:text-emerald-900">
                        <span>↪</span>
                        Cerrar sesión
                    </button>
                </form>
            </div>

        </aside>

        <!-- Contenido -->
        <div class="flex min-w-0 flex-1 flex-col md:ml-64">

            <!-- Barra superior -->
            <header class="sticky top-0 z-20 border-b border-emerald-100 bg-white/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-lg font-black text-white shadow-lg shadow-emerald-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>

                        <div class="hidden sm:block">
                            <p class="text-sm font-bold text-slate-800">
                                {{ auth()->user()->name }}
                            </p>

                            <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">
                                Maestro
                            </p>
                        </div>
                    </div>

                    {{-- Botón hamburguesa móvil --}}
                    <button type="button"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-500 transition hover:bg-emerald-50 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 md:hidden"
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

            <!-- Contenido -->
            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">

                {{-- Título de página --}}
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                            Área del maestro
                        </p>
                        <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">
                            @yield('page-title', 'Dashboard')
                        </h2>
                    </div>
                    @hasSection('page-actions')
                        <div class="flex items-center gap-3">
                            @yield('page-actions')
                        </div>
                    @endif
                </div>

                <!-- Mensaje de éxito -->
                @if (session('success'))
                    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm">

                        <span class="text-xl">✓</span>

                        <div>
                            <p class="font-bold">
                                ¡Operación exitosa!
                            </p>

                            <p class="text-sm">
                                {{ session('success') }}
                            </p>
                        </div>

                    </div>
                @endif

                <!-- Mensaje de error -->
                @if (session('error'))
                    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm">

                        <span class="text-xl">!</span>

                        <div>
                            <p class="font-bold">
                                Ocurrió un problema
                            </p>

                            <p class="text-sm">
                                {{ session('error') }}
                            </p>
                        </div>

                    </div>
                @endif

                @yield('content')

            </main>

        </div>

    </div>

    @include('partials.mobile-navigation', [
        'navItems' => $navItems ?? [
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