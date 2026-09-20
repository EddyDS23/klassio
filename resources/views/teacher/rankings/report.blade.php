<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reporte - {{ $activity->title }} | Klassio</title>

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

            <a
                href="{{ route('teacher.activities.show', $activity->id) }}"
                class="flex items-center gap-3"
            >
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
                Reporte de actividad
            </div>
        @if ($activity->time_limit)
            <p>
                <strong>Tiempo límite:</strong>
                {{ $activity->time_limit }} segundos
            </p>
        @endif
    </section>

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
                        <span>📊</span>
                        Análisis académico
                    </div>

                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Reporte de actividad
                    </h1>
            <tr>
                <th>
                    {{ $activity->mode === 'team' ? 'Equipos' : 'Estudiantes inscritos' }}
                </th>

                <td>
                    {{ $activity->mode === 'team' ? $summary['total_teams'] : $summary['total_students'] }}
                </td>
            </tr>

            <tr>
                <th>
                    {{ $activity->mode === 'team' ? 'Equipos que participaron' : 'Estudiantes que participaron' }}
                </th>

                <td>{{ $summary['participated'] }}</td>
            </tr>

            <tr>
                <th>
                    {{ $activity->mode === 'team' ? 'Equipos que no participaron' : 'Estudiantes que no participaron' }}
                </th>

                <td>{{ $summary['not_participated'] }}</td>
            </tr>

                    <p class="mt-3 text-sm leading-6 text-emerald-50 sm:text-base">
                        Revisa la participación, el estado de las entregas y el rendimiento
                        de tus estudiantes.
                    </p>
                </div>

                <div class="rounded-3xl bg-white/10 p-5 text-center ring-1 ring-white/20 backdrop-blur">
                    <div class="text-5xl">📈</div>

                    <p class="mt-2 text-sm font-black text-white">
                        Resumen académico
                    </p>

                    <p class="mt-1 text-xs text-emerald-100">
                        Datos de la actividad
                    </p>
                </div>

            </div>
        </section>
        <h2>
            Estado de las
            {{ $activity->mode === 'team' ? 'participaciones por equipo' : 'participaciones' }}
        </h2>

        <!-- Información de la actividad -->
        <section class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

            <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400">
                        Actividad analizada
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
                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                        Tipo de actividad
                    </p>

                    <p class="mt-2 font-black capitalize text-slate-800">
                        {{ ucfirst(str_replace('_', ' ', $activity->type)) }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                        Modo
                    </p>

                    <p class="mt-2 font-black text-slate-800">
                        {{ $activity->mode === 'team' ? 'Equipos' : 'Individual' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
                        Puntuación máxima
                    </p>

                    <p class="mt-2 font-black text-slate-800">
                        {{ $activity->max_score }} puntos
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-400">
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
                <td>
                    @if ($summary['average_score'] !== null)
                        {{ $summary['average_score'] }}
                        / {{ $activity->max_score }}
                    @else
                        -
                    @endif
                </td>
            </tr>

            </div>
        </section>

        <!-- Participación -->
        <section class="mb-8">
                <td>
                    @if ($summary['best_score'] !== null)
                        {{ $summary['best_score'] }}
                        / {{ $activity->max_score }}
                    @else
                        -
                    @endif
                </td>
            </tr>

            <div class="mb-4">
                <h2 class="text-2xl font-black text-slate-900">
                    Participación
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Resumen de estudiantes inscritos y participantes.
                </p>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-2xl">
                            👥
                        </div>

                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-black text-indigo-700">
                            Inscritos
                        </span>
                    </div>

                    <p class="mt-5 text-sm font-bold text-slate-500">
                        Estudiantes inscritos
                    </p>
                <td>
                    @if ($summary['average_time'] !== null)
                        {{ floor($summary['average_time'] / 60) }}:{{ str_pad($summary['average_time'] % 60, 2, '0', STR_PAD_LEFT) }}
                    @else
                        -
                    @endif
                </td>
            </tr>

                 
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-2xl">
                            ✓
                        </div>

                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">
                            Participaron
                        </span>
                    </div>

                    <p class="mt-5 text-sm font-bold text-slate-500">
                        Estudiantes que participaron
                    </p>

                    <p class="mt-1 text-4xl font-black text-emerald-700">
                        {{ $summary['participated'] }}
                    </p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-2xl">
                            —
                        </div>

                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-black text-rose-700">
                            Pendientes
                        </span>
                    </div>

                    <p class="mt-5 text-sm font-bold text-slate-500">
                        No participaron
                    </p>

                    <p class="mt-1 text-4xl font-black text-rose-700">
                        {{ $summary['not_participated'] }}
                    </p>
                </div>

            </div>
        </section>

        <!-- Estado de participaciones -->
        <section class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-900/5 sm:p-8">

            <div class="mb-6">
                <h2 class="text-2xl font-black text-slate-900">
                    Estado de las participaciones
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Consulta cómo se encuentran las entregas de la actividad.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">✓</span>

                        <span class="rounded-full bg-white/70 px-2.5 py-1 text-xs font-black text-emerald-700">
                            Finalizados
                        </span>
                    </div>

                    <p class="mt-4 text-sm font-bold text-emerald-800">
                        Completados
                    </p>

                    <p class="mt-1 text-3xl font-black text-emerald-700">
                        {{ $summary['completed'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">⏳</span>

                        <span class="rounded-full bg-white/70 px-2.5 py-1 text-xs font-black text-cyan-700">
                            Activos
                        </span>
                    </div>

                    <p class="mt-4 text-sm font-bold text-cyan-800">
                        En progreso
                    </p>

                    <p class="mt-1 text-3xl font-black text-cyan-700">
                        {{ $summary['started'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-amber-100 bg-amber-50 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">↩</span>

                        <span class="rounded-full bg-white/70 px-2.5 py-1 text-xs font-black text-amber-700">
                            Interrumpidos
                        </span>
                    </div>

                    <p class="mt-4 text-sm font-bold text-amber-800">
                        Abandonados
                    </p>

                    <p class="mt-1 text-3xl font-black text-amber-700">
                        {{ $summary['abandoned'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-rose-100 bg-rose-50 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-2xl">⌛</span>

                        <span class="rounded-full bg-white/70 px-2.5 py-1 text-xs font-black text-rose-700">
                            Sin tiempo
                        </span>
                    </div>

                    <p class="mt-4 text-sm font-bold text-rose-800">
                        Tiempo agotado
                    </p>

                    <p class="mt-1 text-3xl font-black text-rose-700">
                        {{ $summary['expired'] }}
                    </p>
                </div>

            </div>
        </section>

        <!-- Rendimiento -->
        <section class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-900/5 sm:p-8">

            <div class="mb-6">
                <h2 class="text-2xl font-black text-slate-900">
                    Rendimiento
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Indicadores generales de desempeño de los participantes.
                </p>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">

                <!-- Promedio -->
                <div class="rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-teal-50 p-5">

                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm">
                            📊
                        </div>

                        <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-black text-emerald-700">
                            Promedio
                        </span>
                    </div>

                    <p class="mt-5 text-sm font-bold text-emerald-900">
                        Promedio de puntuación
                    </p>

                    <p class="mt-2 text-4xl font-black text-emerald-700">
                        @if ($summary['average_score'] !== null)
                            {{ $summary['average_score'] }}
                            <span class="text-base font-bold text-emerald-500">
                                / {{ $activity->max_score }}
                            </span>
                        @else
                            -
                        @endif
                    </p>

                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-white/80">
                        @if ($summary['average_score'] !== null && $activity->max_score > 0)
                            <div
                                class="h-full rounded-full bg-emerald-500"
                                style="width: {{ min(100, ($summary['average_score'] / $activity->max_score) * 100) }}%;"
                            ></div>
                        @endif
                    </div>
                </div>

                <!-- Mejor puntuación -->
                <div class="rounded-3xl border border-amber-100 bg-gradient-to-br from-amber-50 to-yellow-50 p-5">

                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm">
                            🏆
                        </div>

                        <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-black text-amber-700">
                            Mejor resultado
                        </span>
                    </div>

                    <p class="mt-5 text-sm font-bold text-amber-900">
                        Mejor puntuación
                    </p>

                    <p class="mt-2 text-4xl font-black text-amber-700">
                        @if ($summary['best_score'] !== null)
                            {{ $summary['best_score'] }}
                            <span class="text-base font-bold text-amber-500">
                                / {{ $activity->max_score }}
                            </span>
                        @else
                            -
                        @endif
                    </p>

                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-white/80">
                        @if ($summary['best_score'] !== null && $activity->max_score > 0)
                            <div
                                class="h-full rounded-full bg-amber-500"
                                style="width: {{ min(100, ($summary['best_score'] / $activity->max_score) * 100) }}%;"
                            ></div>
                        @endif
                    </div>
                </div>

                <!-- Tiempo promedio -->
                <div class="rounded-3xl border border-cyan-100 bg-gradient-to-br from-cyan-50 to-sky-50 p-5">

                    <div class="flex items-center justify-between">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm">
                            ⏱️
                        </div>

                        <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-black text-cyan-700">
                            Duración
                        </span>
                    </div>

                    <p class="mt-5 text-sm font-bold text-cyan-900">
                        Tiempo promedio
                    </p>

                    <p class="mt-2 text-4xl font-black text-cyan-700">
                        @if ($summary['average_time'] !== null)
                            {{ floor($summary['average_time'] / 60) }}:{{
                                str_pad(
                                    $summary['average_time'] % 60,
                                    2,
                                    '0',
                                    STR_PAD_LEFT
                                )
                            }}
                        @else
                            -
                        @endif
                    </p>

                    <p class="mt-3 text-xs font-semibold text-cyan-700">
                        Minutos y segundos promedio
                    </p>
                </div>

            </div>
        </section>

        <!-- Navegación -->
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">

            <div class="flex flex-col gap-3 sm:flex-row">

                <a
                    href="{{ route('teacher.activities.results', $activity->id) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-teal-200 bg-teal-50 px-5 py-3 text-sm font-black text-teal-700 transition hover:bg-teal-100"
                >
                    Ver resultados de estudiantes
                </a>

                <a
                    href="{{ route('teacher.activities.ranking', $activity->id) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-600/20 transition hover:from-emerald-700 hover:to-teal-700"
                >
                    Ver ranking →
                </a>

            </div>

        </div>

    </main>

</body>

</html>
