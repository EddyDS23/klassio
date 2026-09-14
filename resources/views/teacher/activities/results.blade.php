<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Resultados - {{ $activity->title }} | Klassio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <!-- Barra superior -->
    <header class="border-b border-emerald-100 bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

            <a href="{{ route('teacher.activities.show', $activity->id) }}"
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
                Resultados
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
                        Evaluación de estudiantes
                    </span>

                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Resultados
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50 sm:text-base">
                        Consulta el desempeño de tus estudiantes en esta actividad.
                    </p>
                </div>

                <div class="hidden h-28 w-28 items-center justify-center rounded-3xl bg-white/15 text-7xl md:flex">
                    🏆
                </div>

            </div>

        </section>


        <!-- Información de la actividad -->
        <section class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                        Actividad evaluada
                    </p>

                    <h2 class="mt-1 break-words text-2xl font-black text-slate-900">
                        {{ $activity->title }}
                    </h2>
                </div>

                <span class="w-fit rounded-full bg-emerald-100 px-4 py-2 text-xs font-black capitalize text-emerald-700">
                    {{ $activity->type }}
                </span>

            </div>


            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div class="rounded-2xl bg-emerald-50 p-4">
                    <p class="text-xs font-bold text-emerald-700">
                        Tipo
                    </p>

                    <p class="mt-1 text-base font-black capitalize text-slate-900">
                        {{ $activity->type }}
                    </p>
                </div>


                <div class="rounded-2xl bg-cyan-50 p-4">
                    <p class="text-xs font-bold text-cyan-700">
                        Modo
                    </p>

                    <p class="mt-1 text-base font-black capitalize text-slate-900">
                        {{ $activity->mode }}
                    </p>
                </div>


                <div class="rounded-2xl bg-teal-50 p-4">
                    <p class="text-xs font-bold text-teal-700">
                        Puntuación máxima
                    </p>

                    <p class="mt-1 text-base font-black text-slate-900">
                        {{ $activity->max_score }} puntos
                    </p>
                </div>


                <div class="rounded-2xl bg-slate-100 p-4">
                    <p class="text-xs font-bold text-slate-500">
                        Tiempo límite
                    </p>

                    <p class="mt-1 text-base font-black text-slate-900">
                        @if ($activity->time_limit)
                            {{ $activity->time_limit }} segundos
                        @else
                            Sin límite
                        @endif
                    </p>
                </div>

            </div>

        </section>


        <!-- Resultados de alumnos -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60">

            <div class="border-b border-slate-100 bg-slate-50 px-6 py-5 sm:px-8">

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                            Seguimiento académico
                        </p>

                        <h2 class="mt-1 text-2xl font-black text-slate-900">
                            Alumnos
                        </h2>
                    </div>

                    <span class="w-fit rounded-full bg-white px-4 py-2 text-sm font-bold text-slate-600 shadow-sm">
                        {{ $results->count() }} registros
                    </span>

                </div>

            </div>


            @if ($results->isEmpty())

                <!-- Estado vacío -->
                <div class="px-6 py-14 text-center sm:px-8">

                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 text-5xl">
                        📊
                    </div>

                    <h3 class="mt-5 text-2xl font-black text-slate-900">
                        No hay resultados todavía
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        No hay alumnos inscritos en esta clase o todavía no existen
                        resultados para mostrar.
                    </p>

                </div>

            @else

                <!-- Tabla responsive -->
                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="bg-slate-100 text-xs uppercase tracking-wider text-slate-500">

                            <tr>
                                <th class="whitespace-nowrap px-6 py-4 font-black">
                                    Alumno
                                </th>

                                <th class="whitespace-nowrap px-6 py-4 font-black">
                                    Correo
                                </th>

                                <th class="whitespace-nowrap px-6 py-4 font-black">
                                    Estado
                                </th>

                                <th class="whitespace-nowrap px-6 py-4 font-black">
                                    Intento
                                </th>

                                <th class="whitespace-nowrap px-6 py-4 font-black">
                                    Puntuación
                                </th>

                                <th class="whitespace-nowrap px-6 py-4 font-black">
                                    Tiempo
                                </th>
                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @foreach ($results as $result)

                                <tr class="transition hover:bg-emerald-50/40">

                                    <!-- Alumno -->
                                    <td class="whitespace-nowrap px-6 py-5">

                                        <div class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 font-black text-emerald-700">
                                                {{ strtoupper(substr($result['student']->name, 0, 1)) }}
                                            </div>

                                            <div>
                                                <p class="font-black text-slate-900">
                                                    {{ $result['student']->name }}
                                                </p>

                                                <p class="text-xs text-slate-500">
                                                    Estudiante
                                                </p>
                                            </div>

                                        </div>

                                    </td>


                                    <!-- Correo -->
                                    <td class="whitespace-nowrap px-6 py-5 text-slate-600">
                                        {{ $result['student']->email }}
                                    </td>


                                    <!-- Estado -->
                                    <td class="whitespace-nowrap px-6 py-5">

                                        @switch($result['status'])

                                            @case('completed')

                                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">
                                                    Completado
                                                </span>

                                                @break

                                            @case('abandoned')

                                                <span class="inline-flex rounded-full bg-orange-100 px-3 py-1 text-xs font-black text-orange-700">
                                                    Abandonado
                                                </span>

                                                @break

                                            @case('expired')

                                                <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-black text-red-700">
                                                    Expirado
                                                </span>

                                                @break

                                            @case('started')

                                                <span class="inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-black text-cyan-700">
                                                    En progreso
                                                </span>

                                                @break

                                            @default

                                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">
                                                    Sin realizar
                                                </span>

                                        @endswitch

                                    </td>


                                    <!-- Intento -->
                                    <td class="whitespace-nowrap px-6 py-5 font-bold text-slate-700">
                                        {{ $result['attempt'] ?? '—' }}
                                    </td>


                                    <!-- Puntuación -->
                                    <td class="whitespace-nowrap px-6 py-5">

                                        @if ($result['score'] !== null)

                                            <span class="font-black text-emerald-700">
                                                {{ $result['score'] }}
                                            </span>

                                            <span class="text-slate-400">
                                                / {{ $activity->max_score }}
                                            </span>

                                        @else

                                            <span class="font-bold text-slate-400">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    <!-- Tiempo -->
                                    <td class="whitespace-nowrap px-6 py-5 font-bold text-slate-700">

                                        @if ($result['elapsed_seconds'] !== null)

                                            <span class="rounded-lg bg-slate-100 px-3 py-1">
                                                {{ sprintf(
                                                    '%02d:%02d',
                                                    intdiv($result['elapsed_seconds'], 60),
                                                    $result['elapsed_seconds'] % 60
                                                ) }}
                                            </span>

                                        @else

                                            <span class="text-slate-400">
                                                —
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </section>


        <!-- Navegación -->
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">

            <a
                href="{{ route('teacher.activities.show', $activity->id) }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700 hover:shadow-xl"
            >
                <span>←</span>
                Volver a la actividad
            </a>

        </div>

    </main>

</body>

</html>