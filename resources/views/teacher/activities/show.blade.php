<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $activity->title }} | Klassio</title>

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

        {{-- Regresar --}}
        <div class="mb-6">
            <a href="{{ route('teacher.activities.index', $activity->class_id) }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-emerald-700 transition hover:text-emerald-900">

                <span class="text-lg">←</span>
                Volver a actividades
            </a>
        </div>


        {{-- Mensajes --}}
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
                <span class="text-xl">✓</span>

                <div>
                    <p class="font-black">¡Operación exitosa!</p>
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
                    <p class="font-black">Ocurrió un problema</p>
                    <p class="text-sm font-medium">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        @endif


        {{-- Encabezado de actividad --}}
        <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-700 via-teal-600 to-cyan-600 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">

            <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">

                <div class="max-w-3xl">

                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider text-white backdrop-blur-sm">
                        <span>🎯</span>
                        Detalle de actividad
                    </div>

                    <h2 class="text-3xl font-black tracking-tight sm:text-4xl">
                        {{ $activity->title }}
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-emerald-50 sm:text-base">
                        Consulta la información, configura el juego, publica la actividad y revisa el desempeño de tus alumnos.
                    </p>

                </div>

                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-3xl bg-white/15 text-5xl shadow-inner backdrop-blur-sm">
                    🎮
                </div>

            </div>

        </section>


        {{-- Información general --}}
        <section class="mb-8">

            <div class="mb-4">
                <h3 class="text-2xl font-black text-slate-900">
                    Información de la actividad
                </h3>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Revisa los datos principales de esta actividad.
                </p>
            </div>


            <div class="grid gap-5 lg:grid-cols-3">

                {{-- Descripción --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">

                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-xl">
                            📝
                        </div>

                        <div>
                            <h4 class="font-black text-slate-900">
                                Descripción
                            </h4>

                            <p class="text-xs font-medium text-slate-500">
                                Información de la actividad
                            </p>
                        </div>
                    </div>

                    <p class="leading-7 text-slate-600">
                        {{ $activity->description ?: 'Sin descripción disponible para esta actividad.' }}
                    </p>

                </div>


                {{-- Estado --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-100 text-xl">
                            📌
                        </div>

                        <div>
                            <h4 class="font-black text-slate-900">
                                Estado
                            </h4>
    

    @if ($activity->mode === 'team' && $activity->status === 'draft')
        <a href="{{ route('teacher.teams.index', $activity->id) }}">
            Administrar equipos
        </a>

        <hr>
    @endif

                            <p class="text-xs font-medium text-slate-500">
                                Situación actual
                            </p>
                        </div>
                    </div>

                    @if ($activity->status === 'draft')
                        <span class="inline-flex rounded-full bg-amber-100 px-4 py-2 text-sm font-black text-amber-700">
                            Borrador
                        </span>
                    @elseif ($activity->status === 'published')
                        <span class="inline-flex rounded-full bg-emerald-100 px-4 py-2 text-sm font-black text-emerald-700">
                            Publicada
                        </span>
                    @elseif ($activity->status === 'closed')
                        <span class="inline-flex rounded-full bg-slate-200 px-4 py-2 text-sm font-black text-slate-700">
                            Cerrada
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-slate-100 px-4 py-2 text-sm font-black text-slate-600">
                            {{ $activity->status }}
                        </span>
                    @endif

                </div>

            </div>
    <p>
        <a href="{{ route('teacher.activities.report', $activity->id) }}">
            Ver reporte
        </a>
    </p>


            {{-- Datos --}}
            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                        Tipo
                    </p>

                    <p class="mt-2 font-black text-slate-900">
                        @switch($activity->type)
                            @case('crossword')
                                Crucigrama
                            @break

                            @case('kahoot')
                                Kahoot
                            @break

                            @case('wordsearch')
                            @case('word_search')
                                Sopa de letras
                            @break

                            @case('matching')
                                Unir conceptos
                            @break

                            @default
                                {{ $activity->type }}
                        @endswitch
                    </p>
                </div>


                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                        Modo
                    </p>

                    <p class="mt-2 font-black text-slate-900">
                        @if ($activity->mode === 'individual')
                            Individual
                        @elseif ($activity->mode === 'team')
                            Por equipos
                        @else
                            {{ $activity->mode }}
                        @endif
                    </p>
                </div>


                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                        Puntuación máxima
                    </p>

                    <p class="mt-2 font-black text-slate-900">
                        {{ $activity->max_score }} puntos
                    </p>
                </div>


                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                        Tiempo límite
                    </p>

                    <p class="mt-2 font-black text-slate-900">
                        {{ $activity->time_limit }} segundos
                    </p>
                </div>

            </div>


            {{-- Fecha límite --}}
            <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                            Fecha límite
                        </p>

                        <p class="mt-1 font-bold text-slate-800">
                            {{ $activity->due_at ?: 'Sin fecha límite' }}
                        </p>
                    </div>

                    <div class="text-2xl">
                        📅
                    </div>

                </div>

            </div>

        </section>


        {{-- Acciones principales --}}
        <section class="mb-8">

            <div class="mb-4">
                <h3 class="text-2xl font-black text-slate-900">
                    Acciones de la actividad
                </h3>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Administra la información y el estado de la actividad.
                </p>
            </div>


            <div class="grid gap-5 md:grid-cols-2">

                {{-- Editar --}}
                @if ($activity->status === 'draft')
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-2xl">
                                ✏️
                            </div>

                            <div>
                                <h4 class="font-black text-slate-900">
                                    Editar información
                                </h4>

                                <p class="text-sm font-medium text-slate-500">
                                    Modifica los datos generales.
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('teacher.activities.edit', $activity->id) }}"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-black text-white transition hover:bg-blue-700">
                            Editar actividad
                        </a>

                    </div>
                @endif


                {{-- Publicar o cerrar --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                    @if ($activity->status === 'draft')

                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-2xl">
                                🚀
                            </div>

                            <div>
                                <h4 class="font-black text-slate-900">
                                    Publicar actividad
                                </h4>

                                <p class="text-sm font-medium text-slate-500">
                                    Hazla visible para tus alumnos.
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('teacher.activities.publish', $activity->id) }}"
                            method="POST">
                            @csrf

                            <button type="submit"
                                class="w-full rounded-xl bg-emerald-600 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-700">
                                Publicar actividad
                            </button>
                        </form>

                    @elseif ($activity->status === 'published')

                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-2xl">
                                🔒
                            </div>

                            <div>
                                <h4 class="font-black text-slate-900">
                                    Cerrar actividad
                                </h4>

                                <p class="text-sm font-medium text-slate-500">
                                    Finaliza la participación.
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('teacher.activities.close', $activity->id) }}"
                            method="POST">
                            @csrf

                            <button type="submit"
                                class="w-full rounded-xl bg-rose-600 px-5 py-3 text-sm font-black text-white transition hover:bg-rose-700">
                                Cerrar actividad
                            </button>
                        </form>

                    @else

                        <div class="flex items-center gap-3 rounded-2xl bg-slate-100 p-4">
                            <span class="text-2xl">ℹ️</span>

                            <p class="text-sm font-bold text-slate-600">
                                Esta actividad no tiene acciones adicionales disponibles.
                            </p>
                        </div>

                    @endif

                </div>

            </div>

        </section>


        {{-- Configuración específica --}}
        @if ($activity->status === 'draft')

            <section class="mb-8">

                <div class="mb-4">
                    <h3 class="text-2xl font-black text-slate-900">
                        Configuración del juego
                    </h3>

                    <p class="mt-1 text-sm font-medium text-slate-500">
                        Configura o edita el contenido específico de esta actividad.
                    </p>
                </div>


                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                    @switch($activity->type)

                        {{-- Crucigrama --}}
                        @case('crossword')

                            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                                <div class="flex items-center gap-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-100 text-3xl">
                                        🧩
                                    </div>

                                    <div>
                                        <h4 class="font-black text-slate-900">
                                            Crucigrama
                                        </h4>

                                        <p class="text-sm font-medium text-slate-500">
                                            Administra las palabras y pistas.
                                        </p>
                                    </div>
                                </div>

                                @if ($activity->crossword && $activity->crossword->words()->exists())

                                    <a href="{{ route('teacher.crossword.edit', $activity->id) }}"
                                        class="rounded-xl bg-violet-600 px-5 py-3 text-center text-sm font-black text-white transition hover:bg-violet-700">
                                        Editar crucigrama
                                    </a>

                                @else

                                    <a href="{{ route('teacher.crossword.configure', $activity->id) }}"
                                        class="rounded-xl bg-violet-600 px-5 py-3 text-center text-sm font-black text-white transition hover:bg-violet-700">
                                        Configurar crucigrama
                                    </a>

                                @endif

                            </div>

                        @break


                        {{-- Kahoot --}}
                        @case('kahoot')

                            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                                <div class="flex items-center gap-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-100 text-3xl">
                                        🏆
                                    </div>

                                    <div>
                                        <h4 class="font-black text-slate-900">
                                            Kahoot
                                        </h4>

                                        <p class="text-sm font-medium text-slate-500">
                                            Administra las preguntas y respuestas.
                                        </p>
                                    </div>
                                </div>

                                @if ($activity->kahoot && $activity->kahoot->questions()->exists())

                                    <a href="{{ route('teacher.kahoot.edit', $activity->id) }}"
                                        class="rounded-xl bg-orange-500 px-5 py-3 text-center text-sm font-black text-white transition hover:bg-orange-600">
                                        Editar Kahoot
                                    </a>

                                @else

                                    <a href="{{ route('teacher.kahoot.configure', $activity->id) }}"
                                        class="rounded-xl bg-orange-500 px-5 py-3 text-center text-sm font-black text-white transition hover:bg-orange-600">
                                        Configurar Kahoot
                                    </a>

                                @endif

                            </div>

                        @break


                        {{-- Sopa de letras --}}
                        @case('wordsearch')
                        @case('word_search')

                            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                                <div class="flex items-center gap-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-100 text-3xl">
                                        🔎
                                    </div>

                                    <div>
                                        <h4 class="font-black text-slate-900">
                                            Sopa de letras
                                        </h4>

                                        <p class="text-sm font-medium text-slate-500">
                                            Administra las palabras escondidas.
                                        </p>
                                    </div>
                                </div>

                                @if ($activity->wordsearch && $activity->wordsearch->words()->exists())

                                    <a href="{{ route('teacher.wordsearch.edit', $activity->id) }}"
                                        class="rounded-xl bg-sky-600 px-5 py-3 text-center text-sm font-black text-white transition hover:bg-sky-700">
                                        Editar sopa de letras
                                    </a>

                                @else

                                    <a href="{{ route('teacher.wordsearch.configure', $activity->id) }}"
                                        class="rounded-xl bg-sky-600 px-5 py-3 text-center text-sm font-black text-white transition hover:bg-sky-700">
                                        Configurar sopa de letras
                                    </a>

                                @endif

                            </div>

                        @break


                        {{-- Unir conceptos --}}
                        @case('matching')

                            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                                <div class="flex items-center gap-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-pink-100 text-3xl">
                                        🔗
                                    </div>

                                    <div>
                                        <h4 class="font-black text-slate-900">
                                            Unir conceptos
                                        </h4>

                                        <p class="text-sm font-medium text-slate-500">
                                            Administra los conceptos y sus parejas.
                                        </p>
                                    </div>
                                </div>

                                @if ($activity->matching && $activity->matching->items()->exists())

                                    <a href="{{ route('teacher.matching.edit', $activity->id) }}"
                                        class="rounded-xl bg-pink-600 px-5 py-3 text-center text-sm font-black text-white transition hover:bg-pink-700">
                                        Editar unir conceptos
                                    </a>

                                @else

                                    <a href="{{ route('teacher.matching.configure', $activity->id) }}"
                                        class="rounded-xl bg-pink-600 px-5 py-3 text-center text-sm font-black text-white transition hover:bg-pink-700">
                                        Configurar unir conceptos
                                    </a>

                                @endif

                            </div>

                        @break


                        @default

                            <div class="rounded-2xl bg-slate-100 p-5">
                                <p class="font-bold text-slate-600">
                                    Este tipo de actividad no tiene configuración adicional.
                                </p>
                            </div>

                    @endswitch

                </div>

            </section>

        @endif


        {{-- Resultados --}}
        <section class="mb-8">

            <div class="mb-4">
                <h3 class="text-2xl font-black text-slate-900">
                    Resultados y seguimiento
                </h3>

                <p class="mt-1 text-sm font-medium text-slate-500">
                    Consulta el desempeño y la participación de tus alumnos.
                </p>
            </div>


            <div class="grid gap-5 md:grid-cols-3">

                <a href="{{ route('teacher.activities.results', $activity->id) }}"
                    class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg">

                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-3xl transition group-hover:scale-110">
                        📊
                    </div>

                    <h4 class="font-black text-slate-900">
                        Resultados de alumnos
                    </h4>

                    <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                        Revisa las calificaciones y el avance de cada estudiante.
                    </p>

                    <span class="mt-5 inline-flex font-black text-emerald-700">
                        Ver resultados →
                    </span>

                </a>


                <a href="{{ route('teacher.activities.ranking', $activity->id) }}"
                    class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg">

                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 text-3xl transition group-hover:scale-110">
                        🏅
                    </div>

                    <h4 class="font-black text-slate-900">
                        Ranking
                    </h4>

                    <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                        Consulta la clasificación de los participantes.
                    </p>

                    <span class="mt-5 inline-flex font-black text-amber-700">
                        Ver ranking →
                    </span>

                </a>


                <a href="{{ route('teacher.activities.report', $activity->id) }}"
                    class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-cyan-300 hover:shadow-lg">

                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-100 text-3xl transition group-hover:scale-110">
                        📄
                    </div>

                    <h4 class="font-black text-slate-900">
                        Reporte
                    </h4>

                    <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                        Consulta el reporte general de esta actividad.
                    </p>

                    <span class="mt-5 inline-flex font-black text-cyan-700">
                        Ver reporte →
                    </span>

                </a>

            </div>

        </section>


        {{-- Regresar --}}
        <div class="border-t border-slate-200 pt-6">

            <a href="{{ route('teacher.activities.index', $activity->class_id) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-5 py-3 text-sm font-black text-white transition hover:bg-slate-900">

                ← Volver a actividades
            </a>

        </div>

    </main>

</body>

</html>