@extends('teacher.layouts.app')

@section('title', 'Actividades')
@section('brand-url', route('teacher.classes.index'))
@section('brand-caption', 'Panel del maestro')
@section('header-context')
<span class="hidden rounded-full bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700 sm:inline-flex">
    Actividades
</span>
@endsection

@section('content')
        <div class="mb-6 flex justify-end">
            <a href="{{ route('teacher.classes.show', $class->id) }}"
               class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver a la clase
            </a>
        </div>

        <!-- Encabezado -->
        <section class="mb-6 rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">
                        Gestión de actividades
                    </p>

                    <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        Actividades de {{ $class->name }}
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-emerald-100">
                        Crea, consulta, edita y configura las actividades
                        educativas de tus estudiantes.
                    </p>
                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 ring-1 ring-emerald-500 md:flex">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.332m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.332m0-14.25v14.25" />
                    </svg>
                </div>

            </div>

        </section>
        {{-- Los mensajes flash (éxito/error) los renderiza la barra del
             layout compartido teacher.layouts.app: aquí no se repiten. --}}


        <!-- Filtros -->
        <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div class="mb-4 flex items-center gap-3 border-b border-slate-100 pb-4">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.159 5.159a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.932 7.938a2.25 2.25 0 0 1-.659-1.591V5.305c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 class="text-base font-bold text-slate-900">
                        Filtrar actividades
                    </h2>

                    <p class="text-xs font-semibold text-slate-500">
                        Encuentra una actividad por nombre o tipo.
                    </p>
                </div>

            </div>


            <form method="GET"
                  action="{{ route('teacher.activities.index', $class->id) }}"
                  class="grid gap-4 md:grid-cols-3">

                <!-- Buscar -->
                <div class="md:col-span-1">

                    <label for="search"
                           class="mb-1.5 block text-sm font-bold text-slate-700">
                        Buscar por nombre
                    </label>

                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Nombre de la actividad"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                    >

                </div>


                <!-- Tipo -->
                <div>

                    <label for="type"
                           class="mb-1.5 block text-sm font-bold text-slate-700">
                        Tipo de actividad
                    </label>

                    <select
                        name="type"
                        id="type"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-100"
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

                        <option value="roulette"
                            {{ request('type') === 'roulette' ? 'selected' : '' }}>
                            Ruleta
                        </option>

                    </select>

                </div>


                <!-- Botones -->
                <div class="flex flex-col justify-end gap-2 sm:flex-row md:flex-col lg:flex-row">

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2"
                    >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        Buscar
                    </button>

                    @if (request()->filled('search') || request()->filled('type'))

                        <a
                            href="{{ route('teacher.activities.index', $class->id) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900"
                        >
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 0 13.803 3.7l3.181-3.182m0-4.991v4.99" />
                            </svg>
                            Limpiar
                        </a>

                    @endif

                </div>

            </form>

        </section>


        <!-- Encabezado de listado -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold tracking-tight text-slate-900">
                    Tus actividades
                </h2>

                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                    {{ $activities->total() }}
                </span>
            </div>

            <a
                href="{{ route('teacher.activities.create', $class->id) }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Crear actividad
            </a>

        </div>


        <!-- Listado -->
        @if ($activities->isEmpty())

            <section class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center shadow-sm">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-400 ring-1 ring-emerald-100">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                </div>

                <h2 class="mt-4 text-lg font-bold text-slate-900">
                    No hay actividades creadas
                </h2>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    Todavía no tienes actividades en esta clase.
                    Crea la primera para comenzar.
                </p>

                <a
                    href="{{ route('teacher.activities.create', $class->id) }}"
                    class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Crear primera actividad
                </a>

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

            @forelse ($unitGroups as $group)
                <details class="group mb-3 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                    @if ($loop->first) open @endif
                >
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-3.5 transition hover:bg-slate-50 sm:px-6">
                        <span class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                </svg>
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-bold text-slate-900">
                                    Unidad {{ $group['unit']->number }}{{ filled($group['unit']->name) ? ' — '.$group['unit']->name : '' }}
                                </span>
                                <span class="block text-xs font-semibold text-slate-500">
                                    {{ $group['activities']->count() }} {{ $group['activities']->count() === 1 ? 'actividad' : 'actividades' }}
                                </span>
                            </span>
                        </span>
                        <span class="shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </span>
                    </summary>

                    <div class="grid gap-3 border-t border-slate-100 p-4 sm:px-6 lg:grid-cols-2">
                        @each('partials.activity-card-teacher', $group['activities'], 'activity')
                    </div>
                </details>
            @empty

            @endforelse

            @if ($unassigned->isNotEmpty())
                <details class="group mb-3 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                    @if ($unitGroups->isEmpty()) open @endif
                >
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-3.5 transition hover:bg-slate-50 sm:px-6">
                        <span class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M21 7.5H3m4.5 0V5.25A2.25 2.25 0 0 1 9.75 3h4.5a2.25 2.25 0 0 1 2.25 2.25V7.5" />
                                </svg>
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-bold text-slate-900">
                                    Sin unidad
                                </span>
                                <span class="block text-xs font-semibold text-slate-500">
                                    {{ $unassigned->count() }} {{ $unassigned->count() === 1 ? 'actividad' : 'actividades' }}
                                </span>
                            </span>
                        </span>
                        <span class="shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </span>
                    </summary>

                    <div class="grid gap-3 border-t border-slate-100 p-4 sm:px-6 lg:grid-cols-2">
                        @each('partials.activity-card-teacher', $unassigned, 'activity')
                    </div>
                </details>
            @endif

        @endif



@endsection
