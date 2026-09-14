<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Actividades | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <!-- Barra superior -->
    <header class="border-b border-emerald-100 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

            <a href="{{ route('teacher.classes.index') }}"
               class="flex items-center gap-3">

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-2xl font-black text-white shadow-lg shadow-emerald-200">
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

            <span class="hidden rounded-full bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700 sm:inline-flex">
                Actividades
            </span>

        </div>
    </header>


    <!-- Contenido principal -->
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <!-- Encabezado -->
        <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">

            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                <div>
                    <span class="mb-3 inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider">
                        Gestión de actividades
                    </span>

                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Actividades de {{ $class->name }}
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50 sm:text-base">
                        Crea, consulta, edita y configura las actividades
                        educativas de tus estudiantes.
                    </p>
                </div>

                <div class="hidden h-28 w-28 items-center justify-center rounded-3xl bg-white/15 text-7xl md:flex">
                    📚
                </div>

            </div>

        </section>


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


        <!-- Filtros -->
        <section class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                        Buscar actividades
                    </p>

                    <h2 class="mt-1 text-xl font-black text-slate-900">
                        Filtra tus actividades
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Encuentra rápidamente una actividad por nombre o tipo.
                    </p>
                </div>

                <div class="hidden rounded-2xl bg-emerald-50 px-4 py-3 text-2xl sm:block">
                    🔎
                </div>

            </div>


            <form method="GET"
                  action="{{ route('teacher.activities.index', $class->id) }}"
                  class="grid gap-5 md:grid-cols-3">

                <!-- Buscar -->
                <div class="md:col-span-1">

                    <label for="search"
                           class="mb-2 block text-sm font-bold text-slate-700">
                        Buscar por nombre
                    </label>

                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Nombre de la actividad"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >

                </div>


                <!-- Tipo -->
                <div>

                    <label for="type"
                           class="mb-2 block text-sm font-bold text-slate-700">
                        Tipo de actividad
                    </label>

                    <select
                        name="type"
                        id="type"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >

                        <option value="">
                            Todas las actividades
                        </option>

                        <option value="kahoot"
                            {{ request('type') === 'kahoot' ? 'selected' : '' }}>
                            Kahoot
                        </option>

                        <option value="crossword"
                            {{ request('type') === 'crossword' ? 'selected' : '' }}>
                            Crucigrama
                        </option>

                        <option value="word_search"
                            {{ request('type') === 'word_search' ? 'selected' : '' }}>
                            Sopa de letras
                        </option>

                        <option value="matching"
                            {{ request('type') === 'matching' ? 'selected' : '' }}>
                            Unir conceptos
                        </option>

                    </select>

                </div>


                <!-- Botones -->
                <div class="flex flex-col justify-end gap-3 sm:flex-row md:flex-col lg:flex-row">

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-emerald-200"
                    >
                        <span>🔎</span>
                        Buscar
                    </button>

                    @if (request()->filled('search') || request()->filled('type'))

                        <a
                            href="{{ route('teacher.activities.index', $class->id) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50"
                        >
                            <span>↺</span>
                            Limpiar
                        </a>

                    @endif

                </div>

            </form>

        </section>


        <!-- Encabezado de listado -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                    Biblioteca de actividades
                </p>

                <h2 class="mt-1 text-2xl font-black text-slate-900">
                    Tus actividades
                </h2>
            </div>

            <a
                href="{{ route('teacher.activities.create', $class->id) }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700 hover:shadow-xl"
            >
                <span class="text-lg">+</span>
                Crear actividad
            </a>

        </div>


        <!-- Listado -->
        @if ($activities->isEmpty())

            <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">

                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 text-5xl">
                    📝
                </div>

                <h2 class="mt-5 text-2xl font-black text-slate-900">
                    No hay actividades creadas
                </h2>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    Todavía no tienes actividades en esta clase.
                    Crea la primera para comenzar.
                </p>

                <a
                    href="{{ route('teacher.activities.create', $class->id) }}"
                    class="mt-6 inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700"
                >
                    <span>+</span>
                    Crear primera actividad
                </a>

            </section>

        @else

            <div class="grid gap-6 lg:grid-cols-2">

                @foreach ($activities as $activity)

                    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60 transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                        <!-- Cabecera de tarjeta -->
                        <div class="border-b border-slate-100 bg-slate-50 px-6 py-5">

                            <div class="flex items-start justify-between gap-4">

                                <div class="min-w-0">

                                    <p class="mb-2 text-xs font-black uppercase tracking-widest text-emerald-600">
                                        Actividad educativa
                                    </p>

                                    <h3 class="break-words text-xl font-black text-slate-900">
                                        {{ $activity->title }}
                                    </h3>

                                </div>

                                @if ($activity->status === 'draft')

                                    <span class="shrink-0 rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700">
                                        Borrador
                                    </span>

                                @else

                                    <span class="shrink-0 rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">
                                        {{ $activity->status }}
                                    </span>

                                @endif

                            </div>

                        </div>


                        <!-- Información -->
                        <div class="space-y-4 px-6 py-6">

                            <div class="grid gap-3 sm:grid-cols-2">

                                <div class="rounded-2xl bg-emerald-50 p-4">
                                    <p class="text-xs font-bold text-emerald-700">
                                        Tipo
                                    </p>

                                    <p class="mt-1 text-sm font-black capitalize text-slate-900">
                                        {{ $activity->type }}
                                    </p>
                                </div>


                                <div class="rounded-2xl bg-cyan-50 p-4">
                                    <p class="text-xs font-bold text-cyan-700">
                                        Modo
                                    </p>

                                    <p class="mt-1 text-sm font-black capitalize text-slate-900">
                                        {{ $activity->mode }}
                                    </p>
                                </div>


                                <div class="rounded-2xl bg-teal-50 p-4">
                                    <p class="text-xs font-bold text-teal-700">
                                        Puntuación máxima
                                    </p>

                                    <p class="mt-1 text-sm font-black text-slate-900">
                                        {{ $activity->max_score }} puntos
                                    </p>
                                </div>


                                <div class="rounded-2xl bg-slate-100 p-4">
                                    <p class="text-xs font-bold text-slate-500">
                                        Tiempo límite
                                    </p>

                                    <p class="mt-1 text-sm font-black text-slate-900">
                                        {{ $activity->time_limit }} segundos
                                    </p>
                                </div>

                            </div>


                            @if ($activity->due_at)

                                <div class="flex items-start gap-3 rounded-2xl border border-orange-100 bg-orange-50 p-4">

                                    <span class="text-xl">📅</span>

                                    <div>
                                        <p class="text-xs font-bold text-orange-700">
                                            Fecha límite
                                        </p>

                                        <p class="mt-1 text-sm font-black text-slate-800">
                                            {{ $activity->due_at }}
                                        </p>
                                    </div>

                                </div>

                            @endif


                            <!-- Acción principal -->
                            <a
                                href="{{ route('teacher.activities.show', $activity->id) }}"
                                class="flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-black text-white transition hover:bg-slate-800"
                            >
                                Ver actividad
                                <span>→</span>
                            </a>


                            <!-- Acciones de borrador -->
                            @if ($activity->status === 'draft')

                                <div class="border-t border-slate-100 pt-4">

                                    <p class="mb-3 text-xs font-black uppercase tracking-widest text-slate-400">
                                        Opciones de configuración
                                    </p>

                                    <div class="grid gap-3 sm:grid-cols-2">

                                        <a
                                            href="{{ route('teacher.activities.edit', $activity->id) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-700 transition hover:bg-emerald-100"
                                        >
                                            ✏️ Editar actividad
                                        </a>


                                        {{-- Configuración del crucigrama --}}
                                        @if ($activity->type === 'crossword')

                                            @if ($activity->crossword && $activity->crossword->words()->exists())

                                                <a
                                                    href="{{ route('teacher.crossword.edit', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm font-black text-cyan-700 transition hover:bg-cyan-100"
                                                >
                                                    🧩 Editar crucigrama
                                                </a>

                                            @else

                                                <a
                                                    href="{{ route('teacher.crossword.configure', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm font-black text-cyan-700 transition hover:bg-cyan-100"
                                                >
                                                    🧩 Configurar crucigrama
                                                </a>

                                            @endif

                                        @endif


                                        {{-- Configuración del Kahoot --}}
                                        @if ($activity->type === 'kahoot')

                                            @if ($activity->kahoot && $activity->kahoot->questions()->exists())

                                                <a
                                                    href="{{ route('teacher.kahoot.edit', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm font-black text-violet-700 transition hover:bg-violet-100"
                                                >
                                                    🏆 Editar Kahoot
                                                </a>

                                            @else

                                                <a
                                                    href="{{ route('teacher.kahoot.configure', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm font-black text-violet-700 transition hover:bg-violet-100"
                                                >
                                                    🏆 Configurar Kahoot
                                                </a>

                                            @endif

                                        @endif


                                        {{-- Configuración de unir palabras --}}
                                        @if ($activity->type === 'matching')

                                            @if ($activity->matching && $activity->matching->items()->exists())

                                                <a
                                                    href="{{ route('teacher.matching.edit', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-black text-orange-700 transition hover:bg-orange-100"
                                                >
                                                    🔗 Editar unir palabras
                                                </a>

                                            @else

                                                <a
                                                    href="{{ route('teacher.matching.configure', $activity->id) }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded-2xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm font-black text-orange-700 transition hover:bg-orange-100"
                                                >
                                                    🔗 Configurar unir palabras
                                                </a>

                                            @endif

                                        @endif

                                    </div>

                                </div>

                            @endif

                        </div>

                    </article>

                @endforeach

            </div>

        @endif


        <!-- Regresar a la clase -->
        <div class="mt-8 text-center">

            <a
                href="{{ route('teacher.classes.show', $class->id) }}"
                class="inline-flex items-center gap-2 text-sm font-black text-emerald-700 transition hover:text-emerald-900"
            >
                <span>←</span>
                Volver a la clase
            </a>

        </div>

    </main>

</body>

</html>