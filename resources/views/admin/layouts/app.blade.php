<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Panel administrativo') | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    @php
        $navItems = [
            [
                'route' => 'admin.dashboard',
                'label' => 'Inicio',
                'pattern' => 'admin.dashboard',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>',
            ],
            [
                'route' => 'admin.users.index',
                'label' => 'Usuarios',
                'pattern' => 'admin.users.*',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>',
            ],
            [
                'route' => 'admin.classes.index',
                'label' => 'Clases',
                'pattern' => 'admin.classes.*',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" /></svg>',
            ],
            [
                'route' => 'admin.activities.index',
                'label' => 'Actividades',
                'pattern' => 'admin.activities.*',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-3.409-.042.75.75 0 0 1-.707-.632 48.264 48.264 0 0 1-.034-2.897.75.75 0 0 1 .515-.707 48.334 48.334 0 0 1 2.887-.085v0c.355 0 .676-.186.959-.401.29-.221.634-.349 1.003-.349 1.036 0 1.875-1.007 1.875-2.25S12.535 1.5 11.5 1.5s-1.875 1.007-1.875 2.25c0 .369.128.713.349 1.003.215.283.401.604.401.959v0c0 .355-.186.676-.401.959-.221.29-.349.634-.349 1.003 0 1.036-1.007 1.875-2.25 1.875s-1.875-1.007-1.875-2.25c0-.369.128-.713.349-1.003.215-.283.401-.604.401-.959v0a.64.64 0 0 0-.657-.643 48.39 48.39 0 0 0-3.409.042.75.75 0 0 0-.707.632 48.264 48.264 0 0 0-.034 2.897.75.75 0 0 0 .515.707c.438.154.902.226 1.397.226v0c.466 0 .872-.054 1.21-.14v5.628c0 1.499 1.193 2.755 2.691 2.755h.002c.325 0 .591.266.591.591v4.875c0 .414.336.75.75.75h.006a.75.75 0 0 0 .75-.75v-4.875c0-.325.266-.591.591-.591h.002c1.498 0 2.691-1.256 2.691-2.755V9.75c.338.086.744.14 1.21.14v0c.495 0 .959-.072 1.397-.226a.75.75 0 0 0 .515-.707 48.264 48.264 0 0 0-.034-2.897.75.75 0 0 0-.707-.632 48.39 48.39 0 0 0-3.409-.042.64.64 0 0 0-.657.643Z" /></svg>',
            ],
            [
                'route' => 'admin.results.index',
                'label' => 'Resultados',
                'pattern' => 'admin.results.*',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>',
            ],
        ];
    @endphp

    <div class="flex min-h-screen">

        <!-- Barra lateral -->
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-800 bg-slate-900 md:flex">

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 border-b border-slate-800 px-6 py-6 transition hover:bg-slate-800/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-600 text-xl font-black text-white shadow-lg shadow-indigo-950/40">
                    K
                </div>

                <div>
                    <h1 class="text-xl font-black tracking-tight text-white">
                        Klassio
                    </h1>

                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-400">
                        Administración
                    </p>
                </div>

            </a>

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6" aria-label="Navegación principal">

                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       aria-current="{{ request()->routeIs($item['pattern']) ? 'page' : 'false' }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition
                             {{ request()->routeIs($item['pattern']) ? 'bg-indigo-600 text-white' : 'text-slate-500 hover:bg-slate-800 hover:text-white' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                        <span class="shrink-0 [&_svg]:h-5 [&_svg]:w-5" aria-hidden="true">{!! $item['icon'] !!}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach

            </nav>

            <div class="border-t border-slate-800 px-4 py-5">
                <form action="{{ url('/logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-xl bg-slate-800 px-4 py-3 text-sm font-bold text-slate-400 transition hover:bg-red-900/30 hover:text-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
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
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-600 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 md:hidden"
                                id="mobile-nav-open"
                                aria-label="Abrir menú de navegación"
                                aria-expanded="false"
                                aria-controls="mobile-nav-panel">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>

                        <div class="min-w-0">
                            <p class="hidden text-[0.65rem] font-black uppercase tracking-widest text-slate-500 sm:block">
                                Panel de administración
                            </p>
                            <h2 class="truncate text-lg font-black tracking-tight text-slate-900 sm:text-xl">
                                @yield('page-title')
                            </h2>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-lg font-black text-white shadow-lg shadow-indigo-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>

                        <div class="hidden sm:block">
                            <p class="text-sm font-bold text-slate-800">
                                {{ auth()->user()->name }}
                            </p>

                            <p class="text-xs font-bold uppercase tracking-widest text-slate-500">
                                Administrador
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

                @if (session('success'))
                    <div data-toast="success" hidden>{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div data-toast="error" hidden>{{ session('error') }}</div>
                @endif

                @include('partials.toast')

                @yield('content')

            </main>

        </div>

    </div>

    @include('partials.mobile-navigation', [
        'navItems' => $navItems,
        'userName' => auth()->user()->name ?? 'Administrador',
        'userRole' => 'Administrador',
        'logoutUrl' => url('/logout'),
        'avatarLetter' => strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)),
        'avatarBg' => 'bg-indigo-600',
        'brandTitle' => 'Klassio',
        'brandSubtitle' => 'Administración',
        'brandIcon' => 'K',
        'brandIconBg' => 'bg-indigo-600',
        // Tema espejo de la sidebar (slate-900 oscuro + indigo)
        'activeClasses' => 'bg-indigo-600 text-white',
        'inactiveClasses' => 'text-slate-400 hover:bg-slate-800 hover:text-white',
        'focusRingClass' => 'focus:ring-indigo-400',
        'panelClasses' => 'bg-slate-900',
        'panelWidth' => 'w-full max-w-xs',
        'panelBorder' => 'border-slate-800',
        'brandTitleClasses' => 'text-white',
        'brandSubtitleClasses' => 'text-indigo-400',
        'closeBtnClasses' => 'text-slate-400 hover:bg-slate-800 hover:text-white',
        'userNameClasses' => 'text-white',
        'userRoleClasses' => 'text-slate-400',
        'logoutClasses' => 'bg-slate-800 text-slate-400 hover:bg-red-900/30 hover:text-red-300',
    ])

    @stack('scripts')

</body>
</html>
