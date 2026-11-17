@extends('student.layouts.app')

@section('title', $class->name)

@section('page-title', $class->name)

@section('page-actions')
    <a href="{{ route('student.classes.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Mis clases
    </a>
@endsection

@section('content')

    @php
        $typeLabels = [
            'crossword' => 'Crucigrama',
            'word_search' => 'Sopa de letras',
            'matching' => 'Relacionar',
            'kahoot' => 'Kahoot',
            'roulette' => 'Ruleta',
        ];

        $typePillClasses = [
            'crossword' => 'bg-amber-100 text-amber-700',
            'word_search' => 'bg-orange-100 text-orange-700',
            'matching' => 'bg-amber-100 text-amber-700',
            'kahoot' => 'bg-pink-100 text-pink-700',
            'roulette' => 'bg-pink-100 text-pink-700',
        ];

        $activityType = $activity?->type ?? null;
    @endphp

    <div class="mx-auto max-w-7xl">

        {{-- Encabezado de la clase --}}
        <section class="rounded-2xl bg-orange-600 p-4 text-white shadow-xl shadow-orange-200 sm:p-8">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-white ring-1 ring-white/20">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        ¡Mi clase!
                    </span>

                    <h2 class="mt-2 break-words text-xl font-bold tracking-tight sm:mt-3 sm:text-3xl">
                        {{ $class->name }}
                    </h2>

                    <p class="mt-1 text-sm text-orange-100 sm:mt-2 sm:text-base">
                        Mira tus juegos y juega con tus amigos.
                    </p>

                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20 md:flex">
                    <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Información y compañeros --}}
        <section class="mt-4 grid gap-4 lg:grid-cols-3">

            {{-- Información de la clase --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 lg:col-span-2">

                <div class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-700 ring-1 ring-orange-100">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 sm:text-lg">
                        Información de la clase
                    </h3>
                </div>

                @if ($class->description)
                    <p class="mt-3 leading-6 text-slate-600">
                        {{ $class->description }}
                    </p>
                @else
                    <p class="mt-3 text-sm italic text-slate-500">
                        Sin descripción.
                    </p>
                @endif

                <div class="mt-3 flex flex-wrap items-center gap-2">

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-50 px-3 py-1.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-200">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="min-w-0 break-words">{{ $class->teacher->name }}</span>
                    </span>

                    <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-bold {{ $class->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                        {{ $class->status === 'active' ? 'Clase activa' : 'Clase archivada' }}
                    </span>

                </div>

            </div>

            {{-- Compañeros --}}
            <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

                <div class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-700 ring-1 ring-orange-100">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 sm:text-lg">
                        Compañeros
                    </h3>
                </div>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Mira quién juega contigo en esta clase.
                </p>

                <a href="{{ route('student.class.students', $class->id) }}"
                   class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-700 focus-visible:ring-offset-2 lg:mt-auto">
                    Ver compañeros
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>

            </div>

        </section>

        {{-- Lo nuevo --}}
        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">

                <div class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-700 ring-1 ring-orange-100">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-900 sm:text-lg">
                            Lo nuevo
                        </h3>

                        <p class="text-sm text-slate-500">
                            Lo último que dejó tu maestro.
                        </p>
                    </div>
                </div>

                <a href="{{ route('student.activities.index', $class->id) }}"
                   class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2">
                    Ver todo
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>

            </div>

            @if ($activity)

                <article class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">

                    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">

                        <div class="min-w-0">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide {{ $typePillClasses[$activityType] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $typeLabels[$activityType] ?? $activity->type }}
                            </span>

                            <h4 class="mt-2 break-words text-base font-bold text-slate-900 sm:text-lg">
                                {{ $activity->title }}
                            </h4>

                            @if ($activity->description)
                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    {{ $activity->description }}
                                </p>
                            @else
                                <p class="mt-1 text-sm italic text-slate-500">
                                    Sin descripción.
                                </p>
                            @endif
                        </div>

                        <a href="{{ route('student.activities.show', $activity->id) }}"
                           class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-700 focus-visible:ring-offset-2">
                            Ver actividad
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>

                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 border-t border-slate-200 pt-3 text-sm text-slate-600">

                        <span class="inline-flex items-center gap-1.5 font-medium">
                            @if ($activity->mode === 'team')
                                <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                                Con amigos
                            @else
                                <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                Solo yo
                            @endif
                        </span>

                        <span class="font-medium">
                            {{ $activity->max_score }} puntos
                        </span>

                        @if ($activity->time_limit)
                            <span class="inline-flex items-center gap-1.5 font-medium">
                                <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $activity->time_limit }} segundos
                            </span>
                        @endif

                        @if ($activity->due_at)
                            <span class="inline-flex items-center gap-1.5 font-medium">
                                <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                {{ $activity->due_at->format('d/m/Y H:i') }}
                            </span>
                        @endif

                    </div>

                </article>

            @else

                <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 text-orange-400">
                        <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.658L19.24 5.33a2.25 2.25 0 0 0-2.152-1.581H6.911a2.25 2.25 0 0 0-2.152 1.581l-2.408 7.85a2.25 2.25 0 0 0-.1.658Z" />
                        </svg>
                    </div>

                    <h4 class="mt-4 text-lg font-bold text-slate-800">
                        No hay actividades todavía
                    </h4>

                    <p class="mt-2 text-sm text-slate-500">
                        Cuando tu maestro publique una actividad, aparecerá aquí.
                    </p>

                </div>

            @endif

        </section>

    </div>

@endsection
