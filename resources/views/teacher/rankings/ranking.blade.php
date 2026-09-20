<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ranking - {{ $activity->title }} | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <!-- Fondo decorativo -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -left-24 -top-24 h-80 w-80 rounded-full bg-emerald-200/40 blur-3xl"></div>
        <div class="absolute right-0 top-20 h-96 w-96 rounded-full bg-cyan-200/30 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-teal-200/20 blur-3xl"></div>
    </div>

    <!-- Barra superior -->
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 shadow-sm backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">

            <a href="{{ route('teacher.activities.show', $activity->id) }}"
               class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-xl font-black text-white shadow-lg shadow-emerald-500/20">
                    K
                </div>

                <div>
                    <p class="text-xl font-black tracking-tight text-slate-900">
                        Klassio
                    </p>

                    <p class="text-xs font-medium text-slate-500">
                        Panel del profesor
                    </p>
                </div>
            </a>

            <div class="hidden items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 sm:flex">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Ranking de actividad
            </div>

            <form method="POST" action="{{ url('/logout') }}">
                @csrf

                <button
                    type="submit"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                >
                    Cerrar sesión
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <!-- Volver -->
        <div class="mb-6">
            <a
                href="{{ route('teacher.activities.show', $activity->id) }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50"
            >
                ← Volver a la actividad
            </a>
        </div>

        <!-- Encabezado principal -->
        <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700 p-6 text-white shadow-xl shadow-emerald-900/10 sm:p-8">

            <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">

                <div class="max-w-3xl">
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1.5 text-sm font-bold text-emerald-50 ring-1 ring-white/20">
                        <span>🏆</span>
                        Clasificación de participantes
                    </div>

                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Ranking
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-emerald-50 sm:text-base">
                        Consulta las mejores puntuaciones y el desempeño de los participantes
                        en esta actividad.
                    </p>
                </div>

                <div class="rounded-3xl bg-white/10 p-5 text-center ring-1 ring-white/20 backdrop-blur">
                    <div class="text-5xl">🏅</div>

                    <p class="mt-2 text-sm font-black text-white">
                        Mejores resultados
                    </p>

                    <p class="mt-1 text-xs text-emerald-100">
                        Top 3 participantes
                    </p>
                </div>

            </div>
        </section>

        <!-- Información de actividad -->
        <section class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

            <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400">
                        Actividad evaluada
                    </p>

                    <h2 class="mt-1 text-2xl font-black text-slate-900">
                        {{ $activity->title }}
                    </h2>
                </div>

                <span class="inline-flex w-fit rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-black text-emerald-700">
                    {{ $activity->mode === 'team' ? 'Modo equipos' : 'Modo individual' }}
                </span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Tipo
                    </p>

                    <p class="mt-2 font-black capitalize text-slate-800">
                        {{ ucfirst(str_replace('_', ' ', $activity->type)) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Modo
                    </p>

                    <p class="mt-2 font-black text-slate-800">
                        {{ $activity->mode === 'team' ? 'Equipos' : 'Individual' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Puntuación máxima
                    </p>

                    <p class="mt-2 font-black text-slate-800">
                        {{ $activity->max_score }} puntos
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                        Tiempo límite
                    </p>

                    <p class="mt-2 font-black text-slate-800">
                        @if ($activity->time_limit)
                            {{ $activity->time_limit }} segundos
                        @else
                            Sin límite
                        @endif
                    </p>
                </div>

            </div>
        </section>

        <!-- Top 3 -->
        <section class="mb-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">

            <div class="border-b border-slate-200 bg-gradient-to-r from-amber-50 via-white to-emerald-50 px-5 py-5 sm:px-8">
                <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">

                    <div>
                        <h2 class="text-2xl font-black text-slate-900">
                            🏆 Top 3
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Los participantes con los mejores resultados.
                        </p>
                    </div>

                    <span class="inline-flex w-fit rounded-full bg-amber-100 px-3 py-1.5 text-xs font-black text-amber-700">
                        Mejores posiciones
                    </span>
                </div>
            </div>

            <div class="p-5 sm:p-8">

                @if ($topThree->isEmpty())

                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center">
                        <div class="text-4xl">📊</div>

                        <h3 class="mt-3 font-black text-slate-800">
                            Aún no hay resultados
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            No hay participaciones completadas para esta actividad.
                        </p>
                    </div>

                @else

                    <div class="grid gap-5 md:grid-cols-3">

                        @foreach ($topThree as $entry)

                            <div
                                class="
                                    relative overflow-hidden rounded-3xl border p-5 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-lg
                                    {{ $entry['position'] == 1
                                        ? 'border-amber-200 bg-gradient-to-br from-amber-50 to-yellow-100'
                                        : ($entry['position'] == 2
                                            ? 'border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100'
                                            : 'border-orange-200 bg-gradient-to-br from-orange-50 to-orange-100') }}
                                "
                            >

                                <div class="absolute right-4 top-4 text-2xl">
                                    @if ($entry['position'] == 1)
                                        🥇
                                    @elseif ($entry['position'] == 2)
                                        🥈
                                    @else
                                        🥉
                                    @endif
                                </div>

                                <div
                                    class="
                                        mx-auto flex h-16 w-16 items-center justify-center rounded-full text-2xl font-black shadow-inner
                                        {{ $entry['position'] == 1
                                            ? 'bg-amber-200 text-amber-800'
                                            : ($entry['position'] == 2
                                                ? 'bg-slate-200 text-slate-700'
                                                : 'bg-orange-200 text-orange-800') }}
                                    "
                                >
                                    {{ $entry['position'] }}
                                </div>

                                <p class="mt-4 text-xs font-black uppercase tracking-wider text-slate-500">
                                    Posición {{ $entry['position'] }}
                                </p>

                                <h3 class="mt-2 break-words text-lg font-black text-slate-900">
                                    @if ($activity->mode === 'team')
                                        {{ $entry['team']->name ?? 'Equipo' }}
                                    @else
                                        {{ $entry['student']->name ?? 'Estudiante' }}
                                    @endif
                                </h3>

                                <div class="mt-4 rounded-2xl bg-white/75 p-3">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Puntuación
                                    </p>

                                    <p class="mt-1 text-2xl font-black text-emerald-700">
                                        {{ $entry['score'] }}
                                        <span class="text-sm font-bold text-slate-400">
                                            / {{ $activity->max_score }}
                                        </span>
                                    </p>
                                </div>

                                <div class="mt-4 flex items-center justify-center gap-2 text-sm font-semibold text-slate-600">
                                    <span>⏱️</span>

                                    @if ($entry['elapsed_seconds'] !== null)
                                        {{ floor($entry['elapsed_seconds'] / 60) }}:{{
                                            str_pad(
                                                $entry['elapsed_seconds'] % 60,
                                                2,
                                                '0',
                                                STR_PAD_LEFT
                                            )
                                        }}
                                    @else
                                        -
                                    @endif
                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>
        </section>

        <!-- Clasificación completa -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">

            <div class="border-b border-slate-200 bg-slate-50 px-5 py-5 sm:px-8">
                <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">

                    <div>
                        <h2 class="text-2xl font-black text-slate-900">
                            Clasificación completa
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Lista completa de participantes con participación terminada.
                        </p>
                    </div>

                    <span class="inline-flex w-fit rounded-full bg-teal-100 px-3 py-1.5 text-xs font-black text-teal-700">
                        {{ $ranking->count() }} resultados
                    </span>
                </div>
            </div>

            <div class="p-5 sm:p-8">

                @if ($ranking->isEmpty())

                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center">
                        <div class="text-4xl">📋</div>

                        <h3 class="mt-3 font-black text-slate-800">
                            No hay participaciones completadas
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Los resultados aparecerán aquí cuando los participantes terminen la actividad.
                        </p>
                    </div>

                @else

                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full min-w-[760px] border-collapse">

                            <thead>
                                <tr class="bg-slate-100 text-left text-xs font-black uppercase tracking-wider text-slate-500">
                                    <th class="px-4 py-4">Posición</th>

                                    @if ($activity->mode === 'team')
                                        <th class="px-4 py-4">Equipo</th>
                                    @else
                                        <th class="px-4 py-4">Estudiante</th>
                                        <th class="px-4 py-4">Correo</th>
                                    @endif

                                    <th class="px-4 py-4">Puntuación</th>
                                    <th class="px-4 py-4">Tiempo</th>
                                    <th class="px-4 py-4">Mejor intento</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200">

                                @foreach ($ranking as $entry)

                                    <tr class="transition hover:bg-emerald-50/40">

                                        <td class="px-4 py-4">
                                            <span
                                                class="
                                                    inline-flex h-9 w-9 items-center justify-center rounded-full text-sm font-black
                                                    {{ $entry['position'] == 1
                                                        ? 'bg-amber-100 text-amber-700'
                                                        : ($entry['position'] == 2
                                                            ? 'bg-slate-200 text-slate-700'
                                                            : ($entry['position'] == 3
                                                                ? 'bg-orange-100 text-orange-700'
                                                                : 'bg-emerald-50 text-emerald-700')) }}
                                                "
                                            >
                                                {{ $entry['position'] }}
                                            </span>
                                        </td>

                                        @if ($activity->mode === 'team')

                                            <td class="px-4 py-4 text-sm font-black text-slate-800">
                                                {{ $entry['team']->name ?? 'Equipo' }}
                                            </td>

                                        @else

                                            <td class="px-4 py-4 text-sm font-black text-slate-800">
                                                {{ $entry['student']->name ?? 'Estudiante' }}
                                            </td>

                                            <td class="px-4 py-4 text-sm text-slate-500">
                                                {{ $entry['student']->email ?? '-' }}
                                            </td>

                                        @endif

                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-black text-emerald-700">
                                                {{ $entry['score'] }} / {{ $activity->max_score }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4 text-sm font-semibold text-slate-600">
                                            @if ($entry['elapsed_seconds'] !== null)
                                                <span class="inline-flex items-center gap-1">
                                                    <span>⏱️</span>

                                                    {{ floor($entry['elapsed_seconds'] / 60) }}:{{
                                                        str_pad(
                                                            $entry['elapsed_seconds'] % 60,
                                                            2,
                                                            '0',
                                                            STR_PAD_LEFT
                                                        )
                                                    }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full bg-cyan-100 px-3 py-1.5 text-xs font-black text-cyan-700">
                                                #{{ $entry['attempt'] }}
                                            </span>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>
                    </div>

                @endif

            </div>
        </section>

        <!-- Navegación inferior -->
        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">

            <a
                href="{{ route('teacher.activities.results', $activity->id) }}"
                class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-600/20 transition hover:from-emerald-700 hover:to-teal-700"
            >
                Ver resultados de estudiantes →
            </a>

        </div>

    </main>

</body>
</html>