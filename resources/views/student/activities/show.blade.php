<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
    @include('partials.assets', ['theme' => 'student'])
</head>

<body class="student-activity student-kids">
    @include('partials.toast')

    <nav aria-label="Navegación de actividades" style="margin: 0 0 1rem;">
        <a href="{{ route('student.activities.index', $activity->class_id) }}" class="klassio-back-button">
            <span aria-hidden="true">←</span> Volver a actividades
        </a>
    </nav>

    <h1>🎮 {{ $activity->title }}</h1>

    @if ($activity->description)
        <p>{{ $activity->description }}</p>
    @endif

    @if (session('success'))
        <p data-toast="success" style="color: green;">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p data-toast="error" style="color: red;">{{ session('error') }}</p>
    @endif

    {{-- Información general --}}
    <section>
        <h2>Información</h2>

        <dl>
            <dt>Tipo:</dt>
            <dd>
                {{ match ($activity->type) {
                    'crossword' => 'Crucigrama',
                    'word_search' => 'Sopa de letras',
                    'matching' => 'Unir conceptos',
                    'kahoot' => 'Kahoot',
                    'roulette' => 'Ruleta',
                    default => $activity->type,
                } }}
            </dd>

            <dt>Modo:</dt>
            <dd>{{ $activity->mode === 'team' ? 'Por equipos' : 'Individual' }}</dd>

            <dt>Puntuación máxima:</dt>
            <dd>{{ $activity->max_score }} pts</dd>

            @if ($activity->time_limit)
                <dt>Tiempo límite:</dt>
                <dd>{{ gmdate('i:s', $activity->time_limit) }} min</dd>
            @endif

            @if ($activity->due_at)
                <dt>Fecha límite:</dt>
                <dd>{{ $activity->due_at->format('d/m/Y H:i') }}</dd>
            @endif

            <dt>Estado:</dt>
            <dd>{{ ucfirst($activity->status) }}</dd>
        </dl>
    </section>

    <hr>

    {{-- Detectar participación activa según modo individual o equipo --}}
    @php
        use Illuminate\Support\Facades\Route;

        $studentId = auth()->id();

        if ($activity->mode === 'team') {
            // Buscar el equipo del estudiante para esta actividad
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
                ->participations()
                ->where('student_id', $studentId)
                ->where('status', 'started')
                ->latest()
                ->first();

            $lastParticipation = $activity
                ->participations()
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

    <section>
        <h2>¡A jugar! 🎮</h2>

        @if ($activeParticipation)

            {{-- Intento activo: continuar, finalizar o abandonar --}}
            <p>¡Sigue jugando! (intento #{{ $activeParticipation->attempt }}).</p>

            @if ($playRouteExists)
                <a href="{{ route($playRoute, $activity->id) }}" @if ($soundGame) data-sound-game="{{ $soundGame }}" @endif>
                    Seguir jugando ▶
                </a>
            @else
                <p>El juego para esta actividad aún no está disponible.</p>
            @endif

            <form method="POST" action="{{ route('student.participation.finish', $activity->id) }}"
                style="display: inline;">
                @csrf
                <button type="submit">Terminar 🏁</button>
            </form>

            <form method="POST" action="{{ route('student.participation.abandon', $activity->id) }}"
                style="display: inline;">
                @csrf
                <button type="submit" onclick="return confirm('¿Seguro que quieres dejar el juego?')">
                    Dejar el juego 👋
                </button>
            </form>
        @elseif ($activity->due_at && now()->isAfter($activity->due_at))
            {{-- Actividad vencida --}}
            <p>Este juego ya terminó. ⏰</p>

            @if ($lastParticipation)
                <a href="{{ route('student.participation.result', $activity->id) }}">
                    Ver mi puntaje ⭐
                </a>
            @endif
        @elseif ($activity->mode === 'team' && !isset($myTeam))
            {{-- Sin equipo asignado --}}
            <p>
                Todavía no tienes equipo en este juego.
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
                    — {{ $lastParticipation->score }} pts ⭐
                </p>

                <a href="{{ route('student.participation.result', $activity->id) }}">
                    ¡Ver mi puntaje! ⭐
                </a>
            @endif

            @if ($playRouteExists)
                <form method="POST" action="{{ route('student.participation.start', $activity->id) }}">
                    @csrf
                    <button type="submit" @if ($soundGame) data-sound-game="{{ $soundGame }}" @endif>
                        {{ $lastParticipation ? '¡Otra vez! 🎮' : '¡A jugar! 🎮' }}
                    </button>
                </form>
            @else
                <p>Este juego aún no está disponible.</p>
            @endif

        @endif

    </section>

    <hr>

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
