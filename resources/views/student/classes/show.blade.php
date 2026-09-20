<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $class->name }} | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside class="hidden w-72 shrink-0 flex-col bg-white shadow-xl lg:sticky lg:top-0 lg:flex lg:h-screen lg:overflow-y-auto">

            <div class="flex items-center gap-3 border-b border-slate-200 px-6 py-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-2xl font-black text-white shadow-lg">
                    K
                </div>

                <div>
                    <h1 class="text-2xl font-black tracking-tight text-indigo-700">
                        Klassio
                    </h1>

                    <p class="text-xs font-medium text-slate-500">
                        Aprende jugando
                    </p>
                </div>
            </div>

            <nav class="flex-1 space-y-2 px-4 py-6">

                <a href="{{ route('student.dashboard') }}"
                   class="flex items-center gap-3 rounded-xl px-4 py-3 font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                    <span class="text-xl">🏠</span>
                    Inicio
                </a>

                <a href="{{ route('student.classes.index') }}"
                   class="flex items-center gap-3 rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white shadow-md">
                    <span class="text-xl">📚</span>
                    Mis clases
                </a>

                <a href="{{ route('student.classes.index') }}"
                   class="flex items-center gap-3 rounded-xl px-4 py-3 font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                    <span class="text-xl">🎮</span>
                    Juegos educativos
                </a>

            </nav>

            <div class="border-t border-slate-200 p-4">

                <div class="mb-4 rounded-2xl bg-indigo-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-500">
                        Estudiante
                    </p>

                    <p class="mt-1 truncate font-bold text-indigo-800">
                        {{ auth()->user()->name }}
                    </p>
                </div>

                <form method="POST" action="{{ url('/logout') }}">
                    @csrf

                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 px-4 py-3 font-semibold text-red-600 transition hover:bg-red-50">
                        <span>↪</span>
                        Cerrar sesión
                    </button>
                </form>

            </div>

        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1">

            <!-- HEADER -->
            <header class="border-b border-slate-200 bg-white px-5 py-5 shadow-sm sm:px-8">

                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">

                    <div>
                        <p class="text-sm font-semibold text-indigo-600">
                            Mi clase
                        </p>

                        <h2 class="mt-1 text-2xl font-black text-slate-900 sm:text-3xl">
                            {{ $class->name }}
                        </h2>
                    </div>

                    <a href="{{ route('student.classes.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">
                        ← Volver
                    </a>

                </div>

            </header>

            <div class="mx-auto max-w-7xl space-y-8 px-5 py-8 sm:px-8">

                <!-- BANNER DE LA CLASE -->
                <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-700 via-indigo-600 to-violet-600 p-6 text-white shadow-xl sm:p-8">

                    <div class="relative z-10 max-w-3xl">

                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-2 text-sm font-bold backdrop-blur">
                            📘 Aula virtual
                        </div>

                        <h1 class="text-3xl font-black sm:text-4xl">
                            {{ $class->name }}
                        </h1>

                        <p class="mt-3 max-w-2xl text-sm leading-6 text-indigo-100 sm:text-base">
                            Consulta la información de tu clase, revisa las actividades
                            y conoce a tus compañeros.
                        </p>

                    </div>

                    <div class="absolute -right-8 -top-10 text-[150px] opacity-20">
                        🎓
                    </div>

                    <div class="absolute -bottom-16 right-24 h-40 w-40 rounded-full bg-white/10"></div>
                    <div class="absolute -right-20 bottom-0 h-56 w-56 rounded-full bg-white/10"></div>

                </section>

                <!-- INFORMACIÓN Y COMPAÑEROS -->
                <section class="grid gap-6 lg:grid-cols-3">

                    <!-- INFORMACIÓN -->
                    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2">

                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-2xl">
                                📋
                            </div>

                            <div>
                                <h2 class="text-xl font-black text-slate-900">
                                    Información de la clase
                                </h2>

                                <p class="text-sm text-slate-500">
                                    Datos generales del aula
                                </p>
                            </div>
                        </div>

                        <div class="space-y-5">

                            <div>
                                <p class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-400">
                                    Descripción
                                </p>

                                @if ($class->description)
                                    <p class="leading-7 text-slate-700">
                                        {{ $class->description }}
                                    </p>
                                @else
                                    <p class="italic text-slate-500">
                                        Sin descripción.
                                    </p>
                                @endif
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">

                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Maestro
                                    </p>

                                    <p class="mt-2 font-bold text-slate-800">
                                        👨‍🏫 {{ $class->teacher->name }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                    <div class="mb-3 flex items-center gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-cyan-100 text-2xl">
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

                        </div>

                    </div>

                    <!-- COMPAÑEROS -->
                    <div class="flex flex-col justify-between rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

                        <div>
                            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-2xl">
                                👥
                            </div>

                            <h2 class="text-xl font-black text-slate-900">
                                Mis compañeros
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Conoce a los estudiantes que forman parte de esta clase.
                            </p>
                        </div>

                        <a href="{{ route('student.class.students', $class->id) }}"
                           class="mt-6 inline-flex items-center justify-center rounded-xl bg-amber-500 px-4 py-3 font-bold text-white transition hover:bg-amber-600">
                            Ver compañeros →
                        </a>

                    </div>

                </section>

                <!-- ACTIVIDAD RECIENTE -->
                <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">

                    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

                        <div class="flex items-center gap-3">

                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-2xl">
                                ✨
                            </div>

                            <div>
                                <h2 class="text-xl font-black text-slate-900 sm:text-2xl">
                                    Actividad reciente
                                </h2>

                                <p class="text-sm text-slate-500">
                                    Revisa la última actividad publicada por tu maestro.
                                </p>
                            </div>

                        </div>

                        <a href="{{ route('student.activities.index', $class->id) }}"
                           class="text-sm font-bold text-indigo-600 transition hover:text-indigo-800">
                            Ver todas →
                        </a>

                    </div>

                    @if ($activity)

                        <article class="rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:p-6">

                            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">

                                <div>
                                    <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-indigo-700">
                                        {{ $activity->type }}
                                    </span>

                                    <h3 class="mt-3 text-xl font-black text-slate-900">
                                        {{ $activity->title }}
                                    </h3>

                                    @if ($activity->description)
                                        <p class="mt-2 leading-6 text-slate-600">
                                            {{ $activity->description }}
                                        </p>
                                    @else
                                        <p class="mt-2 italic text-slate-500">
                                            Sin descripción.
                                        </p>
                                    @endif
                                </div>

                                <a href="{{ route('student.activities.show', $activity->id) }}"
                                   class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-md transition hover:bg-indigo-700">
                                    Ver actividad →
                                </a>

                            </div>

                            <div class="mt-6 grid gap-3 border-t border-slate-200 pt-5 sm:grid-cols-2 lg:grid-cols-4">

                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Modo
                                    </p>

                                    <p class="mt-1 font-bold text-slate-700">
                                        {{ $activity->mode }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Puntuación máxima
                                    </p>

                                    <p class="mt-1 font-bold text-slate-700">
                                        {{ $activity->max_score }} puntos
                                    </p>
                                </div>

                                @if ($activity->time_limit)
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Tiempo límite
                                        </p>

                                        <p class="mt-1 font-bold text-slate-700">
                                            ⏱ {{ $activity->time_limit }} segundos
                                        </p>
                                    </div>
                                @endif

                                @if ($activity->due_at)
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                            Fecha límite
                                        </p>

                                        <p class="mt-1 font-bold text-slate-700">
                                            📅 {{ $activity->due_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                @endif

                            </div>

                        </article>

                    @else

                        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center">

                            <div class="text-5xl">
                                📭
                            </div>

                            <h3 class="mt-4 text-lg font-black text-slate-800">
                                No hay actividades todavía
                            </h3>

                            <p class="mt-2 text-sm text-slate-500">
                                Cuando tu maestro publique una actividad, aparecerá aquí.
                            </p>

                        </div>

                    @endif

                </section>

                <!-- JUEGOS EDUCATIVOS -->
                <section class="rounded-3xl bg-gradient-to-br from-teal-50 to-cyan-50 p-6 ring-1 ring-teal-100 sm:p-8">

                    <div class="mb-6">
                        <span class="text-sm font-black uppercase tracking-widest text-teal-600">
                            Aprende jugando
                        </span>

                        <h2 class="mt-2 text-2xl font-black text-slate-900">
                            Juegos educativos
                        </h2>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                            Muy pronto podrás practicar tus conocimientos con juegos
                            divertidos y actividades interactivas.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                        <div class="rounded-2xl bg-white p-5 shadow-sm">
                            <div class="text-4xl">🔎</div>

                            <h3 class="mt-3 font-black text-slate-800">
                                Sopa de letras
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Encuentra palabras y aprende nuevos conceptos.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white p-5 shadow-sm">
                            <div class="text-4xl">🧩</div>

                            <h3 class="mt-3 font-black text-slate-800">
                                Crucigrama
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Resuelve retos usando tus conocimientos.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white p-5 shadow-sm">
                            <div class="text-4xl">🔵</div>

                            <h3 class="mt-3 font-black text-slate-800">
                                Conecta los puntos
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Une los puntos y descubre nuevas figuras.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-white p-5 shadow-sm">
                            <div class="text-4xl">🏆</div>

                            <h3 class="mt-3 font-black text-slate-800">
                                Quiz
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Pon a prueba tus conocimientos.
                            </p>
                        </div>

                    </div>

                </section>

            </div>

        </main>

    </div>

</body>

</html>