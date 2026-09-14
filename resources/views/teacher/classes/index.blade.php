<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis clases | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    {{-- Barra superior --}}
    <header class="border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">

            <a href="{{ route('teacher.dashboard') }}"
                class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-xl font-black text-white shadow-lg shadow-emerald-200">
                    K
                </div>

                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-900">
                        Klassio
                    </h1>

                    <p class="text-xs font-medium text-slate-500">
                        Panel del profesor
                    </p>
                </div>
            </a>

            <div class="flex items-center gap-3">

                <span class="hidden text-sm font-semibold text-slate-600 sm:block">
                    {{ auth()->user()->name }}
                </span>

                <form action="{{ url('/logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                        Cerrar sesión
                    </button>
                </form>

            </div>

        </div>
    </header>


    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Encabezado --}}
        <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-700 via-teal-600 to-cyan-600 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">

            <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-center">

                <div>

                    <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider backdrop-blur-sm">
                        <span>🏫</span>
                        Administración
                    </div>

                    <h2 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Mis clases
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-emerald-50 sm:text-base">
                        Administra las clases que has creado, consulta sus códigos y organiza tus espacios de aprendizaje.
                    </p>

                </div>

                <a href="{{ route('teacher.classes.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-black text-emerald-700 shadow-lg transition hover:bg-emerald-50">

                    <span class="text-lg">+</span>
                    Crear clase
                </a>

            </div>

        </section>


        {{-- Mensajes --}}
        @if (session('success'))

            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">

                <span class="text-xl">✓</span>

                <div>
                    <p class="font-black">
                        ¡Operación exitosa!
                    </p>

                    <p class="text-sm font-medium">
                        {{ session('success') }}
                    </p>
                </div>

            </div>

        @endif


        @if (session('error'))

            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800 shadow-sm">

                <span class="text-xl">!</span>

                <div>
                    <p class="font-black">
                        Ocurrió un problema
                    </p>

                    <p class="text-sm font-medium">
                        {{ session('error') }}
                    </p>
                </div>

            </div>

        @endif


        {{-- Título de sección --}}
        <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">

            <div>
                <h3 class="text-2xl font-black text-slate-900">
                    Clases creadas
                </h3>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Consulta y administra tus clases desde un solo lugar.
                </p>
            </div>

            <div class="inline-flex w-fit items-center rounded-full bg-emerald-100 px-4 py-2 text-sm font-black text-emerald-700">
                {{ $classes->count() }} clases
            </div>

        </div>


        {{-- Tarjetas de clases --}}
        <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

            @forelse ($classes as $class)

                <article class="group flex flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl">

                    {{-- Cabecera de tarjeta --}}
                    <div class="bg-gradient-to-br from-emerald-600 to-teal-600 p-6 text-white">

                        <div class="flex items-start justify-between gap-4">

                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 text-3xl shadow-inner backdrop-blur-sm">
                                📚
                            </div>

                            @if ($class->status === 'active')

                                <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">
                                    Activa
                                </span>

                            @else

                                <span class="rounded-full bg-slate-900/20 px-3 py-1 text-xs font-black uppercase tracking-wide text-white">
                                    Archivada
                                </span>

                            @endif

                        </div>

                        <h4 class="mt-5 break-words text-2xl font-black tracking-tight">
                            {{ $class->name }}
                        </h4>

                    </div>


                    {{-- Contenido --}}
                    <div class="flex flex-1 flex-col p-6">

                        <p class="min-h-[72px] text-sm font-medium leading-6 text-slate-600">
                            {{ $class->description ?: 'Sin descripción disponible para esta clase.' }}
                        </p>


                        {{-- Datos --}}
                        <div class="mt-6 space-y-4">

                            <div class="rounded-2xl bg-slate-50 p-4">

                                <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                                    Código de acceso
                                </p>

                                <p class="mt-2 break-all text-xl font-black tracking-widest text-emerald-700">
                                    {{ $class->code }}
                                </p>

                            </div>


                            <div class="grid grid-cols-2 gap-3">

                                <div class="rounded-2xl border border-slate-100 p-3">

                                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                                        Estado
                                    </p>

                                    <p class="mt-1 text-sm font-black text-slate-800">
                                        @if ($class->status === 'active')
                                            Activa
                                        @else
                                            Archivada
                                        @endif
                                    </p>

                                </div>


                                <div class="rounded-2xl border border-slate-100 p-3">

                                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                                        Creada
                                    </p>

                                    <p class="mt-1 text-sm font-black text-slate-800">
                                        {{ $class->created_at->format('d/m/Y') }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Acciones --}}
                        <div class="mt-6 space-y-3 border-t border-slate-100 pt-6">

                            <a href="{{ route('teacher.classes.show', $class->id) }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-700">

                                Ver clase
                                <span>→</span>
                            </a>


                            @if ($class->status === 'active')

                                <a href="{{ route('teacher.classes.edit', $class->id) }}"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-black text-slate-700 transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700">

                                    ✏️
                                    Editar clase
                                </a>


                                <form action="{{ route('teacher.classes.archive', $class->id) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-black text-rose-700 transition hover:bg-rose-100">

                                        📦
                                        Archivar clase
                                    </button>

                                </form>

                            @else

                                <form action="{{ route('teacher.classes.unarchive', $class->id) }}"
                                    method="POST">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-cyan-200 bg-cyan-50 px-5 py-3 text-sm font-black text-cyan-700 transition hover:bg-cyan-100">

                                        ↩️
                                        Desarchivar clase
                                    </button>

                                </form>

                            @endif

                        </div>

                    </div>

                </article>

            @empty

                {{-- Estado vacío --}}
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm md:col-span-2 xl:col-span-3">

                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 text-4xl">
                        🏫
                    </div>

                    <h4 class="mt-6 text-2xl font-black text-slate-900">
                        No tienes clases creadas
                    </h4>

                    <p class="mx-auto mt-3 max-w-md text-sm font-medium leading-6 text-slate-500">
                        Crea tu primera clase para comenzar a organizar alumnos, actividades y juegos educativos.
                    </p>

                    <a href="{{ route('teacher.classes.create') }}"
                        class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700">

                        <span class="text-lg">+</span>
                        Crear mi primera clase
                    </a>

                </div>

            @endforelse

        </section>


        {{-- Regreso al dashboard --}}
        <div class="mt-8 border-t border-slate-200 pt-6">

            <a href="{{ route('teacher.dashboard') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-5 py-3 text-sm font-black text-white transition hover:bg-slate-900">

                ← Volver al dashboard
            </a>

        </div>

    </main>

</body>

</html>