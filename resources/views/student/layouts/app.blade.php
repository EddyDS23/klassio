<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Espacio del estudiante') | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800 student-kids">

    @php
        $navItems = [
            [
                'route' => 'student.dashboard',
                'label' => 'Inicio',
                'pattern' => 'student.dashboard',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>',
            ],
            [
                'route' => 'student.classes.index',
                'label' => 'Mis clases',
                'pattern' => 'student.classes.*',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>',
            ],
        ];
    @endphp

    <div class="flex min-h-screen">

        <!-- Barra lateral: identidad cálida "atardecer" -->
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-orange-900 bg-orange-950 md:flex">

            <a href="{{ route('student.dashboard') }}"
               class="flex items-center gap-3 border-b border-orange-900 px-6 py-6 transition hover:bg-orange-900/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400">

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-600 text-xl font-bold text-white shadow-lg shadow-orange-950/40">
                    K
                </div>

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white">
                        Klassio
                    </h1>

                    <p class="text-xs font-bold uppercase tracking-widest text-orange-400">
                        Panel del estudiante
                    </p>
                </div>

            </a>

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6" aria-label="Navegación principal">

                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       aria-current="{{ request()->routeIs($item['pattern']) ? 'page' : 'false' }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3.5 text-base font-bold transition
                             {{ request()->routeIs($item['pattern']) ? 'bg-orange-600 text-white' : 'text-orange-400 hover:bg-orange-900/70 hover:text-white' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400">
                        <span class="shrink-0 [&_svg]:h-6 [&_svg]:w-6" aria-hidden="true">{!! $item['icon'] !!}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach

            </nav>

            <div class="border-t border-orange-900 px-4 py-5">
                <form action="{{ url('/logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-2xl bg-orange-900 px-4 py-3.5 text-base font-bold text-orange-500 transition hover:bg-red-900/30 hover:text-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        Cerrar sesión
                    </button>
                </form>
            </div>

        </aside>

        <!-- Contenido -->
        <div class="flex min-w-0 flex-1 flex-col md:ml-64">

            <!-- Barra superior: título de página + identidad (única franja) -->
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-x-3 gap-y-3 px-4 py-3 sm:px-6 lg:px-8">

                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        {{-- Botón hamburguesa móvil (esquina superior izquierda, solo < 768 px) --}}
                        <button type="button"
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-600 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-1 md:hidden"
                                id="mobile-nav-open"
                                aria-label="Abrir menú de navegación"
                                aria-expanded="false"
                                aria-controls="mobile-nav-panel">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>

                        <div class="min-w-0">
                            <p class="hidden text-[0.65rem] font-bold uppercase tracking-widest text-slate-500 sm:block">
                                Panel del estudiante
                            </p>
                            <h2 class="truncate text-lg font-bold tracking-tight text-slate-900 sm:text-xl">
                                @yield('page-title', 'Inicio')
                            </h2>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-600 text-lg font-bold text-white shadow-lg shadow-orange-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>

                        <div class="hidden sm:block">
                            <p class="text-sm font-bold text-slate-800">
                                {{ auth()->user()->name }}
                            </p>

                            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">
                                Estudiante
                            </p>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Contenido -->
            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">

                {{-- Barra de acciones de la página (encima del contenido) --}}
                @hasSection('page-actions')
                    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end sm:gap-3">
                        @yield('page-actions')
                    </div>
                @endif

                <!-- Mensaje de éxito -->
                @if (session('success'))
                    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm" role="status">

                        <svg class="mt-0.5 h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>

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
                    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm" role="alert">

                        <svg class="mt-0.5 h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>

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

            <!-- Pie -->
            <footer class="border-t border-slate-200 px-4 py-6 text-center text-xs text-slate-400 sm:px-6 lg:px-8">
                © {{ date('Y') }} Klassio · Plataforma educativa
            </footer>

        </div>

    </div>

    @include('partials.mobile-navigation', [
        'navItems' => [
            ['route' => 'student.dashboard', 'label' => 'Inicio', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>', 'pattern' => 'student.dashboard', 'exact' => true],
            ['route' => 'student.classes.index', 'label' => 'Mis clases', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>', 'pattern' => 'student.classes.*'],
        ],
        'userName' => auth()->user()->name ?? 'Estudiante',
        'userRole' => 'Estudiante',
        'logoutUrl' => url('/logout'),
        'avatarLetter' => strtoupper(substr(auth()->user()->name ?? 'E', 0, 1)),
        'avatarBg' => 'bg-orange-600',
        'brandTitle' => 'Klassio',
        'brandSubtitle' => 'Panel del estudiante',
        'brandIcon' => 'K',
        'brandIconBg' => 'bg-orange-600',
        'activeClasses' => 'bg-orange-600 text-white',
        'inactiveClasses' => 'text-orange-400 hover:bg-orange-900/70 hover:text-white',
        'focusRingClass' => 'focus:ring-orange-400',
        'panelClasses' => 'bg-orange-950',
        'panelBorder' => 'border-orange-900',
        'brandTitleClasses' => 'text-white',
        'brandSubtitleClasses' => 'text-orange-400',
        'closeBtnClasses' => 'text-orange-400 hover:bg-orange-900 hover:text-white',
        'userNameClasses' => 'text-white',
        'userRoleClasses' => 'text-orange-400',
        'logoutClasses' => 'bg-red-900/30 text-red-400 hover:bg-red-900/50 hover:text-red-300',
        'panelWidth' => 'w-full max-w-xs',
    ])

    @stack('scripts')
</body>
</html>
