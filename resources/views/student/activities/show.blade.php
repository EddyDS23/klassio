@extends('student.layouts.app')

@section('title', $activity->title)

@section('page-title', $activity->title)

@section('page-actions')
    <a href="{{ route('student.activities.index', $activity->class_id) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        Volver a actividades
    </a>
@endsection

@section('content')

    <div class="mx-auto max-w-3xl">

        @php
            $typeLabels = [
                'crossword' => 'Crucigrama',
                'word_search' => 'Sopa de letras',
                'matching' => 'Unir conceptos',
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

            $activityType = $activity->type;

            $statusLabels = [
                'active' => 'Activa',
                'published' => 'Publicada',
                'draft' => 'Borrador',
                'archived' => 'Archivada',
            ];
        @endphp

        {{-- Encabezado --}}
        <section class="rounded-2xl bg-orange-600 p-4 text-white shadow-xl shadow-orange-200 sm:p-8">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">

                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-white ring-1 ring-white/20">
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.306-.466.557-.327l5.603 3.112Z" />
                        </svg>
                        {{ $typeLabels[$activityType] ?? $activityType }}
                    </span>

                    <h2 class="mt-2 break-words text-xl font-bold tracking-tight sm:mt-3 sm:text-3xl">
                        {{ $activity->title }}
                    </h2>

                    <p class="mt-1 text-sm text-orange-100 sm:mt-2 sm:text-base">
                        {{ $activity->mode === 'team' ? 'Por equipos' : 'Individual' }} · {{ $activity->schoolClass->name }}
                    </p>

                </div>

                <div class="hidden h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20 md:flex">
                    <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.306-.466.557-.327l5.603 3.112Z" />
                    </svg>
                </div>

            </div>

        </section>

        {{-- Detalles del reto --}}
        <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <div class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-700 ring-1 ring-orange-100">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>

                <h3 class="text-base font-bold text-slate-900 sm:text-lg">
                    Detalles del reto
                </h3>

                <span class="ml-auto inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $activity->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                    {{ $statusLabels[$activity->status] ?? ucfirst($activity->status) }}
                </span>
            </div>

            @if ($activity->description)
                <p class="mt-3 leading-6 text-slate-600">
                    {{ $activity->description }}
                </p>
            @endif

            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 border-t border-slate-200 pt-3 text-sm text-slate-600">

                <span class="font-medium">
                    {{ $activity->max_score }} puntos
                </span>

                @if ($activity->time_limit)
                    <span class="inline-flex items-center gap-1.5 font-medium">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        {{ gmdate('i:s', $activity->time_limit) }} min
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

        </section>

        {{-- Lógica de Backend original --}}
        @php
            $studentId = auth()->id();

            if ($activity->mode === 'team') {
                $myTeam = $activity->teams()->whereHas('members', fn($q) => $q->where('student_id', $studentId))->first();

                $activeParticipation = $myTeam
                    ? $activity
                        ->participations()
                        ->where('team_id', $myTeam->id)
                        ->where('status', 'started')
                        ->latest()
                        ->first()
                    : null;

                $lastParticipation = $myTeam
                    ? $activity
                        ->participations()
                        ->where('team_id', $myTeam->id)
                        ->whereIn('status', ['completed', 'abandoned', 'expired'])
                        ->latest()
                        ->first()
                    : null;
            } else {
                $activeParticipation = $activity
                    ?->participations()
                    ->where('student_id', $studentId)
                    ->where('status', 'started')
                    ->latest()
                    ->first();

                $lastParticipation = $activity
                    ?->participations()
                    ->where('student_id', $studentId)
                    ->whereIn('status', ['completed', 'abandoned', 'expired'])
                    ->latest()
                    ->first();
            }

            $playRoute = match ($activity->type) {
                'crossword' => 'student.crossword.play',
                'kahoot' => 'student.kahoot.play',
                'word_search' => 'student.wordsearch.play',
                'matching' => 'student.matching.play',
                'roulette' => 'student.roulette.play',
                default => null,
            };

            $soundGame = match ($activity->type) {
                'crossword' => 'crossword',
                'kahoot' => 'kahoot',
                'word_search' => 'wordsearch',
                'matching' => 'matching',
                'roulette' => 'roulette',
                default => null,
            };

            $playRouteExists = $playRoute && \Illuminate\Support\Facades\Route::has($playRoute);
        @endphp

        {{-- Zona de juego --}}
        <section class="mt-4 rounded-2xl border-2 border-dashed border-slate-300 bg-white p-6 text-center shadow-sm sm:p-8">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-100 text-orange-700">
                <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.306-.466.557-.327l5.603 3.112Z" />
                </svg>
            </div>

            <h3 class="mt-4 text-lg font-bold text-slate-900 sm:text-xl">
                ¡A jugar!
            </h3>

            @if ($activeParticipation)

                {{-- Intento activo: continuar, finalizar o abandonar --}}
                <p class="mt-2 text-sm text-slate-500">
                    ¡Tienes una partida en juego! (intento #{{ $activeParticipation->attempt }}).
                </p>

                <div class="mt-5 flex flex-col justify-center gap-2.5 sm:flex-row sm:flex-wrap sm:gap-3">

                    @if ($playRouteExists)
                        <a href="{{ route($playRoute, $activity->id) }}"
                           @if ($soundGame) data-sound-game="{{ $soundGame }}" @endif
                           class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-700 focus-visible:ring-offset-2 sm:w-auto">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.396 1.663-.98l11.282 6.35c.746.418.746 1.542 0 1.96l-11.282 6.35c-.746.417-1.663-.124-1.663-.98V5.653Z" />
                            </svg>
                            Seguir jugando
                        </a>
                    @else
                        <p class="text-sm text-slate-500">El juego para esta actividad aún no está disponible.</p>
                    @endif

                    <form method="POST" action="{{ route('student.participation.finish', $activity->id) }}" class="sm:contents">
                        @csrf
                        <button type="submit"
                                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2 sm:w-auto">
                            Terminar
                        </button>
                    </form>

                    <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}" class="sm:contents" onsubmit="return confirm('¿Seguro que quieres dejar el juego?')">
                        @csrf
                        <button type="submit"
                                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-5 py-2.5 text-sm font-bold text-red-600 shadow-sm transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 sm:w-auto">
                            Dejar el juego
                        </button>
                    </form>

                </div>

            @elseif ($activity->due_at && now()->isAfter($activity->due_at))

                {{-- Actividad vencida --}}
                <p class="mt-2 text-sm text-slate-500">
                    Este juego ya terminó.
                </p>

                @if ($lastParticipation)
                    <div class="mt-5 flex justify-center">
                        <a href="{{ route('student.participation.result', $activity->id) }}"
                           class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-700 focus-visible:ring-offset-2">
                            Ver mi puntaje
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                @endif

            @elseif ($activity->mode === 'team' && ! isset($myTeam))

                {{-- Sin equipo asignado --}}
                <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                    Todavía no tienes equipo en este juego.
                    Pídele a tu maestro que te ponga en uno.
                </p>

            @else

                {{-- Sin intento activo: mostrar historial y botón de inicio --}}

                @if ($lastParticipation)
                    <div class="mx-auto mt-4 inline-flex flex-wrap items-center justify-center gap-x-2 gap-y-1 rounded-xl bg-slate-50 px-4 py-2.5 text-sm text-slate-600 ring-1 ring-slate-200">
                        <span class="font-semibold">Última vez: #{{ $lastParticipation->attempt }}</span>
                        <span class="text-slate-400" aria-hidden="true">·</span>
                        <span class="font-semibold">
                            {{ match ($lastParticipation->status) {
                                'completed' => '¡Lo lograste!',
                                'abandoned' => 'Lo dejaste',
                                'expired' => 'Se acabó el tiempo',
                                default => $lastParticipation->status,
                            } }}
                        </span>
                        <span class="text-slate-400" aria-hidden="true">·</span>
                        <span class="font-bold text-slate-800">{{ $lastParticipation->score }} pts</span>
                    </div>
                @endif

                <div class="mt-5 flex flex-col justify-center gap-2.5 sm:flex-row sm:flex-wrap sm:gap-3">

                    @if ($lastParticipation)
                        <a href="{{ route('student.participation.result', $activity->id) }}"
                           class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-600 focus-visible:ring-offset-2 sm:w-auto">
                            Ver mi puntaje
                        </a>
                    @endif

                    @if ($playRouteExists)
                        <form method="POST" action="{{ route('student.participation.start', $activity->id) }}" class="sm:contents">
                            @csrf
                            <button type="submit"
                                    @if ($soundGame) data-sound-game="{{ $soundGame }}" @endif
                                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-orange-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-700 focus-visible:ring-offset-2 sm:w-auto">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.396 1.663-.98l11.282 6.35c.746.418.746 1.542 0 1.96l-11.282 6.35c-.746.417-1.663-.124-1.663-.98V5.653Z" />
                                </svg>
                                {{ $lastParticipation ? '¡Otra vez!' : '¡A jugar!' }}
                            </button>
                        </form>
                    @else
                        <p class="text-sm text-slate-500">Este juego aún no está disponible.</p>
                    @endif

                </div>

            @endif

        </section>

    </div>

@endsection

@push('scripts')
    <script src="/js/klassio-sounds.js"></script>
    <script>
        document.querySelectorAll('[data-sound-game]').forEach(function (control) {
            control.addEventListener('click', function () {
                if (!window.KlassioSounds) return;
                var gameName = control.getAttribute('data-sound-game');
                KlassioSounds.prime(gameName);
                KlassioSounds.click();
            });
        });
    </script>
@endpush
