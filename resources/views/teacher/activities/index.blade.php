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
        <div class="mb-6">
            <a href="{{ route('teacher.classes.show', $class->id) }}" class="inline-flex items-center gap-2 text-sm font-black text-emerald-700 transition hover:text-emerald-900">
                <span aria-hidden="true">←</span> Volver a la clase
            </a>
        </div>

        <!-- Encabezado -->
        <section class="mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">

            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

                <div>
                    <span class="mb-3 inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-black uppercase tracking-wider">
                        Gestión de actividades
                    </span>

                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                        Actividades de {{ $class->name }}
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-emerald-50 sm:text-base">
                        Crea, consulta, edita y configura las actividades
                        educativas de tus estudiantes.
                    </p>
                </div>

                <div class="hidden h-28 w-28 items-center justify-center rounded-3xl bg-white/15 text-7xl md:flex">
                    📚
                </div>

            </div>

        </section>


        <!-- Mensaje de éxito -->
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm">

                <span class="text-xl">✓</span>

                <div>
                    <p class="font-bold">
                        ¡Operación exitosa!
                    </p>

                    <p class="text-sm">
                        {{ session('success') }}
                    </p>
                </div>

            </div>
        @endif


        <!-- Filtros -->
        <section class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-200/60 sm:p-8">

            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                        Buscar actividades
                    </p>

                    <h2 class="mt-1 text-xl font-black text-slate-900">
                        Filtra tus actividades
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Encuentra rápidamente una actividad por nombre o tipo.
                    </p>
                </div>

                <div class="hidden rounded-2xl bg-emerald-50 px-4 py-3 text-2xl sm:block">
                    🔎
                </div>

            </div>


            <form method="GET"
                  action="{{ route('teacher.activities.index', $class->id) }}"
                  class="grid gap-5 md:grid-cols-3">

                <!-- Buscar -->
                <div class="md:col-span-1">

                    <label for="search"
                           class="mb-2 block text-sm font-bold text-slate-700">
                        Buscar por nombre
                    </label>

                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Nombre de la actividad"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >

                </div>


                <!-- Tipo -->
                <div>

                    <label for="type"
                           class="mb-2 block text-sm font-bold text-slate-700">
                        Tipo de actividad
                    </label>

                    <select
                        name="type"
                        id="type"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
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
                <div class="flex flex-col justify-end gap-3 sm:flex-row md:flex-col lg:flex-row">

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-emerald-200"
                    >
                        <span>🔎</span>
                        Buscar
                    </button>

                    @if (request()->filled('search') || request()->filled('type'))

                        <a
                            href="{{ route('teacher.activities.index', $class->id) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50"
                        >
                            <span>↺</span>
                            Limpiar
                        </a>

                    @endif

                </div>

            </form>

        </section>


        <!-- Encabezado de listado -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                    Biblioteca de actividades
                </p>

                <h2 class="mt-1 text-2xl font-black text-slate-900">
                    Tus actividades
                </h2>
            </div>

            <a
                href="{{ route('teacher.activities.create', $class->id) }}"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700 hover:shadow-xl"
            >
                <span class="text-lg">+</span>
                Crear actividad
            </a>

        </div>


        <!-- Listado -->
        @if ($activities->isEmpty())

            <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">

                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 text-5xl">
                    📝
                </div>

                <h2 class="mt-5 text-2xl font-black text-slate-900">
                    No hay actividades creadas
                </h2>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    Todavía no tienes actividades en esta clase.
                    Crea la primera para comenzar.
                </p>

                <a
                    href="{{ route('teacher.activities.create', $class->id) }}"
                    class="mt-6 inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-black text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-700"
                >
                    <span>+</span>
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
                <details class="mb-4 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60"
                    @if ($loop->first) open @endif
                >
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4 transition hover:bg-slate-50">
                        <span class="flex min-w-0 items-center gap-3">
                            <span class="text-xl">📂</span>
                            <span class="truncate text-sm font-black uppercase tracking-widest text-emerald-700">
                                Unidad {{ $group['unit']->number }}{{ filled($group['unit']->name) ? ' — '.$group['unit']->name : '' }}
                            </span>
                        </span>
                        <span class="shrink-0 rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">
                            {{ $group['activities']->count() }} {{ $group['activities']->count() === 1 ? 'actividad' : 'actividades' }}
                        </span>
                    </summary>

                    <div class="grid gap-6 border-t border-slate-100 px-4 py-5 sm:px-6 lg:grid-cols-2">
                        @each('partials.activity-card-teacher', $group['activities'], 'activity')
                    </div>
                </details>
            @empty

            @endforelse

            @if ($unassigned->isNotEmpty())
                <details class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg shadow-slate-200/60"
                    @if ($unitGroups->isEmpty()) open @endif
                >
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4 transition hover:bg-slate-50">
                        <span class="flex min-w-0 items-center gap-3">
                            <span class="text-xl">🗂️</span>
                            <span class="truncate text-sm font-black uppercase tracking-widest text-slate-500">
                                Sin unidad
                            </span>
                        </span>
                        <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">
                            {{ $unassigned->count() }} {{ $unassigned->count() === 1 ? 'actividad' : 'actividades' }}
                        </span>
                    </summary>

                    <div class="grid gap-6 border-t border-slate-100 px-4 py-5 sm:px-6 lg:grid-cols-2">
                        @each('partials.activity-card-teacher', $unassigned, 'activity')
                    </div>
                </details>
            @endif

        @endif



@endsection
