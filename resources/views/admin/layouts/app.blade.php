<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Panel administrativo') | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <div class="flex min-h-screen">

        <!-- Barra lateral -->
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 flex-col border-r border-slate-800 bg-slate-900 md:flex">

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 border-b border-slate-800 px-6 py-6">

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-400 text-xl font-black text-slate-900 shadow-lg shadow-amber-900/40">
                    K
                </div>

                <div>
                    <h1 class="text-xl font-black tracking-tight text-white">
                        Klassio
                    </h1>

                    <p class="text-xs font-bold uppercase tracking-widest text-amber-400">
                        Administración
                    </p>
                </div>

            </a>

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">

                @php
                    $navItems = [
                        ['route' => 'admin.dashboard', 'label' => 'Inicio', 'icon' => '🏠', 'pattern' => 'admin.dashboard'],
                        ['route' => 'admin.users.index', 'label' => 'Usuarios', 'icon' => '👥', 'pattern' => 'admin.users.*'],
                        ['route' => 'admin.classes.index', 'label' => 'Clases', 'icon' => '📚', 'pattern' => 'admin.classes.*'],
                        ['route' => 'admin.activities.index', 'label' => 'Actividades', 'icon' => '🎯', 'pattern' => 'admin.activities.*'],
                        ['route' => 'admin.results.index', 'label' => 'Resultados', 'icon' => '🏆', 'pattern' => 'admin.results.*'],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold transition
                              {{ request()->routeIs($item['pattern']) ? 'bg-slate-800 text-white shadow-inner' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <span class="text-lg">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endforeach

            </nav>

            <div class="border-t border-slate-800 px-4 py-5">
                <p class="mb-3 truncate px-4 text-xs font-bold text-slate-400">
                    {{ auth()->user()->name }}
                </p>

                <form action="{{ url('/logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-slate-400 transition hover:bg-red-500/10 hover:text-red-400">
                        <span>↪</span>
                        Cerrar sesión
                    </button>
                </form>
            </div>

        </aside>

        <!-- Contenido -->
        <div class="flex min-w-0 flex-1 flex-col md:ml-64">

            <!-- Barra superior -->
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-lg font-black text-amber-400 shadow-lg shadow-slate-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>

                        <div>
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

            <!-- Navegación móvil -->
            <nav class="sticky top-[73px] z-10 flex gap-2 overflow-x-auto border-b border-slate-200 bg-white px-4 py-3 md:hidden">

                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="whitespace-nowrap rounded-full px-4 py-2 text-xs font-bold transition
                              {{ request()->routeIs($item['pattern']) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach

                <form action="{{ url('/logout') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit"
                            class="whitespace-nowrap rounded-full bg-red-100 px-4 py-2 text-xs font-bold text-red-700 transition hover:bg-red-200">
                        ↪ Salir
                    </button>
                </form>

            </nav>

            <!-- Contenido -->
            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">

                {{-- Título de página --}}
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-slate-500">
                            Panel de administración
                        </p>
                        <h2 class="mt-1 text-2xl font-black tracking-tight text-slate-900">
                            @yield('page-title')
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

                        <span class="text-xl">⚠️</span>

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

    @stack('scripts')

</body>

</html>