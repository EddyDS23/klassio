@extends('student.layouts.app')

@section('title', 'Actividades')

@section('page-title', 'Actividades')

@section('page-actions')
    <a href="{{ route('student.class.show', $class->id) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver a la clase
    </a>
@endsection

@section('content')

    <div class="mx-auto max-w-7xl">

        {{-- Encabezado --}}
        <section class="rounded-2xl bg-orange-600 p-4 text-white shadow-xl shadow-orange-200 sm:p-8">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-white ring-1 ring-white/20">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L12 2.25l-.259 1.035a3.375 3.375 0 0 0-2.455 2.456L9 6l1.036.259a3.375 3.375 0 0 0 2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                        </svg>
                        ¡A jugar!
                    </span>

                    <h2 class="mt-2 break-words text-xl font-bold tracking-tight sm:mt-3 sm:text-3xl">
                        Actividades
                    </h2>

                    <p class="mt-1 text-sm text-orange-100 sm:mt-2 sm:text-base">
                        Elige un reto de {{ $class->name }}.
                    </p>

                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20 md:flex">
                    <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L12 2.25l-.259 1.035a3.375 3.375 0 0 0-2.455 2.456L9 6l1.036.259a3.375 3.375 0 0 0 2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Búsqueda y filtros --}}
        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <form method="GET" action="{{ route('student.activities.index', $class->id) }}"
                  class="flex flex-col gap-3 lg:flex-row lg:items-end">

                <div class="grid flex-1 gap-3 sm:grid-cols-2">

                    <div>
                        <label for="search" class="block text-xs font-bold uppercase tracking-wide text-slate-500">
                            Buscar actividad
                        </label>

                        <input type="search" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Ej. animales, fracciones…"
                               class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-semibold text-slate-800 placeholder-slate-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2">
                    </div>

                    <div>
                        <label for="type" class="block text-xs font-bold uppercase tracking-wide text-slate-500">
                            Tipo de reto
                        </label>

                        <select name="type" id="type"
                                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm font-semibold text-slate-800 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2">
                            <option value="">Todos los retos</option>
                            <option value="kahoot" @selected(request('type') === 'kahoot')>Kahoot</option>
                            <option value="crossword" @selected(request('type') === 'crossword')>Crucigrama</option>
                            <option value="word_search" @selected(request('type') === 'word_search')>Sopa de letras</option>
                            <option value="matching" @selected(request('type') === 'matching')>Unir conceptos</option>
                            <option value="roulette" @selected(request('type') === 'roulette')>Ruleta</option>
                        </select>
                    </div>

                </div>

                <div class="flex gap-2 lg:shrink-0">

                    <button type="submit"
                            class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-700 focus-visible:ring-offset-2 lg:flex-none">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        Buscar
                    </button>

                    @if (request()->filled('search') || request()->filled('type'))
                        <a href="{{ route('student.activities.index', $class->id) }}"
                           class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2">
                            Limpiar
                        </a>
                    @endif

                </div>

            </form>

        </section>

        {{-- Lista de actividades --}}
        @if ($activities->isEmpty())

            <section class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 text-orange-400">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.658L19.24 5.33a2.25 2.25 0 0 0-2.152-1.581H6.911a2.25 2.25 0 0 0-2.152 1.581l-2.408 7.85a2.25 2.25 0 0 0-.1.658Z" />
                    </svg>
                </div>

                <h3 class="mt-4 text-lg font-bold text-slate-900">
                    Todavía no hay actividades
                </h3>

                <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                    @if (request()->filled('search') || request()->filled('type'))
                        No encontramos actividades con ese filtro. Prueba con otra búsqueda.
                    @else
                        Cuando tu maestro publique una, aparecerá aquí.
                    @endif
                </p>

            </section>

        @else

            @php
                $pageActivities = $activities->getCollection();
                $unitGroups = $units
                    ->map(fn ($unit) => ['unit' => $unit, 'activities' => $pageActivities->where('unit_id', $unit->id)->values()])
                    ->reject(fn ($group) => $group['activities']->isEmpty())
                    ->values();
                $unassigned = $pageActivities->filter(fn ($activity) => $activity->unit_id === null)->values();
            @endphp

            {{-- Unidades con actividades --}}
            @foreach ($unitGroups as $group)
                <details class="group mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-slate-300"
                    @if ($loop->first) open @endif
                >
                    <summary class="flex cursor-pointer select-none list-none items-center justify-between gap-3 px-4 py-3.5 transition hover:bg-slate-50 active:bg-slate-100 sm:gap-4 sm:px-5 sm:py-4"
                             title="Toca para mostrar u ocultar las actividades de esta unidad">

                        <span class="flex min-w-0 items-center gap-2.5 sm:gap-3">
                            <span class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-700 sm:flex" aria-hidden="true">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.25 3h6.75m-6.75 0V15a3 3 0 0 1 3-3h3a3 3 0 0 1 3 3v.75m-16.5 0a2.25 2.25 0 0 0 2.25 2.25h1.5a2.25 2.25 0 0 0 2.25-2.25v-.75a2.25 2.25 0 0 1 2.25-2.25h.75m-11.25 3V6.75A2.25 2.25 0 0 1 5.25 4.5h9A2.25 2.25 0 0 1 16.5 6.75v6.75" />
                                </svg>
                            </span>

                            <span class="text-sm font-bold leading-snug text-orange-700">
                                Unidad {{ $group['unit']->number }}{{ filled($group['unit']->name) ? ' — '.$group['unit']->name : '' }}
                            </span>
                        </span>

                        <span class="flex shrink-0 items-center gap-1 sm:gap-2">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                                {{ $group['activities']->count() }} {{ $group['activities']->count() === 1 ? 'actividad' : 'actividades' }}
                            </span>

                            <span class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition-colors hover:text-slate-600" aria-hidden="true">
                                <svg class="chevron h-5 w-5 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </span>
                        </span>

                    </summary>

                    <div class="grid gap-3 border-t border-slate-100 px-4 py-5 sm:px-5 sm:grid-cols-2 xl:grid-cols-3">
                        @each('partials.activity-card-student', $group['activities'], 'activity')
                    </div>
                </details>
            @endforeach

            {{-- Actividades sin unidad --}}
            @if ($unassigned->isNotEmpty())
                <details class="group mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-slate-300"
                    @if ($unitGroups->isEmpty()) open @endif
                >
                    <summary class="flex cursor-pointer select-none list-none items-center justify-between gap-3 px-4 py-3.5 transition hover:bg-slate-50 active:bg-slate-100 sm:gap-4 sm:px-5 sm:py-4"
                             title="Toca para mostrar u ocultar estas actividades">

                        <span class="flex min-w-0 items-center gap-2.5 sm:gap-3">
                            <span class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 sm:flex" aria-hidden="true">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                                </svg>
                            </span>

                            <span class="text-sm font-bold leading-snug text-slate-500">
                                Sin unidad
                            </span>
                        </span>

                        <span class="flex shrink-0 items-center gap-1 sm:gap-2">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                                {{ $unassigned->count() }} {{ $unassigned->count() === 1 ? 'actividad' : 'actividades' }}
                            </span>

                            <span class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition-colors hover:text-slate-600" aria-hidden="true">
                                <svg class="chevron h-5 w-5 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </span>
                        </span>

                    </summary>

                    <div class="grid gap-3 border-t border-slate-100 px-4 py-5 sm:px-5 sm:grid-cols-2 xl:grid-cols-3">
                        @each('partials.activity-card-student', $unassigned, 'activity')
                    </div>
                </details>
            @endif

        @endif

        {{-- Paginación --}}
        @if ($activities->hasPages())
            <div class="mt-4">
                {{ $activities->links('vendor.pagination.student') }}
            </div>
        @endif

    </div>

    <style>
        details[open] > summary .chevron { transform: rotate(180deg); }
    </style>

@endsection
