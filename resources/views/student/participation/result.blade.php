<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado — {{ $activity->title }}</title>
    
    <!-- Fuentes Google para estilo infantil/gamificado -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome para Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-main: #f4f7fe;
            --card-bg: #ffffff;
            --primary: #4318ff;
            --primary-light: #eef2ff;
            --success: #05cd99;
            --success-light: #e6f9f5;
            --warning: #ffb547;
            --warning-light: #fff8ec;
            --danger: #ee5d50;
            --danger-light: #fdecf0;
            --text-dark: #2b3674;
            --text-gray: #a3eedd;
            --text-muted: #707ebe;
            --border: #e9edf7;
            --radius-lg: 20px;
            --radius-md: 14px;
            --shadow-sm: 0px 4px 12px rgba(0, 0, 0, 0.03);
            --shadow-md: 0px 10px 20px rgba(112, 144, 176, 0.07);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
            padding: 32px 16px;
            min-height: 100vh;
        }

        .container {
            max-width: 760px;
            margin: 0 auto;
        }

        /* Navegación */
        .nav-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-nav.primary {
            background-color: #6c5ce7;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(108, 92, 231, 0.3);
        }
        .btn-nav.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 92, 231, 0.4);
        }

        .btn-nav.secondary {
            background-color: #ffffff;
            color: var(--text-muted);
            border: 1px solid var(--border);
        }
        .btn-nav.secondary:hover {
            background-color: #f8fafc;
            color: var(--text-dark);
        }

        /* Encabezado */
        .header-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
        }

        .header-title-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        h1.activity-title {
            font-family: 'Fredoka', cursive;
            font-size: 26px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .meta-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }
        .status-completed { background: var(--success-light); color: var(--success); }
        .status-abandoned { background: var(--warning-light); color: #d97706; }
        .status-expired   { background: var(--danger-light); color: var(--danger); }

        /* Grid de Resumen */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .summary-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        .summary-card .icon-wrapper {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px auto;
            font-size: 18px;
        }

        .summary-card.score .icon-wrapper { background: #eef2ff; color: #4318ff; }
        .summary-card.time .icon-wrapper { background: #fff8ec; color: #ffb547; }
        .summary-card.accuracy .icon-wrapper { background: #e6f9f5; color: #05cd99; }

        .summary-card .label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .summary-card .value {
            font-family: 'Fredoka', cursive;
            font-size: 26px;
            font-weight: 700;
        }

        .value.info { color: #4318ff; }
        .value.good { color: var(--success); }
        .value.bad  { color: var(--danger); }

        /* Sección de Tabla de Respuestas */
        .details-section {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
        }

        .details-section h2 {
            font-family: 'Fredoka', cursive;
            font-size: 20px;
            color: var(--text-dark);
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            text-align: left;
        }

        th {
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 700;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-dark);
            font-weight: 600;
        }

        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) { background-color: #fafbfc; }

        .badge-score {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 13px;
        }
        .badge-score.correct { background: var(--success-light); color: var(--success); }
        .badge-score.incorrect { background: var(--danger-light); color: var(--danger); }

        .empty-state {
            text-align: center;
            padding: 32px 16px;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 36px;
            margin-bottom: 8px;
            display: block;
            opacity: 0.5;
        }

        /* Ajustes específicos para Modo Kids */
        body.student-kids .summary-card .value { font-size: 28px; }
        body.student-kids .summary-card .label { font-size: 13px; }

        @media (max-width: 600px) {
            .summary-grid { grid-template-columns: 1fr; }
            .header-title-wrap { flex-direction: column; align-items: flex-start; }
            .nav-bar { flex-direction: column-reverse; gap: 10px; align-items: stretch; }
            .btn-nav { justify-content: center; }
        }
    </style>
</head>
<body class="student-kids">

<div class="container">

    {{-- Navegación --}}
    <nav class="nav-bar" aria-label="Navegación del resultado">
        <a href="{{ route('student.class.show', $activity->class_id) }}" class="btn-nav secondary">
            <i class="fa-solid fa-arrow-left"></i> Mi clase
        </a>
        <a href="{{ route('student.activities.show', $activity->id) }}" class="btn-nav primary">
            <i class="fa-solid fa-gamepad"></i> ¡Jugar otra vez!
        </a>
    </nav>

    {{-- Encabezado e Información --}}
    <div class="header-card">
        <div class="header-title-wrap">
            <h1 class="activity-title">
                <i class="fa-solid fa-trophy" style="color: #ffb547;"></i> {{ $activity->title }}
            </h1>
            <span class="status-badge status-{{ $participation->status }}">
                <i class="fa-solid {{ match($participation->status) {
                    'completed' => 'fa-circle-check',
                    'abandoned' => 'fa-circle-pause',
                    'expired'   => 'fa-clock',
                    default     => 'fa-circle-info',
                } }}"></i>
                {{ match($participation->status) {
                    'completed' => 'Completado',
                    'abandoned' => 'Abandonado',
                    'expired'   => 'Expirado',
                    default     => $participation->status,
                } }}
            </span>
        </div>
        <div class="meta-info">
            <span><i class="fa-solid fa-rotate-right"></i> Intento #{{ $participation->attempt }}</span>
        </div>
    </div>

    @php
        $correctCount = collect($answers)->where('is_correct', true)->count();
        $total = count($answers);
    @endphp

    {{-- Resumen Estadístico --}}
    <div class="summary-grid">
        <div class="summary-card score">
            <div class="icon-wrapper"><i class="fa-solid fa-star"></i></div>
            <div class="label">Puntaje</div>
            <div class="value info">{{ $participation->score }} / {{ $activity->max_score }}</div>
        </div>

        <div class="summary-card time">
            <div class="icon-wrapper"><i class="fa-solid fa-stopwatch"></i></div>
            <div class="label">Tiempo</div>
            <div class="value info">{{ $elapsed ?? '—' }}</div>
        </div>

        <div class="summary-card accuracy">
            <div class="icon-wrapper"><i class="fa-solid fa-bullseye"></i></div>
            <div class="label">Aciertos</div>
            <div class="value {{ $correctCount === $total && $total > 0 ? 'good' : ($correctCount > 0 ? 'info' : 'bad') }}">
                {{ $correctCount }} / {{ $total }}
            </div>
        </div>
    </div>

    {{-- Respuestas por Tipo de Actividad --}}
    <div class="details-section">
        @if (count($answers) > 0)

            @if ($activity->type === 'crossword')
                <h2><i class="fa-solid fa-border-all"></i> Respuestas del crucigrama</h2>
                <div class="table-container">
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
                                    <td>
                                        <span class="badge-score {{ $a['is_correct'] ? 'correct' : 'incorrect' }}">
                                            {{ $a['is_correct'] ? '+' . $a['score'] : '0' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif ($activity->type === 'kahoot')
                <h2><i class="fa-solid fa-bolt"></i> Respuestas del Quiz</h2>
                <div class="table-container">
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
                                    <td>
                                        <span class="badge-score {{ $a['is_correct'] ? 'correct' : 'incorrect' }}">
                                            <i class="fa-solid {{ $a['is_correct'] ? 'fa-check' : 'fa-xmark' }}"></i>
                                            {{ $a['response'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-score {{ $a['is_correct'] ? 'correct' : 'incorrect' }}">
                                            {{ $a['is_correct'] ? '+' . $a['score'] : '0' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif ($activity->type === 'word_search')
                <h2><i class="fa-solid fa-magnifying-glass"></i> Palabras encontradas</h2>
                <div class="table-container">
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
                                    <td>
                                        <span class="badge-score correct">
                                            <i class="fa-solid fa-check"></i> {{ $a['word'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-score correct">+{{ $a['score'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif ($activity->type === 'matching')
                <h2><i class="fa-solid fa-link"></i> Respuestas de Unir conceptos</h2>
                <div class="table-container">
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
                                    <td>
                                        <span class="badge-score {{ $a['is_correct'] ? 'correct' : 'incorrect' }}">
                                            {{ $a['is_correct'] ? '+' . $a['score'] : '0' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif ($activity->type === 'roulette')
                <h2><i class="fa-solid fa-arrows-spin"></i> Respuestas de la Ruleta</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Pregunta</th>
                                <th>Tu respuesta</th>
                                <th>Correcto</th>
                                <th>Pts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($answers as $a)
                                <tr>
                                    <td>{{ $a['question'] }}</td>
                                    <td>{{ $a['response'] }}</td>
                                    <td>{{ $a['correct'] }}</td>
                                    <td>
                                        <span class="badge-score {{ $a['is_correct'] ? 'correct' : 'incorrect' }}">
                                            {{ $a['is_correct'] ? '+' . $a['score'] : '0' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        @else
            <div class="empty-state">
                <i class="fa-solid fa-inbox"></i>
                <p>No hay respuestas registradas para este intento.</p>
            </div>
        @endif
    </div>

</div>

</body>
</html>