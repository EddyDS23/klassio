@extends('teacher.layouts.app')

@section('title', 'Dashboard - Maestro | Klassio')

@section('page-title', 'Dashboard')

@section('content')

    <!-- Bienvenida -->
    <section class="mb-8 rounded-2xl bg-emerald-700 p-6 text-white shadow-xl shadow-emerald-200 sm:p-8">

        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">

            <div class="max-w-2xl">
                <span class="mb-3 inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white ring-1 ring-white/20">
                    Área del maestro
                </span>

                <h2 class="text-2xl font-bold tracking-tight sm:text-4xl">
                    Estado de tus clases
                </h2>

                <p class="mt-3 text-sm leading-6 text-emerald-50 sm:text-base">
                    Organiza tus clases, administra a tus estudiantes y crea
                    actividades educativas para aprender de una manera más divertida.
                </p>
            </div>

            <div class="hidden h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20 md:flex">
                <svg class="h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.628 48.628 0 0 1 12 20.904a48.628 48.628 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                </svg>
            </div>

        </div>

    </section>


    <!-- Salas que se pueden recuperar -->
    <section class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="active-rooms-heading">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">Partidas en curso</p>
                <h2 id="active-rooms-heading" class="mt-1 text-2xl font-bold text-slate-900">Salas activas</h2>
                <p class="mt-2 text-sm text-slate-600">Vuelve a la sala si cerraste la pestaña o perdiste la conexión.</p>
            </div>

            <a href="{{ route('teacher.game-sessions.create') }}"
               class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 sm:w-auto focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Crear sala
            </a>
        </div>

        @forelse ($activeGameSessions as $activeSession)
            @php
                $gameNames = [
                    'word_search' => 'Sopa de letras',
                    'crossword' => 'Crucigrama',
                    'matching' => 'Relacionar',
                    'kahoot' => 'Kahoot',
                    'roulette' => 'Ruleta',
                ];
                $sessionStatusNames = [
                    'waiting' => 'En espera',
                    'starting' => 'Preparando',
                    'playing' => 'En juego',
                ];
                $sessionStatusColors = [
                    'waiting' => 'bg-emerald-100 text-emerald-700',
                    'starting' => 'bg-emerald-100 text-emerald-700',
                    'playing' => 'bg-amber-100 text-amber-800',
                ];
            @endphp

            <article class="mb-3 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 last:mb-0 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="break-words text-base font-bold text-slate-900">{{ $activeSession->activity->title }}</h3>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $sessionStatusColors[$activeSession->status] ?? 'bg-slate-100 text-slate-600' }}">
                            <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
                            {{ $sessionStatusNames[$activeSession->status] ?? $activeSession->status }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ $gameNames[$activeSession->activity->type] ?? ucfirst($activeSession->activity->type) }}
                        <span aria-hidden="true">·</span>
                        Código <span class="font-mono font-bold tracking-wider text-slate-800">{{ $activeSession->code }}</span>
                        <span aria-hidden="true">·</span>
                        {{ $activeSession->joined_participations_count }} / {{ $activeSession->max_players }} participantes
                    </p>
                </div>

                <a href="{{ route('teacher.game-sessions.show', $activeSession->id) }}"
                   class="inline-flex min-h-11 w-full shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 sm:w-auto transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-1">
                    Reabrir sala
                </a>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 py-6 text-center" role="status">

                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-400">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.007 0c.367 1.985-1.086 4.56-4.007 4.56M12 6h4.25c2.9 0 4.35 2.37 4.007 4.56M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18Z" />
                    </svg>
                </div>

                <p class="mt-3 font-bold text-slate-800">No tienes salas activas</p>
                <p class="mt-1 text-sm text-slate-600">Cuando crees una partida, podrás volver a ella desde aquí.</p>
            </div>
        @endforelse
    </section>


    <!-- Los mensajes de sesión (éxito/error) los muestra el layout unificado. -->

    <!-- Tarjetas principales -->
    <section class="mb-8 grid gap-6 md:grid-cols-2 md:items-stretch [&>a]:flex [&>a]:flex-col">

        <!-- Mis clases -->
        <a href="{{ route('teacher.classes.index') }}"
           class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-md">

            <div class="mb-5 flex items-start justify-between">

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100 transition group-hover:bg-emerald-700 group-hover:text-white group-hover:ring-emerald-700">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>

                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-100">
                    Administrar
                </span>

            </div>

            <h3 class="text-2xl font-bold text-slate-900">
                Mis clases
            </h3>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                Consulta tus clases, revisa estudiantes y administra
                las actividades de cada grupo.
            </p>

            <div class="mt-6 flex items-center gap-2 text-sm font-bold text-emerald-700">
                Ver mis clases
                <svg class="h-4 w-4 transition group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </div>

        </a>


        <!-- Crear clase -->
        <a href="{{ route('teacher.classes.create') }}"
           class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-amber-200 hover:shadow-md">

            <div class="mb-5 flex items-start justify-between">

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 ring-1 ring-amber-100 transition group-hover:bg-amber-700 group-hover:text-white group-hover:ring-amber-700">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>

                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-100">
                    Nueva clase
                </span>

            </div>

            <h3 class="text-2xl font-bold text-slate-900">
                Crear clase
            </h3>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                Crea un nuevo grupo, agrega información y comparte
                el código de acceso con tus estudiantes.
            </p>

            <div class="mt-6 flex items-center gap-2 text-sm font-bold text-amber-700">
                Crear una clase
                <svg class="h-4 w-4 transition group-hover:translate-x-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </div>

        </a>

    </section>

@endsection
