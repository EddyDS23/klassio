<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado — {{ $activity->title }}</title>
    <style>
        a { text-decoration: none; }
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

        .top-nav { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
        .top-nav a {
            border: 1px solid #e2e8f0;
            border-radius: .6rem;
            padding: .55rem .9rem;
            font-weight: 700;
            font-size: 13px;
            color: #475569;
            background: #fff;
        }

        /* Modo kids: números y botones más grandes */
        body.student-kids .summary-card .value { font-size: 30px; }
        body.student-kids .summary-card .label { font-size: 14px; }
        body.student-kids .top-nav a { font-size: 15px; padding: .8rem 1.1rem; }
    </style>
</head>
<body class="student-kids">

<div class="top-nav">
    <a href="{{ route('student.activities.show', $activity->id) }}">🎮 ¡Jugar otra vez!</a>
    <a href="{{ route('student.class.show', $activity->class_id) }}">← Mi clase</a>
</div>

<h1>🎮 {{ $activity->title }}</h1>

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
        <div class="label">⭐ Puntos</div>
        <div class="value info">{{ $participation->score }} /  {{ $activity->max_score }}</div>
    </div>

    <div class="summary-card">
        <div class="label">⏱ Tiempo</div>
        <div class="value info">
            {{ $elapsed ?? '—' }}
        </div>
    </div>

    <div class="summary-card">
        <div class="label">✅ ¡Acertadas!</div>
        @php
            $correctCount = collect($answers)->where('is_correct', true)->count();
        @endphp
        <div class="value {{ $correctCount === $total && $total > 0 ? 'good' : ($correctCount > 0 ? 'info' : 'bad') }}">
            {{ $correctCount }} / {{ $total }}
        </div>
    </div>
</div>

{{-- Respuestas por tipo --}}
@if (count($answers) > 0)

    @if ($activity->type === 'crossword')
        <h2>?? Mi crucigrama</h2>
        <table>
            <thead>
                <tr>
                    <th>Pista</th>
                    <th>Lo que pusiste</th>
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
        <h2>?? Mi quiz</h2>
        <table>
            <thead>
                <tr>
                    <th>Pregunta</th>
                    <th>Lo que pusiste</th>
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
        <h2>?? Palabras que encontr�</h2>
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
        <h2>? Mis parejas</h2>
        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Lo que pusiste</th>
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
    <p class="empty">�Sin respuestas esta vez! Int�ntalo de nuevo. ??</p>
@endif

</body>
</html>
