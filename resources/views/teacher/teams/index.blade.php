@extends('teacher.layouts.app')

@section('title', 'Equipos de la actividad')

@section('page-title', 'Equipos')

@section('content')

    <div class="mx-auto max-w-5xl">

        <div class="mb-6">
            <a href="{{ route('teacher.activities.show', $activity->id) }}"
               class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver al resumen de la actividad
            </a>
        </div>

        @php
            $hasParticipations = $activity->participations()->exists();
            $canEditTeams = $activity->status === 'draft' && !$hasParticipations;
            $assignedCount = $students->filter(fn($student) => $assignedStudentIds->contains($student->id))->count();
            $unassignedStudents = $students->filter(fn($student) => !$assignedStudentIds->contains($student->id));
            $unassignedCount = $unassignedStudents->count();
            $emptyTeamCount = $teams->filter(fn($team) => $team->members->isEmpty())->count();
            $teamsReadyForConfiguration = $teams->isNotEmpty() && $unassignedCount === 0;

            $gameConfig = match ($activity->type) {
                'crossword' => [
                    'configured' => $activity->crossword?->words()->exists() ?? false,
                    'configure' => 'teacher.crossword.configure',
                    'edit' => 'teacher.crossword.edit',
                ],
                'kahoot' => [
                    'configured' => $activity->kahoot?->questions()->exists() ?? false,
                    'configure' => 'teacher.kahoot.configure',
                    'edit' => 'teacher.kahoot.edit',
                ],
                'wordsearch', 'word_search' => [
                    'configured' => $activity->wordsearch?->words()->exists() ?? false,
                    'configure' => 'teacher.wordsearch.configure',
                    'edit' => 'teacher.wordsearch.edit',
                ],
                'matching' => [
                    'configured' => $activity->matching?->items()->exists() ?? false,
                    'configure' => 'teacher.matching.configure',
                    'edit' => 'teacher.matching.edit',
                ],
                'roulette' => [
                    'configured' => $activity->roulette?->items()->exists() ?? false,
                    'configure' => 'teacher.roulette.configure',
                    'edit' => 'teacher.roulette.edit',
                ],
                default => null,
            };

            $nextGameRoute = $canEditTeams && $gameConfig
                ? route($gameConfig['configured'] ? $gameConfig['edit'] : $gameConfig['configure'], $activity->id)
                : route('teacher.activities.show', $activity->id);
        @endphp

        {{-- Encabezado --}}
        <section class="mb-6 rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">
                        Preparación de actividad
                    </p>

                    <h2 class="mt-1 break-words text-2xl font-bold tracking-tight sm:text-3xl">
                        Organiza los equipos
                    </h2>

                    <p class="mt-2 flex items-center gap-1.5 text-sm font-bold text-emerald-100">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.071 5.071 0 0 0-.02-.845 7.002 7.002 0 0 0-2.006-3.326 5.732 5.732 0 0 0-1.664-1.092 5.85 5.85 0 0 0-2.31-.447 5.85 5.85 0 0 0-2.31.447 5.732 5.732 0 0 0-1.664 1.092 7.002 7.002 0 0 0-2.006 3.326 5.071 5.071 0 0 0-.02.845m12 0a5.078 5.078 0 0 1-3.75.002m-4.5-.002a5.078 5.078 0 0 1-3.75.002M12 10.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Zm0 0v.375" />
                        </svg>
                        {{ $activity->title }}
                    </p>
                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 ring-1 ring-emerald-500 md:flex">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.071 5.071 0 0 0-.02-.845 7.002 7.002 0 0 0-2.006-3.326 5.732 5.732 0 0 0-1.664-1.092 5.85 5.85 0 0 0-2.31-.447 5.85 5.85 0 0 0-2.31.447 5.732 5.732 0 0 0-1.664 1.092 7.002 7.002 0 0 0-2.006 3.326 5.071 5.071 0 0 0-.02.845m12 0a5.078 5.078 0 0 1-3.75.002m-4.5-.002a5.078 5.078 0 0 1-3.75.002M12 10.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Zm0 0v.375" />
                    </svg>
                </div>

            </div>

            @if ($canEditTeams)
                {{-- Pasos de preparación --}}
                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-emerald-600 pt-4" aria-label="Pasos para preparar la actividad">

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white ring-1 ring-emerald-500">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Actividad creada
                    </span>

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-bold text-emerald-700" aria-current="step">
                        2 · Organizar equipos
                    </span>

                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $teamsReadyForConfiguration ? 'bg-white/20 text-white ring-1 ring-white/30' : 'bg-emerald-800/60 text-emerald-200' }}">
                        3 · Configurar juego
                    </span>

                </div>
            @endif

        </section>

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm sm:p-5">
                <div class="mb-3 flex items-center gap-2">
                    <svg class="h-5 w-5 shrink-0 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <h3 class="font-bold">Revisa los siguientes errores</h3>
                </div>
                <ul class="list-inside list-disc space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Resumen --}}
        <section class="mb-6">

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">

                    <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Equipos</p>
                        <p class="mt-1 text-lg font-bold text-slate-900">{{ $teams->count() }}</p>
                    </div>

                    <div class="rounded-xl px-3 py-2.5 ring-1 {{ $emptyTeamCount > 0 ? 'bg-amber-50 ring-amber-200' : 'bg-slate-50 ring-slate-200' }}">
                        <p class="text-[11px] font-bold uppercase tracking-wide {{ $emptyTeamCount > 0 ? 'text-amber-600' : 'text-slate-400' }}">Sin integrantes</p>
                        <p class="mt-1 text-lg font-bold {{ $emptyTeamCount > 0 ? 'text-amber-700' : 'text-slate-900' }}">{{ $emptyTeamCount }}</p>
                    </div>

                    <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Asignados</p>
                        <p class="mt-1 text-lg font-bold text-slate-900">{{ $assignedCount }} <span class="text-sm font-bold text-slate-400">/ {{ $students->count() }}</span></p>
                    </div>

                    <div class="rounded-xl px-3 py-2.5 ring-1 {{ $unassignedCount > 0 ? 'bg-amber-50 ring-amber-200' : 'bg-emerald-50 ring-emerald-200' }}">
                        <p class="text-[11px] font-bold uppercase tracking-wide {{ $unassignedCount > 0 ? 'text-amber-600' : 'text-emerald-600' }}">Pendientes</p>
                        <p class="mt-1 text-lg font-bold {{ $unassignedCount > 0 ? 'text-amber-700' : 'text-emerald-700' }}">{{ $unassignedCount }}</p>
                    </div>

                </div>

                {{-- Estado general (único aviso) --}}
                @if ($unassignedCount > 0)
                    <div class="mt-3 flex items-center gap-2 rounded-xl bg-amber-50 p-3 text-amber-800 ring-1 ring-amber-200">
                        <svg class="h-5 w-5 shrink-0 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-sm font-semibold">
                            Hay {{ $unassignedCount }} {{ $unassignedCount === 1 ? 'estudiante sin equipo' : 'estudiantes sin equipo' }}. Asígnalos antes de continuar si deben participar.
                        </p>
                    </div>
                @elseif ($teams->isEmpty())
                    <div class="mt-3 flex items-center gap-2 rounded-xl bg-amber-50 p-3 text-amber-800 ring-1 ring-amber-200">
                        <svg class="h-5 w-5 shrink-0 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-sm font-semibold">Aún no hay equipos creados. Usa la distribución automática o crea uno por uno.</p>
                    </div>
                @elseif ($emptyTeamCount > 0)
                    <div class="mt-3 flex items-center gap-2 rounded-xl bg-amber-50 p-3 text-amber-800 ring-1 ring-amber-200">
                        <svg class="h-5 w-5 shrink-0 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-sm font-semibold">Hay {{ $emptyTeamCount }} {{ $emptyTeamCount === 1 ? 'equipo sin integrantes' : 'equipos sin integrantes' }}. Agrégales estudiantes o elimínalos.</p>
                    </div>
                @else
                    <div class="mt-3 flex items-center gap-2 rounded-xl bg-emerald-50 p-3 text-emerald-800 ring-1 ring-emerald-200">
                        <svg class="h-5 w-5 shrink-0 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <p class="text-sm font-semibold">Todos los estudiantes activos están asignados a un equipo.</p>
                    </div>
                @endif

                @if (! $canEditTeams)
                    <div class="mt-3 flex items-center gap-2 rounded-xl bg-slate-50 p-3 text-slate-600 ring-1 ring-slate-200">
                        <svg class="h-5 w-5 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <p class="text-sm font-semibold">La composición ya no se puede modificar porque la actividad no está en borrador o ya tiene participaciones.</p>
                    </div>
                @endif

            </div>

        </section>

        {{-- Crear equipos --}}
        @if ($canEditTeams)
            <section id="crear-equipos" class="mb-6">

                <h3 class="mb-3 text-lg font-bold text-slate-900">
                    Crear equipos
                </h3>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                    {{-- Acción principal: distribución automática --}}
                    <form method="POST" action="{{ route('teacher.teams.randomize', $activity->id) }}">
                        @csrf

                        <div class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">

                            <div>
                                <label for="team-count" class="mb-1.5 block text-sm font-bold text-slate-700">
                                    Distribuir automáticamente
                                </label>

                                <input type="number"
                                       name="team_count"
                                       id="team-count"
                                       min="1"
                                       max="{{ $students->count() }}"
                                       placeholder="Cantidad de equipos, ej. 4"
                                       required
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            </div>

                            <button type="button"
                                    data-confirm-toggle
                                    aria-expanded="false"
                                    aria-controls="randomize-confirm"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
                                <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 0 13.803 3.7l3.181-3.182m0-4.991v4.99" />
                                </svg>
                                Crear equipos equilibrados
                            </button>

                        </div>

                        {{-- Confirmación (dos pasos, inline) --}}
                        <div id="randomize-confirm" class="mt-3 hidden rounded-xl border border-amber-200 bg-amber-50 p-3" data-confirm-panel>
                            <p class="text-sm font-bold text-amber-800">
                                Esto reemplazará la distribución actual de equipos.
                            </p>

                            <div class="mt-3 flex gap-2">
                                <button type="submit"
                                        class="flex-1 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2">
                                    Sí, distribuir
                                </button>

                                <button type="button" data-confirm-cancel
                                        class="rounded-xl border border-amber-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-amber-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2">
                                    Cancelar
                                </button>
                            </div>
                        </div>

                    </form>

                    <div class="mt-4 border-t border-slate-100 pt-4">

                        <form method="POST" action="{{ route('teacher.teams.store', $activity->id) }}"
                              class="grid gap-3 sm:grid-cols-[1fr_auto] sm:items-end">
                            @csrf

                            <div>
                                <label for="new-team-name" class="mb-1.5 block text-sm font-bold text-slate-700">
                                    O crea un equipo manualmente
                                </label>

                                <input type="text"
                                       name="name"
                                       id="new-team-name"
                                       placeholder="Nombre del equipo, ej. Exploradores"
                                       value="{{ old('name') }}"
                                       required
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            </div>

                            <button type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
                                <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Crear equipo
                            </button>

                        </form>

                    </div>

                </div>

            </section>
        @endif

        {{-- Equipos actuales --}}
        <section id="equipos-actuales" class="mb-6">

            <h3 class="mb-3 text-lg font-bold text-slate-900">
                Equipos actuales
                <span class="text-sm font-bold text-slate-400">({{ $teams->count() }})</span>
            </h3>

            @if ($teams->isEmpty())

                <div class="flex items-center gap-2.5 rounded-2xl border border-slate-200 bg-white p-4 text-slate-600 shadow-sm">
                    <svg class="h-5 w-5 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <p class="text-sm font-semibold">
                        No hay equipos todavía.
                        @if ($canEditTeams)
                            Usa “Crear equipos equilibrados” o “Crear equipo” para empezar.
                        @endif
                    </p>
                </div>

            @else

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">

                    @foreach ($teams as $team)
                        <article class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" data-confirm-card>

                            {{-- Cabecera del equipo --}}
                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-4 py-3">

                                <div class="min-w-0">
                                    <h4 class="truncate text-base font-bold text-slate-900">
                                        {{ $team->name }}
                                    </h4>

                                    <p class="text-xs font-bold {{ $team->members->isEmpty() ? 'text-amber-600' : 'text-emerald-700' }}">
                                        {{ $team->members->count() }} {{ $team->members->count() === 1 ? 'integrante' : 'integrantes' }}
                                    </p>
                                </div>

                                @if ($canEditTeams)
                                    <button type="button" data-confirm-toggle
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                                        aria-expanded="false"
                                        aria-controls="delete-team-{{ $team->id }}"
                                        aria-label="Eliminar el equipo {{ $team->name }}">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                @endif

                            </div>

                            {{-- Confirmación de eliminación del equipo --}}
                            @if ($canEditTeams)
                                <div id="delete-team-{{ $team->id }}" class="hidden border-b border-red-100 bg-red-50 p-3" data-confirm-panel>
                                    <p class="text-sm font-bold text-red-800">
                                        ¿Eliminar el equipo <span class="font-bold">{{ $team->name }}</span>?
                                    </p>

                                    <p class="mt-0.5 text-xs text-red-700/80">
                                        También se quitarán sus integrantes.
                                    </p>

                                    <div class="mt-3 flex gap-2">
                                        <form method="POST"
                                              action="{{ route('teacher.teams.destroy', ['id' => $activity->id, 'teamId' => $team->id]) }}"
                                              class="flex-1">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-3 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                                Sí, eliminar
                                            </button>
                                        </form>

                                        <button type="button" data-confirm-cancel
                                                class="rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Integrantes --}}
                            <div class="flex-1 p-4">

                                @if ($team->members->isEmpty())
                                    <p class="rounded-xl bg-slate-50 p-3 text-sm font-semibold text-slate-500 ring-1 ring-slate-200">
                                        Este equipo no tiene alumnos.
                                        @if ($canEditTeams)
                                            Agrega el primero con el formulario de abajo.
                                        @endif
                                    </p>
                                @else
                                    <ul class="divide-y divide-slate-100">
                                        @foreach ($team->members as $member)
                                            <li class="py-2 first:pt-0">

                                                <div class="flex items-center gap-2.5">

                                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-sm font-bold text-emerald-700 ring-1 ring-emerald-100">
                                                        {{ strtoupper(substr($member->student->name, 0, 1)) }}
                                                    </div>

                                                    <div class="min-w-0 flex-1">
                                                        <p class="truncate text-sm font-bold text-slate-800">
                                                            {{ $member->student->name }}
                                                        </p>

                                                        @if ($member->student->email)
                                                            <p class="hidden truncate text-xs text-slate-400 sm:block">
                                                                {{ $member->student->email }}
                                                            </p>
                                                        @endif
                                                    </div>

                                                    @if ($canEditTeams)
                                                        <button type="button" data-confirm-toggle
                                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                                                            aria-expanded="false"
                                                            aria-controls="remove-member-{{ $team->id }}-{{ $member->student_id }}"
                                                            aria-label="Quitar a {{ $member->student->name }} del equipo">
                                                            <svg class="h-4.5 w-4.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    @endif

                                                </div>

                                                @if ($canEditTeams)
                                                    {{-- Confirmación de retirada (dos pasos, inline) --}}
                                                    <div id="remove-member-{{ $team->id }}-{{ $member->student_id }}"
                                                         class="mt-2 hidden rounded-xl border border-red-200 bg-red-50 p-3"
                                                         data-confirm-panel>

                                                        <p class="text-sm font-semibold text-red-800">
                                                            ¿Quitar a
                                                            <span class="font-bold">{{ $member->student->name }}</span>
                                                            del equipo?
                                                        </p>

                                                        <div class="mt-2 flex gap-2">
                                                            <form method="POST"
                                                                  action="{{ route('teacher.teams.members.remove', [
                                                                      'id' => $activity->id,
                                                                      'teamId' => $team->id,
                                                                      'studentId' => $member->student_id,
                                                                  ]) }}"
                                                                  class="flex-1">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit"
                                                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-3 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                                                    Sí, quitar
                                                                </button>
                                                            </form>

                                                            <button type="button" data-confirm-cancel
                                                                    class="rounded-xl border border-red-200 bg-white px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2">
                                                                Cancelar
                                                            </button>
                                                        </div>

                                                    </div>
                                                @endif

                                            </li>
                                        @endforeach
                                    </ul>
                                @endif

                            </div>

                            {{-- Agregar integrante --}}
                            @if ($canEditTeams)
                                <form method="POST"
                                      action="{{ route('teacher.teams.members.add', ['id' => $activity->id, 'teamId' => $team->id]) }}"
                                      class="border-t border-slate-100 bg-slate-50/60 p-4">
                                    @csrf

                                    <label for="team-student-{{ $team->id }}" class="mb-1.5 block text-sm font-bold text-slate-700">
                                        Agregar integrante
                                    </label>

                                    <div class="grid gap-2 sm:grid-cols-[1fr_auto]">
                                        <select name="student_id"
                                                id="team-student-{{ $team->id }}"
                                                required
                                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">

                                            <option value="">Seleccionar alumno...</option>

                                            @foreach ($students as $student)
                                                @if (! $assignedStudentIds->contains($student->id))
                                                    <option value="{{ $student->id }}">
                                                        {{ $student->name }}
                                                        @if ($student->email)
                                                            — {{ $student->email }}
                                                        @endif
                                                    </option>
                                                @endif
                                            @endforeach

                                        </select>

                                        <button type="submit"
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
                                            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Agregar
                                        </button>
                                    </div>
                                </form>
                            @endif

                        </article>
                    @endforeach

                </div>

            @endif

        </section>

        {{-- Estudiantes sin equipo --}}
        <section class="mb-6">

            <h3 class="mb-3 text-lg font-bold text-slate-900">
                Estudiantes sin equipo
                <span class="text-sm font-bold {{ $unassignedCount > 0 ? 'text-amber-600' : 'text-slate-400' }}">({{ $unassignedCount }})</span>
            </h3>

            @if ($unassignedStudents->isEmpty())

                <div class="flex items-center gap-2.5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    <svg class="h-5 w-5 shrink-0 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <p class="text-sm font-semibold">Todos los estudiantes activos están incluidos en un equipo.</p>
                </div>

            @else

                <ul class="grid gap-2 sm:grid-cols-2">
                    @foreach ($unassignedStudents as $student)
                        <li class="flex items-center gap-2.5 rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-sm font-bold text-slate-500 ring-1 ring-slate-200">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold text-slate-800">
                                    {{ $student->name }}
                                </p>

                                @if ($student->email)
                                    <p class="hidden truncate text-xs text-slate-400 sm:block">
                                        {{ $student->email }}
                                    </p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

            @endif

        </section>

        {{-- Siguiente paso --}}
        <section class="mb-8">

            @if ($canEditTeams && $gameConfig)
                @if (! $teamsReadyForConfiguration)

                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:p-5">
                        <p class="text-sm font-bold text-amber-800">
                            @if ($teams->isEmpty())
                                Para continuar, crea al menos un equipo y asigna a todos los estudiantes.
                            @else
                                Asigna a los {{ $unassignedCount === 1 ? 'estudiante pendiente' : 'estudiantes pendientes' }} a un equipo antes de configurar el juego.
                            @endif
                        </p>

                        <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                            <a href="{{ $teams->isEmpty() ? '#crear-equipos' : '#equipos-actuales' }}"
                               class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-amber-200 bg-white px-5 py-2.5 text-sm font-bold text-amber-800 transition hover:bg-amber-100 sm:w-auto">
                                {{ $teams->isEmpty() ? 'Crear el primer equipo' : 'Asignar estudiantes' }}
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                </svg>
                            </a>
                        </div>
                    </div>

                @else

                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 sm:p-5">
                        <p class="text-sm font-semibold text-emerald-900">
                            Distribución lista. Continúa con el contenido del juego
                            @include('partials.game-type-label', ['type' => $activity->type]).
                        </p>

                        <a href="{{ $nextGameRoute }}"
                           class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
                            {{ $gameConfig['configured'] ? 'Continuar a la configuración' : 'Configurar el juego' }}
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>

                @endif
            @else

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <p class="text-sm font-semibold text-slate-600">
                        Esta actividad ya no está en preparación. Regresa al resumen para consultar sus acciones disponibles.
                    </p>

                    <a href="{{ route('teacher.activities.show', $activity->id) }}"
                       class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 sm:w-auto">
                        Volver al resumen de actividad
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>

            @endif

        </section>

    </div>

@endsection

@push('scripts')
    @if ($canEditTeams)
        {{-- Confirmaciones en dos pasos (distribuir, eliminar equipo, quitar integrante) --}}
        <script>
        (function () {
            var toggles = Array.prototype.slice.call(document.querySelectorAll('[data-confirm-toggle]'));
            var panels = Array.prototype.slice.call(document.querySelectorAll('[data-confirm-panel]'));

            function closePanel(panel) {
                panel.classList.add('hidden');

                var toggle = document.querySelector('[aria-controls="' + panel.id + '"]');

                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            }

            toggles.forEach(function (toggle) {
                toggle.addEventListener('click', function () {
                    var panel = document.getElementById(toggle.getAttribute('aria-controls'));

                    if (! panel) {
                        return;
                    }

                    var wasOpen = toggle.getAttribute('aria-expanded') === 'true';

                    // Solo una confirmación abierta a la vez.
                    panels.forEach(closePanel);

                    if (! wasOpen) {
                        panel.classList.remove('hidden');
                        toggle.setAttribute('aria-expanded', 'true');
                    }
                });
            });

            panels.forEach(function (panel) {
                var cancel = panel.querySelector('[data-confirm-cancel]');

                if (cancel) {
                    cancel.addEventListener('click', function () {
                        closePanel(panel);
                    });
                }
            });
        })();
    </script>
    @endif
@endpush
