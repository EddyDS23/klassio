@extends('teacher.layouts.app')

@section('title', 'Resultados — ' . $activity->title)

@section('page-title', 'Resultados')

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
                        Evaluación de estudiantes
                    </p>

                    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        Resultados
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
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

        {{-- Resultados --}}
        <section class="mb-8">

            <h3 class="mb-3 flex flex-wrap items-center gap-2 text-lg font-bold text-slate-900">
                Resultados por alumno
                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                    {{ $results->count() }} {{ $results->count() === 1 ? 'registro' : 'registros' }}
                </span>
            </h3>

            @if ($results->isEmpty())

                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                        <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </div>

                    <h4 class="mt-4 text-base font-bold text-slate-900">
                        No hay resultados todavía
                    </h4>

                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
                        No hay alumnos inscritos en esta clase o todavía no existen
                        resultados para mostrar.
                    </p>

                </div>

            @else

                {{-- Tarjetas (móvil) --}}
                <div class="grid gap-3 sm:hidden">

                    @foreach ($results as $result)
                        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">
                                    {{ strtoupper(substr($result['student']->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-900">
                                        {{ $result['student']->name }}
                                    </p>

                                    @switch($result['status'])
                                        @case('completed')
                                            <span class="text-xs font-bold text-emerald-700">Completado</span>
                                        @break
                                        @case('abandoned')
                                            <span class="text-xs font-bold text-slate-500">Abandonado</span>
                                        @break
                                        @case('expired')
                                            <span class="text-xs font-bold text-red-600">Expirado</span>
                                        @break
                                        @case('started')
                                            <span class="text-xs font-bold text-amber-600">En progreso</span>
                                        @break
                                        @default
                                            <span class="text-xs font-bold text-slate-500">Sin realizar</span>
                                    @endswitch
                                </div>

                            </div>

                            <div class="mt-3 grid grid-cols-3 gap-2">

                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Intento</p>
                                    <p class="mt-0.5 text-xs font-bold text-slate-700">{{ $result['attempt'] ?? '—' }}</p>
                                </div>

                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Puntos</p>
                                    <p class="mt-0.5 text-xs font-bold text-emerald-700">
                                        {{ $result['score'] !== null ? $result['score'].' / '.$activity->max_score : '—' }}
                                    </p>
                                </div>

                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tiempo</p>
                                    <p class="mt-0.5 text-xs font-bold text-slate-700">
                                        {{ $result['elapsed_seconds'] !== null
                                            ? sprintf('%02d:%02d', intdiv($result['elapsed_seconds'], 60), $result['elapsed_seconds'] % 60)
                                            : '—' }}
                                    </p>
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
                                    <th class="whitespace-nowrap px-5 py-3.5 font-bold">Alumno</th>
                                    <th class="whitespace-nowrap px-5 py-3.5 font-bold">Correo</th>
                                    <th class="whitespace-nowrap px-5 py-3.5 font-bold">Estado</th>
                                    <th class="whitespace-nowrap px-5 py-3.5 font-bold">Intento</th>
                                    <th class="whitespace-nowrap px-5 py-3.5 font-bold">Puntuación</th>
                                    <th class="whitespace-nowrap px-5 py-3.5 font-bold">Tiempo</th>
                                </tr>

                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                @foreach ($results as $result)

                                    <tr class="transition hover:bg-emerald-50/40">

                                        <td class="whitespace-nowrap px-5 py-3.5">

                                            <div class="flex items-center gap-3">

                                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">
                                                    {{ strtoupper(substr($result['student']->name, 0, 1)) }}
                                                </div>

                                                <p class="font-bold text-slate-900">
                                                    {{ $result['student']->name }}
                                                </p>

                                            </div>

                                        </td>

                                        <td class="whitespace-nowrap px-5 py-3.5 text-slate-600">
                                            {{ $result['student']->email }}
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-3.5">

                                            @switch($result['status'])

                                                @case('completed')
                                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Completado</span>
                                                @break

                                                @case('abandoned')
                                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Abandonado</span>
                                                @break

                                                @case('expired')
                                                    <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700">Expirado</span>
                                                @break

                                                @case('started')
                                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">En progreso</span>
                                                @break

                                                @default
                                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">Sin realizar</span>

                                            @endswitch

                                        </td>

                                        <td class="whitespace-nowrap px-5 py-3.5 font-bold text-slate-700">
                                            {{ $result['attempt'] ?? '—' }}
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-3.5">
                                            @if ($result['score'] !== null)
                                                <span class="font-bold text-emerald-700">{{ $result['score'] }}</span>
                                                <span class="text-slate-400">/ {{ $activity->max_score }}</span>
                                            @else
                                                <span class="font-bold text-slate-400">—</span>
                                            @endif
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-3.5 font-bold text-slate-700">
                                            @if ($result['elapsed_seconds'] !== null)
                                                <span class="rounded-lg bg-slate-100 px-2.5 py-1">
                                                    {{ sprintf('%02d:%02d', intdiv($result['elapsed_seconds'], 60), $result['elapsed_seconds'] % 60) }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

        </section>

    </div>

@endsection
