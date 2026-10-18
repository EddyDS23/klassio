@extends('teacher.layouts.session')

@section('title', 'Resultados — Sala {{ $session->code }}')

@section('head')
<style>
    .session-results { max-width: 1120px; margin-inline: auto; }
    .session-results .page-hero { margin-bottom: 1.4rem; }
    .result-summary, .result-ranking { overflow: hidden; border: 1px solid #d7e8e4; border-radius: 20px; box-shadow: 0 14px 38px rgb(23 69 60 / .08); }
    .result-outcome { display: inline-flex; padding: .5rem .8rem; border-radius: 999px; background: #e9f6ed; color: #287243; font-weight: 800; }
    .result-card-body { padding: 1.5rem; }
    .result-table { min-width: 680px; vertical-align: middle; }
    .result-table th { padding: .9rem .75rem; border-bottom: 1px solid #d7e8e4; color: #587371; font-size: .77rem; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }
    .result-table td { padding: .9rem .75rem; border-bottom: 1px solid #e5efeb; }
    .result-table tbody tr:nth-child(even) { background: #f8fbf9; }
    .result-table tbody tr:hover { background: #effbf5; }
    .result-place { display: inline-grid; width: 30px; height: 30px; place-items: center; border-radius: 50%; background: #eff6f2; color: #365f54; font-weight: 800; }
    .result-actions a { min-height: 44px; }
    @media (max-width: 575px) { .session-results .page-hero { text-align: left !important; } .result-card-body { padding: 1rem; } }
</style>
@endsection

@section('content')
    <div class="page-shell session-results">

        <div class="page-hero text-center">
            <h1 class="font-bold mb-1">Resultados de la partida</h1>
            <p class="mb-0 opacity-75">{{ $session->activity->title }} — Sala {{ $session->code }}</p>
        </div>

        @php
            $winnerId = $state['winner_participation_id'] ?? null;
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
                'winner' => 'Ganador',
                'draw' => 'Empate',
                'collaborative_success' => 'Objetivo colaborativo cumplido',
                default => 'Finalizada',
            };
        @endphp

        <div class="surface-card result-summary mb-4">
            <div class="result-card-body">
                <p class="text-sm uppercase font-bold text-slate-500 mb-2">{{ $gameLabel }} · Sala {{ $session->code }}</p>
                <span class="result-outcome">{{ $resultLabel }}</span>

                @if ($winnerId)
                    @php
                        $winner = $players->firstWhere('id', $winnerId);
                    @endphp
                    <h2 class="mt-3 mb-0">
                        🏆 {{ $winner?->team?->name ?? $winner?->student?->name ?? 'Jugador' }}
                    </h2>
                @elseif ($state['result_type'] === 'draw')
                    <h2 class="mt-3 mb-0">🤝 Empate entre los mejores</h2>
                @endif
            </div>
        </div>

        <div class="surface-card result-ranking mb-4">
            <div class="result-card-body">
                <h2 class="text-lg font-bold mb-3">Clasificación</h2>
                <div class="overflow-x-auto">
                <table class="result-table w-full min-w-[680px] border-collapse text-left text-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jugador</th>
                            <th>Puntaje</th>
                            <th>{{ $progressLabel }}</th>
                            <th>Tiempo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
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
                            @endphp
                            <tr class="{{ $p->id === $winnerId ? 'bg-amber-50' : '' }}">
                                <td><span class="result-place">{{ $index + 1 }}</span></td>
                                <td>
                                    {{ $p->team?->name ?? $p->student?->name }}
                                    @if ($p->id === $winnerId && $session->result_type === 'winner')
                                        <span class="text-amber-600">★</span>
                                    @endif
                                </td>
                                <td>{{ $p->score }} / {{ $session->activity->max_score }}</td>
                                <td>{{ $answeredCount }} / {{ $itemCount }}</td>
                                <td>{{ $p->elapsed_seconds !== null ? sprintf('%02d:%02d', intdiv((int) $p->elapsed_seconds, 60), (int) $p->elapsed_seconds % 60) : '—' }}</td>
                                <td><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $p->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">{{ match ($p->status) {
                                    'completed' => 'Completó',
                                    'started' => 'En curso',
                                    'abandoned' => 'Abandonó',
                                    'expired' => 'Tiempo agotado',
                                    default => ucfirst($p->status),
                                } }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 result-actions">
            <a href="{{ route('teacher.activities.show', $session->activity_id) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-cyan-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-cyan-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700 disabled:cursor-not-allowed disabled:opacity-50">
                Ver la actividad
            </a>
            <a href="{{ route('teacher.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700">
                Volver al panel
            </a>
        </div>

    </div>
@endsection
