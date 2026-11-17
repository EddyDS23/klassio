@extends('teacher.layouts.app')

@section('title', 'Ranking — ' . $activity->title)

@section('page-title', 'Ranking')

@section('content')

    <div class="mx-auto max-w-5xl">

        <div class="mb-6">
            <a href="{{ route('teacher.activities.show', $activity->id) }}"
               class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver a la actividad
            </a>
        </div>

        {{-- Encabezado --}}
        <section class="mb-6 rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">
                        Clasificación de participantes
                    </p>

                    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        Ranking
                    </h2>

                    <p class="mt-2 flex items-center gap-1.5 text-sm font-bold text-emerald-100">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                        {{ $activity->title }}
                    </p>
                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 ring-1 ring-emerald-500 md:flex">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 0 3-3V15a3 3 0 0 0-3-3h-3m-6 6.75a3 3 0 0 1-3-3V15a3 3 0 0 1 3-3h3m-3-1.125V6.75c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V10.875M4.5 3.75h15c.621 0 1.125.504 1.125 1.125v3c0 .621-.504 1.125-1.125 1.125h-15a1.125 1.125 0 0 1-1.125-1.125v-3c0-.621.504-1.125 1.125-1.125Z" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Datos de la actividad --}}
        <section class="mb-6">

            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">

                <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Tipo</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        @include('partials.game-type-label', ['type' => $activity->type])
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Modo</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ $activity->mode === 'team' ? 'Por equipos' : 'Individual' }}
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Puntuación máx.</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">{{ $activity->max_score }} pts</p>
                </div>

                <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Tiempo límite</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">
                        @if ($activity->time_limit && $activity->time_limit % 60 === 0)
                            {{ $activity->time_limit / 60 }} min
                        @elseif ($activity->time_limit)
                            {{ $activity->time_limit }} seg
                        @else
                            Sin límite
                        @endif
                    </p>
                </div>

            </div>

        </section>

        {{-- Top 3 --}}
        <section class="mb-6">

            <h3 class="mb-3 flex flex-wrap items-center gap-2 text-lg font-bold text-slate-900">
                Top 3
                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                    Mejores posiciones
                </span>
            </h3>

            @if ($topThree->isEmpty())

                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                        <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 0 3-3V15a3 3 0 0 0-3-3h-3m-6 6.75a3 3 0 0 1-3-3V15a3 3 0 0 1 3-3h3m-3-1.125V6.75c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V10.875M4.5 3.75h15c.621 0 1.125.504 1.125 1.125v3c0 .621-.504 1.125-1.125 1.125h-15a1.125 1.125 0 0 1-1.125-1.125v-3c0-.621.504-1.125 1.125-1.125Z" />
                        </svg>
                    </div>

                    <h4 class="mt-4 text-base font-bold text-slate-900">
                        Aún no hay resultados
                    </h4>

                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
                        No hay participaciones completadas para esta actividad todavía.
                    </p>

                </div>

            @else

                <div class="grid gap-3 md:grid-cols-3">

                    @foreach ($topThree as $entry)

                        <article class="rounded-2xl border bg-white p-5 shadow-sm {{ $entry['position'] == 1 ? 'border-amber-300' : ($entry['position'] == 2 ? 'border-slate-300' : 'border-orange-300') }}">

                            <div class="flex items-center gap-3">

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-lg font-bold ring-1 {{ $entry['position'] == 1 ? 'bg-amber-100 text-amber-700 ring-amber-200' : ($entry['position'] == 2 ? 'bg-slate-100 text-slate-700 ring-slate-200' : 'bg-orange-100 text-orange-700 ring-orange-200') }}">
                                    {{ $entry['position'] }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-bold uppercase tracking-wide {{ $entry['position'] == 1 ? 'text-amber-600' : ($entry['position'] == 2 ? 'text-slate-500' : 'text-orange-600') }}">
                                        @if ($entry['position'] == 1)
                                            Primer lugar
                                        @elseif ($entry['position'] == 2)
                                            Segundo lugar
                                        @else
                                            Tercer lugar
                                        @endif
                                    </p>

                                    <p class="truncate text-sm font-bold text-slate-900">
                                        @if ($activity->mode === 'team')
                                            {{ $entry['team']->name ?? 'Equipo' }}
                                        @else
                                            {{ $entry['student']->name ?? 'Estudiante' }}
                                        @endif
                                    </p>
                                </div>

                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2">

                                <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Puntuación</p>
                                    <p class="mt-0.5 text-sm font-bold text-emerald-700">
                                        {{ $entry['score'] }}
                                        <span class="text-xs font-bold text-slate-400">/ {{ $activity->max_score }}</span>
                                    </p>
                                </div>

                                <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tiempo</p>
                                    <p class="mt-0.5 flex items-center gap-1 text-sm font-bold text-slate-700">
                                        <svg class="h-3.5 w-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        @if ($entry['elapsed_seconds'] !== null)
                                            {{ floor($entry['elapsed_seconds'] / 60) }}:{{ str_pad($entry['elapsed_seconds'] % 60, 2, '0', STR_PAD_LEFT) }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>

            @endif

        </section>

        {{-- Clasificación completa --}}
        <section class="mb-8">

            <h3 class="mb-3 flex flex-wrap items-center gap-2 text-lg font-bold text-slate-900">
                Clasificación completa
                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                    {{ $ranking->count() }} {{ $ranking->count() === 1 ? 'resultado' : 'resultados' }}
                </span>
            </h3>

            @if ($ranking->isEmpty())

                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-500 ring-1 ring-slate-200">
                        <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.651-.025-1.305-.035-1.96-.033m0 0h2.96m-2.96 0C6.043 4.805 5.036 5.02 4.447 5.373a2.25 2.25 0 0 0-.947 2.096c0 1.481.325 2.926.905 4.262.077.173.117.359.117.547v4.286a3 3 0 0 0 3 3h.75m4.5-14.836c1.768-.033 3.535.039 5.287.172" />
                        </svg>
                    </div>

                    <h4 class="mt-4 text-base font-bold text-slate-900">
                        No hay participaciones completadas
                    </h4>

                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
                        Los resultados aparecerán aquí cuando los participantes terminen la actividad.
                    </p>

                </div>

            @else

                {{-- Tarjetas (móvil) --}}
                <div class="grid gap-3 sm:hidden">

                    @foreach ($ranking as $entry)
                        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                            <div class="flex items-center gap-3">

                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm font-bold ring-1 {{ $entry['position'] == 1 ? 'bg-amber-100 text-amber-700 ring-amber-200' : ($entry['position'] == 2 ? 'bg-slate-100 text-slate-700 ring-slate-200' : ($entry['position'] == 3 ? 'bg-orange-100 text-orange-700 ring-orange-200' : 'bg-emerald-50 text-emerald-700 ring-emerald-100')) }}">
                                    {{ $entry['position'] }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-900">
                                        @if ($activity->mode === 'team')
                                            {{ $entry['team']->name ?? 'Equipo' }}
                                        @else
                                            {{ $entry['student']->name ?? 'Estudiante' }}
                                        @endif
                                    </p>

                                    @if ($activity->mode !== 'team' && ($entry['student']->email ?? null))
                                        <p class="truncate text-xs text-slate-500">{{ $entry['student']->email }}</p>
                                    @endif
                                </div>

                            </div>

                            <div class="mt-3 grid grid-cols-3 gap-2">

                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Puntos</p>
                                    <p class="mt-0.5 text-xs font-bold text-emerald-700">
                                        {{ $entry['score'] }} / {{ $activity->max_score }}
                                    </p>
                                </div>

                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tiempo</p>
                                    <p class="mt-0.5 text-xs font-bold text-slate-700">
                                        @if ($entry['elapsed_seconds'] !== null)
                                            {{ floor($entry['elapsed_seconds'] / 60) }}:{{ str_pad($entry['elapsed_seconds'] % 60, 2, '0', STR_PAD_LEFT) }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>

                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Intento</p>
                                    <p class="mt-0.5 text-xs font-bold text-slate-700">#{{ $entry['attempt'] }}</p>
                                </div>

                            </div>

                        </article>
                    @endforeach

                </div>

                {{-- Tabla (desktop) --}}
                <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm sm:block">

                    <div class="overflow-x-auto">

                        <table class="min-w-full text-left text-sm">

                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">

                                <tr>
                                    <th class="whitespace-nowrap px-5 py-3.5 font-bold">Posición</th>

                                    @if ($activity->mode === 'team')
                                        <th class="whitespace-nowrap px-5 py-3.5 font-bold">Equipo</th>
                                    @else
                                        <th class="whitespace-nowrap px-5 py-3.5 font-bold">Estudiante</th>
                                        <th class="whitespace-nowrap px-5 py-3.5 font-bold">Correo</th>
                                    @endif

                                    <th class="whitespace-nowrap px-5 py-3.5 font-bold">Puntuación</th>
                                    <th class="whitespace-nowrap px-5 py-3.5 font-bold">Tiempo</th>
                                    <th class="whitespace-nowrap px-5 py-3.5 font-bold">Mejor intento</th>
                                </tr>

                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                @foreach ($ranking as $entry)

                                    <tr class="transition hover:bg-emerald-50/40">

                                        <td class="whitespace-nowrap px-5 py-3.5">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-sm font-bold ring-1 {{ $entry['position'] == 1 ? 'bg-amber-100 text-amber-700 ring-amber-200' : ($entry['position'] == 2 ? 'bg-slate-100 text-slate-700 ring-slate-200' : ($entry['position'] == 3 ? 'bg-orange-100 text-orange-700 ring-orange-200' : 'bg-emerald-50 text-emerald-700 ring-emerald-100')) }}">
                                                {{ $entry['position'] }}
                                            </span>
                                        </td>

                                        @if ($activity->mode === 'team')

                                            <td class="whitespace-nowrap px-5 py-3.5 font-bold text-slate-900">
                                                {{ $entry['team']->name ?? 'Equipo' }}
                                            </td>

                                        @else

                                            <td class="whitespace-nowrap px-5 py-3.5 font-bold text-slate-900">
                                                {{ $entry['student']->name ?? 'Estudiante' }}
                                            </td>

                                            <td class="whitespace-nowrap px-5 py-3.5 text-slate-600">
                                                {{ $entry['student']->email ?? '—' }}
                                            </td>

                                        @endif

                                        <td class="whitespace-nowrap px-5 py-3.5">
                                            <span class="font-bold text-emerald-700">{{ $entry['score'] }}</span>
                                            <span class="text-slate-400">/ {{ $activity->max_score }}</span>
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-3.5 font-bold text-slate-700">
                                            @if ($entry['elapsed_seconds'] !== null)
                                                <span class="rounded-lg bg-slate-100 px-2.5 py-1">
                                                    {{ floor($entry['elapsed_seconds'] / 60) }}:{{ str_pad($entry['elapsed_seconds'] % 60, 2, '0', STR_PAD_LEFT) }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-3.5">
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                                                #{{ $entry['attempt'] }}
                                            </span>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

        </section>

        {{-- Navegación inferior --}}
        <div class="mb-8">
            <a href="{{ route('teacher.activities.results', $activity->id) }}"
               class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
                Ver resultados de estudiantes
            </a>
        </div>

    </div>

@endsection
