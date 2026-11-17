@extends('teacher.layouts.app')

@section('title', $activity->title . ' | Klassio')
@section('brand-url', route('teacher.dashboard'))
@section('brand-caption', 'Panel del profesor')
@section('header-context')
<span class="hidden text-sm font-semibold text-slate-600 sm:block">
    {{ auth()->user()->name }}
</span>
<form action="{{ url('/logout') }}" method="POST">
    @csrf
    <button type="submit" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
        Cerrar sesión
    </button>
</form>
@endsection

@php
    $typeIcons = [
        'kahoot' => 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z',
        'crossword' => 'M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-3.409-.042.75.75 0 0 1-.707-.632 48.264 48.264 0 0 1-.034-2.897.75.75 0 0 1 .515-.707 48.334 48.334 0 0 1 2.887-.085v0c.355 0 .676-.186.959-.401.29-.221.634-.349 1.003-.349 1.036 0 1.875-1.007 1.875-2.25S12.535 1.5 11.5 1.5s-1.875 1.007-1.875 2.25c0 .369.128.713.349 1.003.215.283.401.604.401.959v0c0 .355-.186.676-.401.959-.221.29-.349.634-.349 1.003 0 1.036-1.007 1.875-2.25 1.875s-2.25-.84-2.25-1.875c0-.369.128-.713.349-1.003.215-.283.401-.604.401-.959v0a.64.64 0 0 0-.657-.643 48.39 48.39 0 0 0-3.409.042.75.75 0 0 0-.707.632 48.264 48.264 0 0 0-.034 2.897.75.75 0 0 0 .515.707c.438.154.902.226 1.397.226v0c.466 0 .872-.054 1.21-.14v5.628c0 1.499 1.193 2.755 2.691 2.755h.002c.325 0 .591.266.591.591v4.875c0 .414.336.75.75.75h.006a.75.75 0 0 0 .75-.75v-4.875c0-.325.266-.591.591-.591h.002c1.498 0 2.691-1.256 2.691-2.755V9.75c.338.086.744.14 1.21.14v0c.495 0 .959-.072 1.397-.226a.75.75 0 0 0 .515-.707 48.264 48.264 0 0 0-.034-2.897.75.75 0 0 0-.707-.632 48.39 48.39 0 0 0-3.409-.042.64.64 0 0 0-.657.643Z',
        'word_search' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
        'matching' => 'M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.242',
        'roulette' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 0 13.803 3.7l3.181-3.182m0-4.991v4.99',
    ];

    $typeIcon = $typeIcons[$activity->type] ?? $typeIcons['word_search'];

    // Ruta de configuración según el tipo (editar si ya tiene contenido, configurar si no).
    $configRoute = null;
    if ($activity->type === 'crossword') {
        $configRoute = ($activity->crossword && $activity->crossword->words()->exists())
            ? 'teacher.crossword.edit'
            : 'teacher.crossword.configure';
    } elseif ($activity->type === 'kahoot') {
        $configRoute = ($activity->kahoot && $activity->kahoot->questions()->exists())
            ? 'teacher.kahoot.edit'
            : 'teacher.kahoot.configure';
    } elseif (in_array($activity->type, ['wordsearch', 'word_search'])) {
        $configRoute = ($activity->wordsearch && $activity->wordsearch->words()->exists())
            ? 'teacher.wordsearch.edit'
            : 'teacher.wordsearch.configure';
    } elseif ($activity->type === 'matching') {
        $configRoute = ($activity->matching && $activity->matching->items()->exists())
            ? 'teacher.matching.edit'
            : 'teacher.matching.configure';
    } elseif ($activity->type === 'roulette') {
        $configRoute = ($activity->roulette && $activity->roulette->items()->exists())
            ? 'teacher.roulette.edit'
            : 'teacher.roulette.configure';
    }
@endphp

@section('content')

    <div class="mx-auto max-w-5xl">

        <div class="mb-6">
            <a href="{{ route('teacher.activities.index', $activity->class_id) }}"
               class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver a actividades
            </a>
        </div>

        {{-- Los mensajes flash (éxito/error) los renderiza la barra del
             layout compartido teacher.layouts.app: aquí no se repiten. --}}

        {{-- Encabezado de la actividad --}}
        <section class="mb-6 rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">
                            Detalle de actividad
                        </p>

                        @if ($activity->status === 'draft')
                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-700">Borrador</span>
                        @elseif ($activity->status === 'published')
                            <span class="rounded-full bg-white px-2.5 py-0.5 text-xs font-bold text-emerald-700">Publicada</span>
                        @elseif ($activity->status === 'closed')
                            <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white ring-1 ring-white/30">Cerrada</span>
                        @else
                            <span class="rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white capitalize ring-1 ring-white/30">{{ $activity->status }}</span>
                        @endif
                    </div>

                    <h2 class="mt-1 break-words text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ $activity->title }}
                    </h2>

                    <p class="mt-2 flex items-center gap-1.5 text-sm font-bold text-emerald-100">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                        </svg>
                        @include('partials.game-type-label', ['type' => $activity->type])
                    </p>
                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 ring-1 ring-emerald-500 md:flex">
                    <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Información de la actividad --}}
        <section class="mb-6">

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                {{-- Descripción --}}
                <div class="flex items-start gap-3 border-b border-slate-100 pb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-slate-900">
                            Descripción
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-slate-600">
                            {{ $activity->description ?: 'Sin descripción disponible para esta actividad.' }}
                        </p>
                    </div>
                </div>

                {{-- Datos en chips --}}
                <div class="grid grid-cols-2 gap-2 pt-4 sm:grid-cols-4">

                    <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                            Modo
                        </p>

                        <p class="mt-1 flex items-center gap-1.5 text-sm font-bold text-slate-800">
                            <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                @if ($activity->mode === 'team')
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.071 5.071 0 0 0-.02-.845 7.002 7.002 0 0 0-2.006-3.326 5.732 5.732 0 0 0-1.664-1.092 5.85 5.85 0 0 0-2.31-.447 5.85 5.85 0 0 0-2.31.447 5.732 5.732 0 0 0-1.664 1.092 7.002 7.002 0 0 0-2.006 3.326 5.071 5.071 0 0 0-.02.845m12 0a5.078 5.078 0 0 1-3.75.002m-4.5-.002a5.078 5.078 0 0 1-3.75.002M12 10.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Zm0 0v.375" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                @endif
                            </svg>
                            @if ($activity->mode === 'team')
                                Por equipos
                            @elseif ($activity->mode === 'individual')
                                Individual
                            @else
                                <span class="capitalize">{{ $activity->mode }}</span>
                            @endif
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                            Puntuación
                        </p>

                        <p class="mt-1 flex items-center gap-1.5 text-sm font-bold text-slate-800">
                            <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                            </svg>
                            {{ $activity->max_score }} pts
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                            Tiempo límite
                        </p>

                        <p class="mt-1 flex items-center gap-1.5 text-sm font-bold text-slate-800">
                            <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            @if ($activity->time_limit && $activity->time_limit % 60 === 0)
                                {{ $activity->time_limit / 60 }} min
                            @else
                                {{ $activity->time_limit }} seg
                            @endif
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-50 px-3 py-2.5 ring-1 ring-slate-200">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                            Fecha límite
                        </p>

                        <p class="mt-1 flex items-center gap-1.5 text-sm font-bold text-slate-800">
                            <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            @if ($activity->due_at)
                                {{ $activity->due_at->format('d/m/Y H:i') }}
                            @else
                                Sin fecha
                            @endif
                        </p>
                    </div>

                </div>

                {{-- Equipos --}}
                @if ($activity->mode === 'team')
                    <div class="mt-4 flex flex-col gap-3 rounded-xl bg-emerald-50 p-3 ring-1 ring-emerald-100 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-2.5">
                            <svg class="h-5 w-5 shrink-0 text-emerald-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.071 5.071 0 0 0-.02-.845 7.002 7.002 0 0 0-2.006-3.326 5.732 5.732 0 0 0-1.664-1.092 5.85 5.85 0 0 0-2.31-.447 5.85 5.85 0 0 0-2.31.447 5.732 5.732 0 0 0-1.664 1.092 7.002 7.002 0 0 0-2.006 3.326 5.071 5.071 0 0 0-.02.845m12 0a5.078 5.078 0 0 1-3.75.002m-4.5-.002a5.078 5.078 0 0 1-3.75.002M12 10.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Zm0 0v.375" />
                            </svg>

                            <p class="text-sm font-bold text-slate-700">
                                Actividad por equipos. Revisa los integrantes antes de continuar.
                            </p>
                        </div>

                        <a href="{{ route('teacher.teams.index', $activity->id) }}"
                           class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
                            Revisar equipos
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>

                    </div>
                @endif

            </div>

        </section>

        {{-- Acciones de la actividad --}}
        <section class="mb-6">

            <h3 class="mb-3 text-lg font-bold text-slate-900">
                Acciones de la actividad
            </h3>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                <div class="flex flex-col gap-3">

                    @if ($activity->status === 'draft')

                        {{-- Publicar (acción principal) --}}
                        <form action="{{ route('teacher.activities.publish', $activity->id) }}" method="POST">
                            @csrf

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                </svg>
                                Publicar actividad
                            </button>

                            <p class="mt-2 text-center text-xs font-medium text-slate-500">
                                Hazla visible para tus estudiantes.
                            </p>
                        </form>

                        {{-- Editar (secundaria) --}}
                        <a href="{{ route('teacher.activities.edit', $activity->id) }}"
                           class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">
                            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            Editar información
                        </a>

                    @elseif ($activity->status === 'published')

                        {{-- Sala multijugador (acción principal) --}}
                        <a href="{{ route('teacher.game-sessions.create', ['activity_id' => $activity->id]) }}"
                           class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                            </svg>
                            Crear sala en vivo
                        </a>

                        <p class="text-center text-xs font-medium text-slate-500">
                            Crea una partida y comparte el código con tus estudiantes.
                        </p>

                        {{-- Cerrar (destructiva) --}}
                        <form action="{{ route('teacher.activities.close', $activity->id) }}" method="POST">
                            @csrf

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-5 py-2.5 text-sm font-bold text-red-600 transition hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                                <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                Cerrar actividad
                            </button>

                            <p class="mt-2 text-center text-xs font-medium text-slate-500">
                                Finaliza la participación de los estudiantes. No se puede revertir.
                            </p>
                        </form>

                    @else

                        <div class="flex items-center gap-2.5 rounded-xl bg-slate-50 p-3 ring-1 ring-slate-200">
                            <svg class="h-5 w-5 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>

                            <p class="text-sm font-bold text-slate-600">
                                Esta actividad está cerrada y no tiene acciones adicionales disponibles.
                            </p>
                        </div>

                    @endif

                </div>

            </div>

        </section>

        {{-- Configuración del juego (solo borrador) --}}
        @if ($activity->status === 'draft' && $configRoute)

            <section class="mb-6">

                <h3 class="mb-3 text-lg font-bold text-slate-900">
                    Configuración del juego
                </h3>

                <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5">

                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                            </svg>
                        </div>

                        <p class="text-sm font-bold text-slate-700">
                            Configura el contenido de
                            @include('partials.game-type-label', ['type' => $activity->type])
                            antes de publicarla.
                        </p>
                    </div>

                    <a href="{{ route($configRoute, $activity->id) }}"
                       class="inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Configurar juego
                    </a>

                </div>

            </section>

        @endif

        {{-- Resultados y seguimiento --}}
        <section class="mb-8">

            <h3 class="mb-3 text-lg font-bold text-slate-900">
                Resultados y seguimiento
            </h3>

            <div class="grid gap-3 sm:grid-cols-3">

                <a href="{{ route('teacher.activities.results', $activity->id) }}"
                   class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-300 hover:shadow-md sm:p-5">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </div>

                    <h4 class="mt-3 text-sm font-bold text-slate-900">
                        Resultados de alumnos
                    </h4>

                    <p class="mt-1 text-xs font-medium leading-5 text-slate-500">
                        Calificaciones y avance de cada estudiante.
                    </p>

                    <span class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-emerald-700">
                        Ver resultados
                        <svg class="h-4 w-4 transition group-hover:translate-x-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </span>

                </a>

                <a href="{{ route('teacher.activities.ranking', $activity->id) }}"
                   class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-300 hover:shadow-md sm:p-5">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.458 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.515M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                        </svg>
                    </div>

                    <h4 class="mt-3 text-sm font-bold text-slate-900">
                        Ranking
                    </h4>

                    <p class="mt-1 text-xs font-medium leading-5 text-slate-500">
                        Clasificación de los participantes.
                    </p>

                    <span class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-emerald-700">
                        Ver ranking
                        <svg class="h-4 w-4 transition group-hover:translate-x-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </span>

                </a>

                <a href="{{ route('teacher.activities.report', $activity->id) }}"
                   class="group flex flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-300 hover:shadow-md sm:p-5">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>

                    <h4 class="mt-3 text-sm font-bold text-slate-900">
                        Reporte
                    </h4>

                    <p class="mt-1 text-xs font-medium leading-5 text-slate-500">
                        Reporte general de esta actividad.
                    </p>

                    <span class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-emerald-700">
                        Ver reporte
                        <svg class="h-4 w-4 transition group-hover:translate-x-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </span>

                </a>

            </div>

        </section>

    </div>

@endsection
