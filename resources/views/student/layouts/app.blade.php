<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Espacio del estudiante') | Klassio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 student-kids">
    <div class="flex min-h-screen flex-col lg:flex-row">
        <aside class="hidden w-64 shrink-0 flex-col border-r border-slate-200 bg-white lg:sticky lg:top-0 lg:flex lg:h-screen lg:overflow-y-auto">
            <div class="flex h-24 items-center gap-3 px-7">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-xl font-extrabold text-white">K</div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Klassio</h1>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Aprende y conecta</p>
                </div>
            </div>

            <nav class="flex-1 px-4 py-5" aria-label="Navegación principal">
                <p class="mb-4 px-3 text-[11px] font-bold uppercase tracking-widest text-slate-400">Menú principal</p>

                <a href="{{ route('student.dashboard') }}"
                    @class([
                        'mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition',
                        'bg-indigo-50 font-bold text-indigo-600' => request()->routeIs('student.dashboard'),
                        'font-medium text-slate-500 hover:bg-slate-50 hover:text-indigo-600' => ! request()->routeIs('student.dashboard'),
                    ])>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l9-9 9 9M5 10v10h14V10" />
                    </svg>
                    Inicio
                </a>

                <a href="{{ route('student.classes.index') }}"
                    @class([
                        'mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition',
                        'bg-indigo-50 font-bold text-indigo-600' => request()->routeIs('student.classes.*'),
                        'font-medium text-slate-500 hover:bg-slate-50 hover:text-indigo-600' => ! request()->routeIs('student.classes.*'),
                    ])>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8 7h8M8 11h8M8 15h5" />
                    </svg>
                    Mis clases
                </a>

                <a href="{{ route('student.dashboard') }}#juegos"
                    class="mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-indigo-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 8h12a4 4 0 014 4v3a4 4 0 01-4 4h-1l-3-3H10l-3 3H6a4 4 0 01-4-4v-3a4 4 0 014-4zM8 12v4M6 14h4M16 13h.01M19 11h.01" />
                    </svg>
                    Juegos educativos
                </a>

                <a href="{{ route('student.dashboard') }}#actividades"
                    class="mb-2 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-500 transition hover:bg-slate-50 hover:text-indigo-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5h6M9 3h6v4H9V3zM6 7h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2zM8 12h8M8 16h5" />
                    </svg>
                    Actividades
                </a>
            </nav>

            <div class="border-t border-slate-100 p-4">
                <div class="mb-4 flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-600">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400">Estudiante</p>
                    </div>
                </div>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-500 transition hover:bg-rose-50 hover:text-rose-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 17l5-5-5-5M15 12H3M21 3v18" />
                        </svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </aside>

<main class="min-w-0 flex-1">
            <header class="sticky top-4 z-20 mx-4 mt-4 flex flex-col sm:h-auto rounded-2xl border border-slate-200 bg-white px-5 shadow-sm sm:mx-6 sm:px-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Mi espacio académico</p>
                        <h2 class="mt-1 text-lg font-bold text-slate-800">@yield('page-heading', 'Inicio')</h2>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400">Estudiante</p>
                        </div>
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-600 font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>

                        {{-- Botón hamburguesa móvil --}}
                        <button type="button"
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 md:hidden"
                                id="mobile-nav-open"
                                aria-label="Abrir menú de navegación"
                                aria-expanded="false"
                                aria-controls="mobile-nav-panel">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            @yield('content')
        </main>
    </div>

    @include('partials.mobile-navigation', [
        'navItems' => [
            ['route' => 'student.dashboard', 'label' => 'Inicio', 'icon' => '🏠', 'pattern' => 'student.dashboard', 'exact' => true],
            ['route' => 'student.classes.index', 'label' => 'Mis clases', 'icon' => '📚', 'pattern' => 'student.classes.*'],
            ['route' => 'student.dashboard', 'label' => 'Juegos educativos', 'icon' => '🎮', 'pattern' => 'student.dashboard', 'fragment' => 'juegos'],
            ['route' => 'student.dashboard', 'label' => 'Actividades', 'icon' => '📝', 'pattern' => 'student.dashboard', 'fragment' => 'actividades'],
        ],
        'userName' => auth()->user()->name ?? 'Estudiante',
        'userRole' => 'Estudiante',
        'logoutUrl' => url('/logout'),
        'avatarLetter' => strtoupper(substr(auth()->user()->name ?? 'E', 0, 1)),
        'avatarBg' => 'bg-indigo-600',
        'brandTitle' => 'Klassio',
        'brandSubtitle' => 'Aprende y conecta',
        'brandIcon' => 'K',
        'brandIconBg' => 'bg-indigo-600',
    ])

    @stack('scripts')
</body>
</html>