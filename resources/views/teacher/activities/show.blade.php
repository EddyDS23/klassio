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

    <p>
        Estado: {{ $activity->status }}
    </p>

    @if($activity->due_at)
        <p>
            Fecha límite: {{ $activity->due_at }}
        </p>
    @endif

    @if($activity->status === 'draft')
        <a href="{{ route('teacher.activities.edit', $activity->id) }}">
            Editar
        </a>

        <form
            action="{{ route('teacher.activities.publish', $activity->id) }}"
            method="POST"
        >
            @csrf

            <button type="submit">
                Publicar
            </button>
        </form>
    @endif

    @if($activity->status === 'published')
        <form
            action="{{ route('teacher.activities.close', $activity->id) }}"
            method="POST"
        >
            @csrf

            <button type="submit">
                Cerrar actividad
            </button>
        </form>
    @endif

    <br>

    <a href="{{ route('teacher.activities.index', $activity->class_id) }}">
        Volver
    </a>

</body>
</html>

