<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
    
    <!-- Fuentes Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome para Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @include('partials.assets', ['theme' => 'student'])

    <style>
        :root {
            --bg-main: #f4f7fe;
            --card-bg: #ffffff;
            --primary: #6c5ce7;
            --primary-hover: #5b4bc4;
            --success: #05cd99;
            --success-light: #e6f9f5;
            --warning: #ffb547;
            --warning-light: #fff8ec;
            --danger: #ee5d50;
            --danger-light: #fdecf0;
            --text-dark: #2b3674;
            --text-muted: #707ebe;
            --border: #e9edf7;
            --radius-lg: 20px;
            --radius-md: 14px;
            --shadow-md: 0px 10px 20px rgba(112, 144, 176, 0.07);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body.student-activity {
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

        /* Botón Volver */
        .klassio-back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            background-color: #ffffff;
            color: var(--text-muted);
            border: 1px solid var(--border);
            margin-bottom: 24px;
            transition: all 0.2s ease;
        }
        .klassio-back-button:hover {
            background-color: #f8fafc;
            color: var(--text-dark);
        }

        /* Mensajes de Alerta (Toasts) */
        .toast-msg {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .toast-success { background: var(--success-light); color: var(--success); border: 1px solid var(--success); }
        .toast-error { background: var(--danger-light); color: var(--danger); border: 1px solid var(--danger); }

        /* Tarjeta Principal */
        .activity-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        h1.activity-title {
            font-family: 'Fredoka', cursive;
            font-size: 28px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .badges-container {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }
        .badge-type { background: #eef2ff; color: #4318ff; }
        .badge-mode { background: #f4f0ff; color: #6c5ce7; }
        .badge-status { background: #f8fafc; color: var(--text-muted); border: 1px solid var(--border); }

        .activity-description {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        /* Grid de Info */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .info-card {
            background: #f8fafc;
            border-radius: var(--radius-md);
            padding: 14px;
            border: 1px solid var(--border);
        }

        .info-card dt {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-card dd {
            font-family: 'Fredoka', cursive;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* Área de Acción / Estado del Juego */
        .game-action-box {
            background: #fafbfc;
            border-radius: var(--radius-lg);
            padding: 24px;
            border: 2px dashed var(--border);
            text-align: center;
        }

        .game-action-box h2 {
            font-family: 'Fredoka', cursive;
            font-size: 22px;
            margin-bottom: 12px;
        }

        .game-action-box p {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 20px;
        }

        /* Botones de Acción */
        .btn-action-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-game {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: var(--radius-md);
            font-family: 'Fredoka', cursive;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-game-primary {
            background-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(108, 92, 231, 0.35);
        }
        .btn-game-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(108, 92, 231, 0.45);
        }

        .btn-game-success {
            background-color: var(--success);
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(5, 205, 153, 0.35);
        }
        .btn-game-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(5, 205, 153, 0.45);
        }

        .btn-game-secondary {
            background-color: #ffffff;
            color: var(--text-dark);
            border: 1px solid var(--border);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            padding: 12px 20px;
        }
        .btn-game-secondary:hover { background-color: #f8fafc; }

        .btn-game-danger {
            background-color: var(--danger-light);
            color: var(--danger);
            border: 1px solid var(--danger);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            padding: 12px 20px;
        }
        .btn-game-danger:hover { background-color: #fcdbd7; }

        @media (max-width: 600px) {
            .btn-action-group { flex-direction: column; width: 100%; }
            .btn-game { width: 100%; }
        }
    </style>
</head>

<body class="student-activity student-kids">
    @include('partials.toast')

    <div class="container">
        
        <nav aria-label="Navegación de actividades">
            <a href="{{ route('student.activities.index', $activity->class_id) }}" class="klassio-back-button">
                <i class="fa-solid fa-arrow-left"></i> Volver a actividades
            </a>
        </nav>

        {{-- Toasts / Notificaciones de sesión --}}
        @if (session('success'))
            <div class="toast-msg toast-success" data-toast="success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="toast-msg toast-error" data-toast="error">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <div class="activity-card">
            
            {{-- Encabezado e Insignias --}}
            <div class="activity-header">
                <h1 class="activity-title">
                    <i class="fa-solid fa-gamepad" style="color: #6c5ce7;"></i> {{ $activity->title }}
                </h1>
                
                <div class="badges-container">
                    <span class="badge badge-type">
                        <i class="fa-solid fa-puzzle-piece"></i>
                        {{ match ($activity->type) {
                            'crossword' => 'Crucigrama',
                            'word_search' => 'Sopa de letras',
                            'matching' => 'Unir conceptos',
                            'kahoot' => 'Kahoot',
                            'roulette' => 'Ruleta',
                            default => $activity->type,
                        } }}
                    </span>
                    <span class="badge badge-mode">
                        <i class="fa-solid {{ $activity->mode === 'team' ? 'fa-users' : 'fa-user' }}"></i>
                        {{ $activity->mode === 'team' ? 'Por equipos' : 'Individual' }}
                    </span>
                    <span class="badge badge-status">
                        {{ ucfirst($activity->status) }}
                    </span>
                </div>
            </div>

            @if ($activity->description)
                <p class="activity-description">{{ $activity->description }}</p>
            @endif

            {{-- Información General --}}
            <div class="info-grid">
                <div class="info-card">
                    <dt>Puntuación Max.</dt>
                    <dd>{{ $activity->max_score }} pts</dd>
                </div>

                @if ($activity->time_limit)
                    <div class="info-card">
                        <dt>Tiempo Límite</dt>
                        <dd>{{ gmdate('i:s', $activity->time_limit) }} min</dd>
                    </div>
                @endif

                @if ($activity->due_at)
                    <div class="info-card">
                        <dt>Fecha Límite</dt>
                        <dd>{{ $activity->due_at->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
            </div>

            {{-- Lógica de Backend original --}}
            @php
                use Illuminate\Support\Facades\Route;

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

                $playRouteExists = $playRoute && Route::has($playRoute);
            @endphp

            {{-- Sección Dinámica del Estado del Juego --}}
            <div class="game-action-box">
                <h2>¡A jugar! 🎮</h2>

                @if ($activeParticipation)
                    {{-- Intento activo: continuar, finalizar o abandonar --}}
                    <p>¡Tienes una partida en juego! (intento #{{ $activeParticipation->attempt }}).</p>

                    <div class="btn-action-group">
                        @if ($playRouteExists)
                            <a href="{{ route($playRoute, $activity->id) }}" class="btn-game btn-game-primary" @if ($soundGame) data-sound-game="{{ $soundGame }}" @endif>
                                <i class="fa-solid fa-play"></i> Seguir jugando ▶
                            </a>
                        @else
                            <p>El juego para esta actividad aún no está disponible.</p>
                        @endif

                        <form method="POST" action="{{ route('student.participation.finish', $activity->id) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-game btn-game-secondary">
                                Terminar 🏁
                            </button>
                        </form>

                        <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-game btn-game-danger" onclick="return confirm('¿Seguro que quieres dejar el juego?')">
                                Dejar el juego 👋
                            </button>
                        </form>
                    </div>

                @elseif ($activity->due_at && now()->isAfter($activity->due_at))
                    {{-- Actividad vencida --}}
                    <p>Este juego ya terminó. ⏰</p>

                    @if ($lastParticipation)
                        <div class="btn-action-group">
                            <a href="{{ route('student.participation.result', $activity->id) }}" class="btn-game btn-game-primary">
                                <i class="fa-solid fa-star"></i> Ver mi puntaje ⭐
                            </a>
                        </div>
                    @endif

                @elseif ($activity->mode === 'team' && !isset($myTeam))
                    {{-- Sin equipo asignado --}}
                    <p>
                        Todavía no tienes equipo en este juego. <br>
                        Pídele a tu maestro que te ponga en uno. 👥
                    </p>

                @else
                    {{-- Sin intento activo: mostrar historial y botón de inicio --}}
                    @if ($lastParticipation)
                        <p>
                            Última vez: #{{ $lastParticipation->attempt }} 
                            ({{ match ($lastParticipation->status) {
                                'completed' => '¡Lo lograste! 🎉',
                                'abandoned' => 'Lo dejaste',
                                'expired' => 'Se acabó el tiempo ⏰',
                                default => $lastParticipation->status,
                            } }})
                            — <strong>{{ $lastParticipation->score }} pts ⭐</strong>
                        </p>
                    @endif

                    <div class="btn-action-group">
                        @if ($lastParticipation)
                            <a href="{{ route('student.participation.result', $activity->id) }}" class="btn-game btn-game-secondary">
                                <i class="fa-solid fa-award"></i> ¡Ver mi puntaje! ⭐
                            </a>
                        @endif

                        @if ($playRouteExists)
                            <form method="POST" action="{{ route('student.participation.start', $activity->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-game btn-game-success" @if ($soundGame) data-sound-game="{{ $soundGame }}" @endif>
                                    {{ $lastParticipation ? '¡Otra vez! 🎮' : '¡A jugar! 🎮' }}
                                </button>
                            </form>
                        @else
                            <p>Este juego aún no está disponible.</p>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>

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
</body>
</html>