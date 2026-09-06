<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades</title>
</head>
<body>

    <h1>Actividades de {{ $class->name }}</h1>

    <p>
        <a href="{{ route('teacher.activities.create', $class->id) }}">
            Crear actividad
        </a>
    </p>

    @if(session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    @if($activities->isEmpty())

        <p>No hay actividades creadas.</p>

    @else

        @foreach($activities as $activity)

            <div>
                <h2>{{ $activity->title }}</h2>

                <p>
                    <strong>Tipo:</strong>
                    {{ $activity->type }}
                </p>

                <p>
                    <strong>Modo:</strong>
                    {{ $activity->mode }}
                </p>

                <p>
                    <strong>Puntuación máxima:</strong>
                    {{ $activity->max_score }}
                </p>

                <p>
                    <strong>Tiempo límite:</strong>
                    {{ $activity->time_limit }} segundos
                </p>

                <p>
                    <strong>Estado:</strong>
                    {{ $activity->status }}
                </p>

                @if($activity->due_at)
                    <p>
                        <strong>Fecha límite:</strong>
                        {{ $activity->due_at }}
                    </p>
                @endif

                <p>
                    <a href="{{ route('teacher.activities.show', $activity->id) }}">
                        Ver actividad
                    </a>
                </p>

                @if($activity->status === 'draft')

                    <p>
                        <a href="{{ route('teacher.activities.edit', $activity->id) }}">
                            Editar actividad
                        </a>
                    </p>

                    @if($activity->type === 'crossword')

                        @if($activity->crossword && $activity->crossword->words()->exists())

                            <p>
                                <a href="{{ route('teacher.crossword.edit', $activity->id) }}">
                                    Editar crucigrama
                                </a>
                            </p>

                        @else

                            <p>
                                <a href="{{ route('teacher.crossword.configure', $activity->id) }}">
                                    Configurar crucigrama
                                </a>
                            </p>

                        @endif

                    @endif

                @endif

            </div>

            <hr>

        @endforeach

    @endif

    <p>
        <a href="{{ route('teacher.classes.show', $class->id) }}">
            Volver a la clase
        </a>
    </p>

</body>
</html>