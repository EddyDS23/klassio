<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
</head>
<body>

    <h1>{{ $activity->title }}</h1>

    <p>
        {{ $activity->description }}
    </p>

    <p>
        Tipo: {{ $activity->type }}
    </p>

    <p>
        Modo: {{ $activity->mode }}
    </p>

    <p>
        Puntuación máxima: {{ $activity->max_score }}
    </p>

    <p>
        Tiempo límite: {{ $activity->time_limit }} segundos
    </p>

    @if($activity->due_at)
        <p>
            Fecha límite: {{ $activity->due_at }}
        </p>
    @endif

    <p>
        Aquí comenzará el juego.
    </p>

    <a href="{{ route('student.activities.index', $activity->class_id) }}">
        Volver
    </a>

</body>
</html>

