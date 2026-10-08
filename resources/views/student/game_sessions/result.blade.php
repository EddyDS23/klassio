<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados — Sala {{ $session->code }}</title>
    @include('partials.assets', ['theme' => 'student'])
</head>

<body>

    <div class="page-shell">

        <div class="page-hero text-center">
            <h1 class="fw-bold mb-1">Resultados de la partida</h1>
            <p class="mb-0 opacity-75">{{ $session->activity->title }} — Sala {{ $session->code }}</p>
        </div>

        @php
            $winnerId = $state['winner_participation_id'] ?? null;
            $resultLabel = match ($state['result_type'] ?? null) {
                'winner' => 'Ganador',
                'draw' => 'Empate',
                'collaborative_success' => 'Objetivo colaborativo cumplido',
                default => 'Finalizada',
            };
        @endphp

        <div class="card surface-card mb-4">
            <div class="card-body p-4">
                <span class="badge text-bg-warning text-uppercase">{{ $resultLabel }}</span>

                @if ($winnerId)
                    @php($winner = $players->firstWhere('id', $winnerId))
                    <h2 class="mt-3 mb-0">
                        🏆 {{ $winner?->student?->name ?? 'Jugador' }}
                    </h2>
                @elseif ($state['result_type'] === 'draw')
                    <h2 class="mt-3 mb-0">🤝 Empate entre los mejores</h2>
                @endif
            </div>
        </div>

        <div class="card surface-card mb-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">Clasificación</h2>
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jugador</th>
                            <th>Puntaje</th>
                            <th>Respondidas</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($players as $index => $p)
                            <tr class="{{ $p->id === $winnerId ? 'table-warning' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $p->student?->name }}
                                    @if ($p->id === $winnerId && $session->result_type === 'winner')
                                        <span class="text-warning">★</span>
                                    @endif
                                </td>
                                <td>{{ $p->score }} / {{ $session->activity->max_score }}</td>
                                <td>{{ $p->rouletteAnswers()->count() }}</td>
                                <td>{{ ucfirst($p->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($myResult && isset($myResult['answers']))
            <div class="card surface-card mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Mi resultado</h2>
                    <p>
                        Puntaje: <strong>{{ $myResult['participation']->score }} / {{ $session->activity->max_score }}</strong>
                        — Tiempo: {{ $myResult['elapsed'] }}
                    </p>
                    <ul class="list-group list-group-flush">
                        @foreach ($myResult['answers'] as $answer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    {{ $answer['question'] ?? 'Pregunta' }}
                                </span>
                                <span class="badge {{ $answer['is_correct'] ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ $answer['is_correct'] ? 'Correcta' : 'Incorrecta' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('student.activities.show', $session->activity_id) }}" class="btn btn-klassio">
                Volver a la actividad
            </a>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                Ir al inicio
            </a>
        </div>

    </div>

</body>

</html>