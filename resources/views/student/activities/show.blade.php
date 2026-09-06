<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
</head>

<body>

    <h1>{{ $activity->title }}</h1>

    @if($activity->description)
        <p>
            {{ $activity->description }}
        </p>
    @endif

    <section>
        <h2>Información de la actividad</h2>

        <dl>
            <dt>Tipo:</dt>
            <dd>{{ $activity->type }}</dd>

            <dt>Modo:</dt>
            <dd>{{ $activity->mode }}</dd>

            <dt>Puntuación máxima:</dt>
            <dd>{{ $activity->max_score }}</dd>

            @if($activity->time_limit)
                <dt>Tiempo límite:</dt>
                <dd>{{ $activity->time_limit }} segundos</dd>
            @endif

            @if($activity->due_at)
                <dt>Fecha límite:</dt>
                <dd>{{ $activity->due_at }}</dd>
            @endif

            <dt>Estado:</dt>
            <dd>{{ $activity->status }}</dd>
        </dl>
    </section>

    <hr>

    <section>
        <h2>Acciones</h2>

        @if($activity->type === 'crossword')

            <a href="{{ route('student.crossword.play', $activity->id) }}">
                Iniciar crucigrama
            </a>

        @else

            <p>
                Esta actividad todavía no tiene un juego disponible.
            </p>

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

