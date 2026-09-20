<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado — {{ $activity->title }}</title>
    <script>
        try {
            if (localStorage.getItem('klassio-theme') === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        } catch (e) {}
    </script>
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

        .klassio-theme-btn {
            border: 1px solid #cbd5e1;
            border-radius: .6rem;
            background: #fff;
            color: #475569;
            padding: .5rem .8rem;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            float: right;
        }

        /* Negro solo si se activa (respeta el modo elegido en el resto de Klassio) */
        html[data-theme="dark"] { color-scheme: dark; }
        html[data-theme="dark"] body { background: #0f172a; color: #e2e8f0; }
        html[data-theme="dark"] h1, html[data-theme="dark"] h2 { color: #f1f5f9; }
        html[data-theme="dark"] .meta { color: #94a3b8; }
        html[data-theme="dark"] .summary-card { background: #1e293b; border-color: #334155; }
        html[data-theme="dark"] .summary-card .label { color: #94a3b8; }
        html[data-theme="dark"] .value.good, html[data-theme="dark"] .correct { color: #4ade80; }
        html[data-theme="dark"] .value.bad, html[data-theme="dark"] .incorrect { color: #f87171; }
        html[data-theme="dark"] .value.info { color: #7dd3fc; }
        html[data-theme="dark"] th { background: #1e293b; border-color: #334155; color: #94a3b8; }
        html[data-theme="dark"] td { border-color: #1e293b; }
        html[data-theme="dark"] .status-completed { background: #052e16; color: #4ade80; }
        html[data-theme="dark"] .status-abandoned { background: #451a03; color: #fbbf24; }
        html[data-theme="dark"] .status-expired { background: #450a0a; color: #f87171; }
        html[data-theme="dark"] .nav a { color: #7dd3fc; }
        html[data-theme="dark"] .top-nav a { background: #1e293b; border-color: #334155; color: #7dd3fc; }
        html[data-theme="dark"] .empty { color: #64748b; }
        html[data-theme="dark"] .klassio-theme-btn { background: #1e293b; color: #f1f5f9; border-color: #475569; }
    </style>
</head>
<body>

<button type="button" id="theme-btn" class="klassio-theme-btn">🌙 Negro</button>
<script>
    (function () {
        var btn = document.getElementById('theme-btn');
        function label() {
            return document.documentElement.getAttribute('data-theme') === 'dark' ? '☀️ Claro' : '🌙 Negro';
        }
        btn.textContent = label();
        btn.addEventListener('click', function () {
            var dark = document.documentElement.getAttribute('data-theme') !== 'dark';
            if (dark) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
            }
            try { localStorage.setItem('klassio-theme', dark ? 'dark' : 'light'); } catch (e) {}
            btn.textContent = label();
        });
    })();
</script>

<div class="top-nav">
    <a href="{{ route('student.activities.show', $activity->id) }}">← Volver a la actividad</a>
    <a href="{{ route('student.class.show', $activity->class_id) }}">Volver a la clase</a>
</div>

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
        <div class="value info">{{ $participation->score }} /  {{ $activity->max_score }}</div>
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

</body>
</html>
