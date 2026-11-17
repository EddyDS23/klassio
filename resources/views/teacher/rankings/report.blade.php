@extends('teacher.layouts.app')

@section('title', 'Reporte — ' . $activity->title)

@section('page-title', 'Reporte')

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
                        Análisis académico
                    </p>

                    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        Reporte de actividad
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

        {{-- Participación --}}
        <section class="mb-6">

            <h3 class="mb-3 text-lg font-bold text-slate-900">Participación</h3>

            <div class="grid gap-3 sm:grid-cols-3">

                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-slate-600 ring-1 ring-slate-200">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                @if ($activity->mode === 'team')
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                @endif
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                {{ $activity->mode === 'team' ? 'Equipos inscritos' : 'Estudiantes inscritos' }}
                            </p>
                            <p class="mt-0.5 text-2xl font-bold text-slate-900">
                                {{ $activity->mode === 'team' ? $summary['total_teams'] : $summary['total_students'] }}
                            </p>
                        </div>

                    </div>

                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                {{ $activity->mode === 'team' ? 'Equipos que participaron' : 'Estudiantes que participaron' }}
                            </p>
                            <p class="mt-0.5 text-2xl font-bold text-emerald-700">{{ $summary['participated'] }}</p>
                        </div>

                    </div>

                </article>

                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-700 ring-1 ring-red-100">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">No participaron</p>
                            <p class="mt-0.5 text-2xl font-bold text-red-700">{{ $summary['not_participated'] }}</p>
                        </div>

                    </div>

                </article>

            </div>

        </section>

        {{-- Estado de las participaciones --}}
        <section class="mb-6">

            <h3 class="mb-3 text-lg font-bold text-slate-900">Estado de las participaciones</h3>

            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">

                <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                    <p class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">
                        <svg class="h-3.5 w-3.5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Completados
                    </p>
                    <p class="mt-1 text-2xl font-bold text-emerald-700">{{ $summary['completed'] }}</p>
                </div>

                <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                    <p class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">
                        <svg class="h-3.5 w-3.5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        En progreso
                    </p>
                    <p class="mt-1 text-2xl font-bold text-amber-600">{{ $summary['started'] }}</p>
                </div>

                <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                    <p class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">
                        <svg class="h-3.5 w-3.5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                        </svg>
                        Abandonados
                    </p>
                    <p class="mt-1 text-2xl font-bold text-slate-600">{{ $summary['abandoned'] }}</p>
                </div>

                <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                    <p class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">
                        <svg class="h-3.5 w-3.5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        Tiempo agotado
                    </p>
                    <p class="mt-1 text-2xl font-bold text-red-700">{{ $summary['expired'] }}</p>
                </div>

            </div>

        </section>

        {{-- Rendimiento --}}
        <section class="mb-8">

            <h3 class="mb-3 text-lg font-bold text-slate-900">Rendimiento</h3>

            <div class="grid gap-3 sm:grid-cols-3">

                {{-- Promedio --}}
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Promedio</p>
                            <p class="mt-0.5 text-2xl font-bold text-emerald-700">
                                @if ($summary['average_score'] !== null)
                                    {{ $summary['average_score'] }}
                                    <span class="text-sm font-bold text-slate-400">/ {{ $activity->max_score }}</span>
                                @else
                                    <span class="text-sm font-bold text-slate-400">Sin datos aún</span>
                                @endif
                            </p>
                        </div>

                    </div>

                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100">
                        @if ($summary['average_score'] !== null && $activity->max_score > 0)
                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ min(100, ($summary['average_score'] / $activity->max_score) * 100) }}%;"></div>
                        @endif
                    </div>

                </article>

                {{-- Mejor puntuación --}}
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-amber-100">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 0 3-3V15a3 3 0 0 0-3-3h-3m-6 6.75a3 3 0 0 1-3-3V15a3 3 0 0 1 3-3h3m-3-1.125V6.75c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V10.875M4.5 3.75h15c.621 0 1.125.504 1.125 1.125v3c0 .621-.504 1.125-1.125 1.125h-15a1.125 1.125 0 0 1-1.125-1.125v-3c0-.621.504-1.125 1.125-1.125Z" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Mejor resultado</p>
                            <p class="mt-0.5 text-2xl font-bold text-amber-600">
                                @if ($summary['best_score'] !== null)
                                    {{ $summary['best_score'] }}
                                    <span class="text-sm font-bold text-slate-400">/ {{ $activity->max_score }}</span>
                                @else
                                    <span class="text-sm font-bold text-slate-400">Sin datos aún</span>
                                @endif
                            </p>
                        </div>

                    </div>

                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100">
                        @if ($summary['best_score'] !== null && $activity->max_score > 0)
                            <div class="h-full rounded-full bg-amber-500" style="width: {{ min(100, ($summary['best_score'] / $activity->max_score) * 100) }}%;"></div>
                        @endif
                    </div>

                </article>

                {{-- Tiempo promedio --}}
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-slate-600 ring-1 ring-slate-200">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Tiempo promedio</p>
                            <p class="mt-0.5 text-2xl font-bold text-slate-900">
                                @if ($summary['average_time'] !== null)
                                    {{ floor($summary['average_time'] / 60) }}:{{ str_pad($summary['average_time'] % 60, 2, '0', STR_PAD_LEFT) }}
                                    <span class="text-sm font-bold text-slate-400">min:seg</span>
                                @else
                                    <span class="text-sm font-bold text-slate-400">Sin datos aún</span>
                                @endif
                            </p>
                        </div>

                    </div>

                </article>

            </div>

        </section>

        {{-- Navegación inferior --}}
        <div class="mb-8 grid gap-3 sm:grid-cols-2">

            <a href="{{ route('teacher.activities.results', $activity->id) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
                Ver resultados de estudiantes
            </a>

            <a href="{{ route('teacher.activities.ranking', $activity->id) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 0 3-3V15a3 3 0 0 0-3-3h-3m-6 6.75a3 3 0 0 1-3-3V15a3 3 0 0 1 3-3h3m-3-1.125V6.75c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V10.875M4.5 3.75h15c.621 0 1.125.504 1.125 1.125v3c0 .621-.504 1.125-1.125 1.125h-15a1.125 1.125 0 0 1-1.125-1.125v-3c0-.621.504-1.125 1.125-1.125Z" />
                </svg>
                Ver ranking
            </a>

        </div>

    </div>

@endsection
