<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
</head>
<body>

    <h1>{{ $activity->title }}</h1>

    @if ($activity->description)
        <p>{{ $activity->description }}</p>
    @endif

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    {{-- Información general --}}
    <section>
        <h2>Información</h2>

        <dl>
            <dt>Tipo:</dt>
            <dd>
                {{ match($activity->type) {
                    'crossword'   => 'Crucigrama',
                    'word_search' => 'Sopa de letras',
                    'matching'    => 'Unir conceptos',
                    'kahoot'      => 'Kahoot',
                    default       => $activity->type,
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

    {{-- Participación activa --}}
    @php
        $activeParticipation = $activity->participations()
            ->where('student_id', auth()->id())
            ->where('status', 'started')
            ->latest()
            ->first();

        $lastParticipation = $activity->participations()
            ->where('student_id', auth()->id())
            ->whereIn('status', ['completed', 'abandoned', 'expired'])
            ->latest()
            ->first();
    @endphp

    <section>
        <h2>Acciones</h2>

        @if ($activeParticipation)

            {{-- Ya tiene un intento activo: llevar al juego o permitir abandonar --}}
            <p>Tienes un intento en curso (intento #{{ $activeParticipation->attempt }}).</p>

            @php
                $playRoute = match($activity->type) {
                    'crossword'   => 'student.crossword.play',
                    'kahoot'      => 'student.kahoot.play',
                    'word_search' => 'student.wordsearch.play',
                    'matching'    => 'student.matching.play',
                    default       => null,
                };
            @endphp

            @if ($playRoute && \Illuminate\Support\Facades\Route::has($playRoute))
                <a href="{{ route($playRoute, $activity->id) }}">
                    Continuar actividad
                </a>
            @else
                <p>El juego para esta actividad aún no está disponible.</p>
            @endif

            <form method="POST"
                  action="{{ route('student.participation.finish', $activity->id) }}"
                  style="display:inline;">
                @csrf
                <button type="submit">Finalizar actividad</button>
            </form>

            <form method="POST"
                  action="{{ route('student.participation.abandon', $activity->id) }}"
                  style="display:inline;">
                @csrf
                <button type="submit"
                        onclick="return confirm('¿Estás seguro de que quieres abandonar?')">
                    Abandonar
                </button>
            </form>

        @elseif ($activity->due_at && now()->isAfter($activity->due_at))

            {{-- Actividad vencida --}}
            <p>La fecha límite de esta actividad ya pasó.</p>

            @if ($lastParticipation)
                <a href="{{ route('student.participation.result', $activity->id) }}">
                    Ver mi resultado
                </a>
            @endif

        @else

            {{-- Sin intento activo: mostrar botón de inicio --}}
            @if ($lastParticipation)
                <p>
                    Último intento: #{{ $lastParticipation->attempt }}
                    ({{ match($lastParticipation->status) {
                        'completed' => 'Completado',
                        'abandoned' => 'Abandonado',
                        'expired'   => 'Expirado',
                        default     => $lastParticipation->status,
                    } }})
                    — {{ $lastParticipation->score }} pts
                </p>

                <a href="{{ route('student.participation.result', $activity->id) }}">
                    Ver resultado
                </a>
            @endif

            <form method="POST"
                  action="{{ route('student.participation.start', $activity->id) }}">
                @csrf
                <button type="submit">
                    {{ $lastParticipation ? 'Intentar de nuevo' : 'Iniciar actividad' }}
                </button>
            </form>

        @endif

    </section>

    <hr>

    <nav>
        <a href="{{ route('student.activities.index', $activity->class_id) }}">
            Volver a actividades
        </a>
    </nav>

</body>
</html>