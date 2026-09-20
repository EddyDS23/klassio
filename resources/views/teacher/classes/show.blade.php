<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $class->name }} | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    {{-- Barra superior --}}
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white shadow-sm">
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

        {{-- Navegación --}}
        <div class="mb-6">
            <a href="{{ route('teacher.classes.index') }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 transition hover:text-emerald-900">

                <span class="text-lg">←</span>
                Volver a mis clases
            </a>
        </div>


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


        {{-- Encabezado de la clase --}}
        <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-700 via-teal-600 to-cyan-600 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">

            <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">

                <div class="min-w-0">

                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider backdrop-blur-sm">
                        <span>🏫</span>
                        Detalle de clase
                    </div>

                    <h2 class="break-words text-3xl font-black tracking-tight sm:text-4xl">
                        {{ $class->name }}
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-emerald-50 sm:text-base">
                        Administra la información, el acceso, los alumnos y las actividades de esta clase.
                    </p>

                </div>

                <div class="hidden h-24 w-24 shrink-0 items-center justify-center rounded-3xl bg-white/15 text-5xl shadow-inner backdrop-blur-sm sm:flex">
                    📚
                </div>

            </div>

        </section>


        {{-- Información general --}}
        <section class="mb-8">

            <div class="mb-5">
                <h3 class="text-2xl font-black text-slate-900">
                    Información de la clase
                </h3>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Consulta los datos principales de este espacio de aprendizaje.
                </p>
            </div>


            <div class="grid gap-5 lg:grid-cols-3">

                {{-- Descripción --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">

                    <div class="mb-4 flex items-center gap-3">

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-2xl">
                            📝
                        </div>

                        <div>
                            <h4 class="font-black text-slate-900">
                                Descripción
                            </h4>

                            <p class="text-xs font-medium text-slate-500">
                                Información general
                            </p>
                        </div>

                    </div>

                    <p class="leading-7 text-slate-600">
                        {{ $class->description ?: 'Sin descripción disponible para esta clase.' }}
                    </p>

                </div>


                {{-- Estado --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                    <div class="mb-4 flex items-center gap-3">

                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-2xl">
                            📌
                        </div>

                        <div>
                            <h4 class="font-black text-slate-900">
                                Estado
                            </h4>

                            <p class="text-xs font-medium text-slate-500">
                                Situación actual
                            </p>
                        </div>

                    </div>

                    @if ($class->status === 'active')

                        <span class="inline-flex rounded-full bg-emerald-100 px-4 py-2 text-sm font-black text-emerald-700">
                            Clase activa
                        </span>

                    @else

                        <span class="inline-flex rounded-full bg-slate-200 px-4 py-2 text-sm font-black text-slate-700">
                            Clase archivada
                        </span>

                    @endif

                </div>

            </div>


            {{-- Datos adicionales --}}
            <div class="mt-5 grid gap-4 sm:grid-cols-2">

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                        Fecha de creación
                    </p>

                    <p class="mt-2 font-black text-slate-900">
                        {{ $class->created_at->format('d/m/Y') }}
                    </p>

                </div>


                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                        Nombre de la clase
                    </p>

                    <p class="mt-2 break-words font-black text-slate-900">
                        {{ $class->name }}
                    </p>

                </div>

            </div>

        </section>


        {{-- Código de acceso --}}
        <section class="mb-8">

            <div class="rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-600 p-6 text-white shadow-xl shadow-indigo-200 sm:p-8">

                <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">

                    <div class="max-w-2xl">

                        <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider backdrop-blur-sm">
                            <span>🔑</span>
                            Acceso de estudiantes
                        </div>

                        <h3 class="text-2xl font-black sm:text-3xl">
                            Código de acceso
                        </h3>

                        <p class="mt-3 text-sm font-medium leading-6 text-indigo-100">
                            Comparte este código con tus estudiantes para que puedan unirse a la clase.
                        </p>

                    </div>

                    <div class="rounded-2xl bg-white px-6 py-5 text-center shadow-lg">

                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">
                            Código
                        </p>

                        <p class="mt-2 break-all text-3xl font-black tracking-[0.2em] text-indigo-700">
                            {{ $class->code }}
                        </p>

                    </div>

                </div>


                <div class="mt-6 border-t border-white/20 pt-6">

                    <form action="{{ route('teacher.classes.regenerate-code', $class->id) }}"
                        method="POST">

                        @csrf
                        @method('PATCH')

                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-black text-indigo-700 transition hover:bg-indigo-50 sm:w-auto">

                            🔄
                            Regenerar código
                        </button>

                    </form>

                </div>

            </div>

        </section>


        {{-- Alumnos y actividades --}}
        <section class="mb-8">

            <div class="mb-5">
                <h3 class="text-2xl font-black text-slate-900">
                    Administración de la clase
                </h3>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Accede rápidamente a los alumnos y a las actividades.
                </p>
            </div>


            <div class="grid gap-5 md:grid-cols-2">

                {{-- Alumnos --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-100 text-3xl">
                        👥
                    </div>

                    <h4 class="text-xl font-black text-slate-900">
                        Alumnos
                    </h4>

                    <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                        Consulta y administra los alumnos inscritos en esta clase.
                    </p>

                    <a href="{{ route('teacher.classes.students', $class->id) }}"
                        class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-cyan-600 px-5 py-3 text-sm font-black text-white transition hover:bg-cyan-700">

                        Ver alumnos
                        <span>→</span>
                    </a>

                </div>


                {{-- Actividades --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-3xl">
                        🎮
                    </div>

                    <h4 class="text-xl font-black text-slate-900">
                        Actividades
                    </h4>

                    <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                        Crea y administra las actividades y juegos educativos de esta clase.
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">

                        <a href="{{ route('teacher.activities.index', $class->id) }}"
                            class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-black text-emerald-700 transition hover:bg-emerald-100">

                            Ver actividades
                        </a>

                        <a href="{{ route('teacher.activities.create', $class->id) }}"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-3 text-center text-sm font-black text-white transition hover:bg-emerald-700">

                            + Crear actividad
                        </a>

                    </div>

                </div>

            </div>

        </section>


        {{-- Administrar clase --}}
        <section class="mb-8">

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <div class="mb-6">

                    <h3 class="text-2xl font-black text-slate-900">
                        Administrar clase
                    </h3>

                    <p class="mt-1 text-sm font-medium text-slate-500">
                        Modifica los datos o cambia el estado de la clase.
                    </p>

                </div>


                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">

                    <a href="{{ route('teacher.classes.edit', $class->id) }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-black text-white transition hover:bg-blue-700">

                        ✏️
                        Editar clase
                    </a>


                    @if ($class->status === 'active')

                        <form action="{{ route('teacher.classes.archive', $class->id) }}"
                            method="POST">

                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-3 text-sm font-black text-white transition hover:bg-rose-700 sm:w-auto">

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
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-cyan-600 px-5 py-3 text-sm font-black text-white transition hover:bg-cyan-700 sm:w-auto">

                                ↩️
                                Desarchivar clase
                            </button>

                        </form>

                    @endif

                </div>

            </div>

        </section>


    </main>

</body>

</html>