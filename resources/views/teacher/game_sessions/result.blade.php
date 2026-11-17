@extends('teacher.layouts.app')

@section('title', 'Resultados — Sala '.$session->code)

@section('page-title', 'Resultados')

@section('content')

    @php
        $isWinnerType = ($state['result_type'] ?? null) === 'winner';
        $isDraw = ($state['result_type'] ?? null) === 'draw';
        $winnerId = $state['winner_participation_id'] ?? null;

        $typeIcons = [
            'kahoot' => 'm3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z',
            'crossword' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m0 0H21',
            'word_search' => 'm21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z',
            'matching' => 'M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244',
            'roulette' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99',
        ];

        $typeIcon = $typeIcons[$session->activity->type] ?? $typeIcons['roulette'];

        $gameLabel = match ($session->activity->type) {
            'crossword' => 'Crucigrama',
            'kahoot' => 'Kahoot',
            'word_search' => 'Sopa de letras',
            'matching' => 'Relacionar',
            default => 'Ruleta',
        };

        $progressLabel = match ($session->activity->type) {
            'crossword', 'word_search' => 'Palabras',
            'matching' => 'Parejas',
            'kahoot' => 'Preguntas',
            default => 'Respuestas',
        };

        $resultLabel = match ($state['result_type'] ?? null) {
            'winner' => 'Con ganador',
            'draw' => 'Empate',
            'collaborative_success' => 'Objetivo cumplido',
            default => 'Finalizada',
        };

        $positionChipClass = fn (int $position): string => match (true) {
            $position === 1 => 'bg-amber-100 text-amber-700 ring-amber-200',
            $position === 2 => 'bg-slate-100 text-slate-700 ring-slate-200',
            $position === 3 => 'bg-orange-100 text-orange-700 ring-orange-200',
            default => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        };

        $statusPill = fn (string $status): array => match ($status) {
            'completed' => ['bg-emerald-100 text-emerald-700', 'Completó'],
            'started' => ['bg-amber-100 text-amber-800', 'En progreso'],
            'abandoned' => ['bg-slate-100 text-slate-600', 'Abandonó'],
            'expired' => ['bg-slate-100 text-slate-600', 'Tiempo agotado'],
            default => ['bg-slate-100 text-slate-600', ucfirst($status)],
        };
    @endphp

    <div class="mx-auto max-w-5xl">

        <div class="mb-6">
            <a href="{{ route('teacher.dashboard') }}"
               class="inline-flex self-start items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver al panel
            </a>
        </div>

        {{-- Encabezado --}}
        <section class="mb-6 rounded-2xl bg-emerald-700 p-5 text-white shadow-sm sm:p-6">

            <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">

                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">
                        Salas multijugador
                    </p>

                    <h2 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">
                        Resultados de la partida
                    </h2>

                    <p class="mt-2 flex flex-wrap items-center gap-x-1.5 gap-y-1 text-sm font-bold text-emerald-100">
                        <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                        </svg>
                        {{ $gameLabel }} · {{ $session->activity->title }}
                    </p>
                </div>

                <div class="flex shrink-0 flex-col items-start gap-2 md:items-end">
                    <span class="rounded-full bg-emerald-600 px-3 py-1 font-mono text-sm font-bold tracking-[0.2em] text-white ring-1 ring-emerald-500">
                        {{ $session->code }}
                    </span>

                    <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide bg-emerald-100 text-emerald-800">
                        {{ $resultLabel }}
                    </span>
                </div>

            </div>

        </section>

        {{-- Resultado --}}
        @if ($isWinnerType || $isDraw)
            @php
                $winner = $players->firstWhere('id', $winnerId);
            @endphp

            <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">
                    {{ $isWinnerType ? 'Ganador de la partida' : 'Empate en la partida' }}
                </p>

                <div class="mt-3 flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 ring-1 ring-amber-200">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0-3.375-6.375m-7.125 0L6 18.75m4.875-9.75L9.75 2.25h4.5l-1.125 6.75h-2.25Z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <p class="truncate text-lg font-bold text-slate-900">
                            @if ($isWinnerType)
                                {{ $winner?->team?->name ?? $winner?->student?->name ?? 'Jugador' }}
                            @else
                                Empate entre los mejores puntajes
                            @endif
                        </p>

                        @if ($isWinnerType && $winner)
                            <p class="text-sm text-slate-500">
                                {{ $winner->score }} de {{ $session->activity->max_score }} puntos
                            </p>
                        @endif
                    </div>
                </div>

            </section>
        @endif

        {{-- Clasificación --}}
        <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

            <h3 class="text-lg font-bold text-slate-900">
                Clasificación
            </h3>

            <p class="mt-0.5 text-sm text-slate-600">
                Ordenada por puntaje. {{ $progressLabel }} muestra el avance de cada participante.
            </p>

            @if ($players->isEmpty())
                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600" role="status">
                    Esta sala no registró participantes.
                </div>
            @else

                {{-- Tarjetas (móvil) --}}
                <div class="mt-4 grid gap-3 sm:hidden">

                    @foreach ($players as $index => $p)
                        @php
                            [$answeredCount, $itemCount] = match ($session->activity->type) {
                                'crossword' => [
                                    $p->crosswordAnswers()->count(),
                                    $session->activity->crossword?->words()->count() ?? 0,
                                ],
                                'word_search' => [
                                    $p->wordsearchAnswers()->count(),
                                    $session->activity->wordsearch?->words()->count() ?? 0,
                                ],
                                'matching' => [
                                    $p->matchingAnswers()->where('is_correct', true)->distinct('matching_item_id')->count('matching_item_id'),
                                    $session->activity->matching?->items()->count() ?? 0,
                                ],
                                'kahoot' => [
                                    $p->kahootAnswers()->count(),
                                    $session->activity->kahoot?->questions()->count() ?? 0,
                                ],
                                default => [
                                    $p->rouletteAnswers()->count(),
                                    $session->activity->roulette?->items()->count() ?? 0,
                                ],
                            };

                            [$statusClass, $statusText] = $statusPill($p->status);
                        @endphp

                        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm font-bold ring-1 {{ $positionChipClass($index + 1) }}">
                                    {{ $index + 1 }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-900">
                                        {{ $p->team?->name ?? $p->student?->name }}
                                    </p>
                                </div>

                                <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>

                            <div class="mt-3 grid grid-cols-3 gap-2">
                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Puntos</p>
                                    <p class="mt-0.5 text-xs font-bold text-emerald-700">
                                        {{ $p->score }} / {{ $session->activity->max_score }}
                                    </p>
                                </div>

                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $progressLabel }}</p>
                                    <p class="mt-0.5 text-xs font-bold text-slate-700">
                                        {{ $answeredCount }} / {{ $itemCount }}
                                    </p>
                                </div>

                                <div class="rounded-lg bg-slate-50 px-2 py-1.5 ring-1 ring-slate-200">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tiempo</p>
                                    <p class="mt-0.5 text-xs font-bold text-slate-700">
                                        {{ $p->elapsed_seconds !== null ? sprintf('%02d:%02d', intdiv((int) $p->elapsed_seconds, 60), (int) $p->elapsed_seconds % 60) : '—' }}
                                    </p>
                                </div>
                            </div>

                        </article>
                    @endforeach

                </div>

                {{-- Tabla (desktop) --}}
                <div class="mt-4 hidden overflow-hidden rounded-2xl border border-slate-200 shadow-sm sm:block">

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="whitespace-nowrap px-4 py-3.5 font-bold">#</th>
                                    <th class="whitespace-nowrap px-4 py-3.5 font-bold">Jugador</th>
                                    <th class="whitespace-nowrap px-4 py-3.5 font-bold">Puntaje</th>
                                    <th class="whitespace-nowrap px-4 py-3.5 font-bold">{{ $progressLabel }}</th>
                                    <th class="whitespace-nowrap px-4 py-3.5 font-bold">Tiempo</th>
                                    <th class="whitespace-nowrap px-4 py-3.5 font-bold">Estado</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @foreach ($players as $index => $p)
                                    @php
                                        [$answeredCount, $itemCount] = match ($session->activity->type) {
                                            'crossword' => [
                                                $p->crosswordAnswers()->count(),
                                                $session->activity->crossword?->words()->count() ?? 0,
                                            ],
                                            'word_search' => [
                                                $p->wordsearchAnswers()->count(),
                                                $session->activity->wordsearch?->words()->count() ?? 0,
                                            ],
                                            'matching' => [
                                                $p->matchingAnswers()->where('is_correct', true)->distinct('matching_item_id')->count('matching_item_id'),
                                                $session->activity->matching?->items()->count() ?? 0,
                                            ],
                                            'kahoot' => [
                                                $p->kahootAnswers()->count(),
                                                $session->activity->kahoot?->questions()->count() ?? 0,
                                            ],
                                            default => [
                                                $p->rouletteAnswers()->count(),
                                                $session->activity->roulette?->items()->count() ?? 0,
                                            ],
                                        };

                                        [$statusClass, $statusText] = $statusPill($p->status);
                                    @endphp

                                    <tr class="transition hover:bg-emerald-50/40 {{ $p->id === $winnerId ? 'bg-amber-50/50' : '' }}">
                                        <td class="whitespace-nowrap px-4 py-3.5">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-sm font-bold ring-1 {{ $positionChipClass($index + 1) }}">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3.5 font-bold text-slate-900">
                                            {{ $p->team?->name ?? $p->student?->name }}
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3.5 font-bold text-emerald-700">
                                            {{ $p->score }} / {{ $session->activity->max_score }}
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3.5 text-slate-700">
                                            {{ $answeredCount }} / {{ $itemCount }}
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3.5 font-mono tabular-nums text-slate-700">
                                            {{ $p->elapsed_seconds !== null ? sprintf('%02d:%02d', intdiv((int) $p->elapsed_seconds, 60), (int) $p->elapsed_seconds % 60) : '—' }}
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3.5">
                                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

            @endif

        </section>

        {{-- Acciones --}}
        <section class="mb-8">
            <a href="{{ route('teacher.activities.show', $session->activity_id) }}"
               class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2 sm:w-auto">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                Ver la actividad
            </a>
        </section>

    </div>

@endsection
