@extends('student.layouts.app')

@section('title', 'Resultados — Sala '.$session->code)

@section('page-title', 'Resultados')

@section('page-actions')
    <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">

        <a href="{{ route('student.activities.show', $session->activity_id) }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-600 focus-visible:ring-offset-2">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Volver a la actividad
        </a>

        <a href="{{ route('student.dashboard') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-pink-600 focus-visible:ring-offset-2">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Ir al inicio
        </a>

    </div>
@endsection

@section('content')

    @php
        $isWinnerType = ($state['result_type'] ?? null) === 'winner';
        $isDraw = ($state['result_type'] ?? null) === 'draw';
        $isCollaborative = ($state['result_type'] ?? null) === 'collaborative_success';
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
            default => 'bg-slate-50 text-slate-600 ring-slate-200',
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

        {{-- Encabezado --}}
        <section class="mb-6 rounded-2xl bg-pink-700 p-4 text-white shadow-sm sm:p-6">

            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-pink-200 sm:text-xs">
                        Sala multijugador
                    </p>

                    <h2 class="mt-1 text-xl font-bold tracking-tight sm:text-2xl lg:text-3xl">
                        Resultados de la partida
                    </h2>

                    <p class="mt-2 flex items-start gap-1.5 text-xs font-bold text-pink-100 sm:text-sm">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $typeIcon }}" />
                        </svg>
                        <span class="min-w-0">{{ $gameLabel }} · {{ $session->activity->title }}</span>
                    </p>
                </div>

                <div class="flex shrink-0 flex-col items-end gap-1.5 sm:gap-2">
                    <span class="rounded-full bg-pink-600 px-2.5 py-1 font-mono text-xs font-bold tracking-[0.2em] text-white ring-1 ring-pink-500 sm:text-sm">
                        {{ $session->code }}
                    </span>

                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide bg-pink-100 text-pink-800 sm:text-xs">
                        {{ $resultLabel }}
                    </span>
                </div>

            </div>

        </section>

        {{-- Resultado destacado --}}
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
        @elseif ($isCollaborative)
            <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">
                    Resultado del equipo
                </p>

                <div class="mt-3 flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 ring-1 ring-amber-200">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                    </div>

                    <p class="min-w-0 text-lg font-bold text-slate-900">
                        Objetivo colaborativo cumplido
                    </p>
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

            @if ($session->activity->mode === 'individual')
                <p class="mt-1 text-xs text-slate-500">
                    En caso de igual puntaje, gana quien completó la partida en menos tiempo. Si también empatan en tiempo, se comparte el empate.
                </p>
            @endif

            @if ($players->isEmpty())
                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600" role="status">
                    Esta sala no registró participantes.
                </div>
            @else

                {{-- Tarjetas (móvil) --}}
                <div class="mt-4 grid gap-3 lg:hidden">

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

                        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $p->id === $winnerId ? 'ring-2 ring-amber-200' : '' }}">

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
                                    <p class="mt-0.5 text-xs font-bold text-pink-700">
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
                <div class="mt-4 hidden overflow-hidden rounded-2xl border border-slate-200 shadow-sm lg:block">

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

                                    <tr class="transition hover:bg-pink-50/40 {{ $p->id === $winnerId ? 'bg-amber-50/50' : '' }}">
                                        <td class="whitespace-nowrap px-4 py-3.5">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-sm font-bold ring-1 {{ $positionChipClass($index + 1) }}">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3.5 font-bold text-slate-900">
                                            {{ $p->team?->name ?? $p->student?->name }}
                                        </td>

                                        <td class="whitespace-nowrap px-4 py-3.5 font-bold text-pink-700">
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

        {{-- Tu resultado --}}
        @if ($myResult && isset($myResult['answers']))
            <section class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">

                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-pink-50 text-pink-700 ring-1 ring-pink-100">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h3 class="text-lg font-bold text-slate-900">
                            Tu resultado
                        </h3>

                        <p class="text-sm text-slate-600">
                            Detalle de tus {{ strtolower($progressLabel) }} en esta partida.
                        </p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2 sm:max-w-md">

                    <div class="rounded-lg bg-slate-50 px-3 py-2 ring-1 ring-slate-200">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $progressLabel }}</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-700">
                            {{ count($myResult['answers']) }} / {{ $myResult['total'] }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-slate-50 px-3 py-2 ring-1 ring-slate-200">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Puntaje</p>
                        <p class="mt-0.5 text-sm font-bold text-pink-700">
                            {{ $myResult['participation']->score }} / {{ $session->activity->max_score }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-slate-50 px-3 py-2 ring-1 ring-slate-200">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tiempo</p>
                        <p class="mt-0.5 text-sm font-bold text-slate-700">
                            {{ $myResult['elapsed'] ?? '—' }}
                        </p>
                    </div>

                </div>

                <div class="mt-4 grid gap-2.5 sm:grid-cols-2">

                    @forelse ($myResult['answers'] as $answer)
                        <div class="flex items-start gap-3 rounded-xl border p-3.5 {{ $answer['is_correct'] ? 'border-emerald-200 bg-emerald-50/60' : 'border-red-200 bg-red-50/60' }}">

                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $answer['is_correct'] ? 'bg-emerald-600' : 'bg-red-600' }} text-white">
                                @if ($answer['is_correct'])
                                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                @else
                                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                @endif
                            </span>

                            <div class="min-w-0 flex-1">
                                <p class="break-words text-sm font-bold text-slate-900">
                                    @if (isset($answer['word']))
                                        Palabra: {{ $answer['word'] }}
                                    @elseif (isset($answer['clue']))
                                        Pista: {{ $answer['clue'] }}
                                    @elseif (isset($answer['question']))
                                        {{ $answer['question'] }}
                                    @elseif (isset($answer['left'], $answer['right']))
                                        {{ $answer['left'] }}
                                        <svg class="mx-0.5 inline h-4 w-4 shrink-0 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                        </svg>
                                        {{ $answer['right'] }}
                                    @else
                                        Elemento del juego
                                    @endif
                                </p>

                                @if (isset($answer['response']))
                                    <p class="mt-0.5 break-words text-xs text-slate-600">
                                        Tu respuesta: {{ $answer['response'] }}
                                        @if (isset($answer['correct']) && !$answer['is_correct'])
                                            · Correcta: {{ $answer['correct'] }}
                                        @endif
                                    </p>
                                @endif

                                <div class="mt-2 flex items-center gap-1.5">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $answer['is_correct'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $answer['is_correct'] ? 'Correcta' : 'Incorrecta' }}
                                    </span>

                                    @if (isset($answer['score']))
                                        <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">
                                            {{ $answer['score'] }} pts
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @empty
                        <p class="col-span-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600" role="status">
                            No hay respuestas registradas para mostrar.
                        </p>
                    @endforelse

                </div>

            </section>
        @endif

    </div>

@endsection
