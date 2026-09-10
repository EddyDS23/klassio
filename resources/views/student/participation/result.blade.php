<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado — {{ $activity->title }}</title>
    <style>
        body { font-family: sans-serif; padding: 24px; max-width: 680px; margin: 0 auto; }
        h1 { margin-bottom: 4px; }
        .meta { color: #555; font-size: 14px; margin-bottom: 24px; }
        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 28px;
        }
        .summary-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
            text-align: center;
        }
        .summary-card .label { font-size: 12px; color: #888; margin-bottom: 4px; }
        .summary-card .value { font-size: 24px; font-weight: bold; }
        .value.good  { color: #16a34a; }
        .value.bad   { color: #dc2626; }
        .value.info  { color: #2563eb; }

        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; padding: 8px 10px; background: #f3f4f6; border-bottom: 2px solid #e5e7eb; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        tr:last-child td { border-bottom: none; }

        .correct   { color: #16a34a; font-weight: bold; }
        .incorrect { color: #dc2626; font-weight: bold; }

        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-abandoned { background: #fef9c3; color: #854d0e; }
        .status-expired   { background: #fee2e2; color: #991b1b; }

        .nav { margin-top: 28px; }
        .nav a { color: #2563eb; text-decoration: none; margin-right: 16px; font-size: 14px; }
        .nav a:hover { text-decoration: underline; }

        .empty { color: #888; font-size: 14px; margin-top: 12px; }
    </style>
</head>
<body>

<h1>{{ $activity->title }}</h1>

<div class="meta">
    Intento #{{ $participation->attempt }} ·
    <span class="status-badge status-{{ $participation->status }}">
        {{ match($participation->status) {
            'completed' => 'Completado',
            'abandoned' => 'Abandonado',
            'expired'   => 'Expirado',
            default     => $participation->status,
        } }}
    </span>
</div>

{{-- Resumen --}}
<div class="summary">
    <div class="summary-card">
        <div class="label">Puntaje</div>
        <div class="value info">{{ $participation->score }}</div>
    </div>

    <div class="summary-card">
        <div class="label">Tiempo</div>
        <div class="value info">
            {{ $elapsed ?? '—' }}
        </div>
    </div>

    <div class="summary-card">
        <div class="label">Respuestas correctas</div>
        @php
            $correctCount = collect($answers)->where('is_correct', true)->count();
            $total        = count($answers);
        @endphp
        <div class="value {{ $correctCount === $total && $total > 0 ? 'good' : ($correctCount > 0 ? 'info' : 'bad') }}">
            {{ $correctCount }} / {{ $total }}
        </div>
    </div>
</div>

{{-- Respuestas por tipo --}}
@if (count($answers) > 0)

    @if ($activity->type === 'crossword')
        <h2>Respuestas del crucigrama</h2>
        <table>
            <thead>
                <tr>
                    <th>Pista</th>
                    <th>Tu respuesta</th>
                    <th>Correcta</th>
                    <th>Pts</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($answers as $a)
                    <tr>
                        <td>{{ $a['clue'] }}</td>
                        <td>{{ $a['response'] }}</td>
                        <td>{{ $a['correct'] }}</td>
                        <td class="{{ $a['is_correct'] ? 'correct' : 'incorrect' }}">
                            {{ $a['is_correct'] ? '+' . $a['score'] : '0' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif ($activity->type === 'kahoot')
        <h2>Respuestas del Kahoot</h2>
        <table>
            <thead>
                <tr>
                    <th>Pregunta</th>
                    <th>Tu respuesta</th>
                    <th>Pts</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($answers as $a)
                    <tr>
                        <td>{{ $a['question'] }}</td>
                        <td class="{{ $a['is_correct'] ? 'correct' : 'incorrect' }}">
                            {{ $a['is_correct'] ? '✓' : '✗' }} {{ $a['response'] }}
                        </td>
                        <td class="{{ $a['is_correct'] ? 'correct' : 'incorrect' }}">
                            {{ $a['is_correct'] ? '+' . $a['score'] : '0' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif ($activity->type === 'word_search')
        <h2>Palabras encontradas</h2>
        <table>
            <thead>
                <tr>
                    <th>Palabra</th>
                    <th>Pts</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($answers as $a)
                    <tr>
                        <td class="correct">✓ {{ $a['word'] }}</td>
                        <td class="correct">+{{ $a['score'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif ($activity->type === 'matching')
        <h2>Respuestas de Unir conceptos</h2>
        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Tu respuesta</th>
                    <th>Correcto</th>
                    <th>Pts</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($answers as $a)
                    <tr>
                        <td>{{ $a['left'] }}</td>
                        <td>{{ $a['response'] }}</td>
                        <td>{{ $a['right'] }}</td>
                        <td class="{{ $a['is_correct'] ? 'correct' : 'incorrect' }}">
                            {{ $a['is_correct'] ? '+' . $a['score'] : '0' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

@else
    <p class="empty">No hay respuestas registradas para este intento.</p>
@endif

<div class="nav">
    <a href="{{ route('student.activities.show', $activity->id) }}">← Volver a la actividad</a>
    <a href="{{ route('student.class.show', $activity->class_id) }}">Volver a la clase</a>
</div>

</body>
</html>