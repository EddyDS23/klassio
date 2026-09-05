<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades</title>
</head>
<body>

    <h1>Actividades de {{ $class->name }}</h1>

    @if($activities->isEmpty())
        <p>No hay actividades disponibles.</p>
    @else
        @foreach($activities as $activity)
            <div>
                <h2>{{ $activity->title }}</h2>

                <p>{{ $activity->description }}</p>

                <p>
                    Tipo: {{ $activity->type }}
                </p>

                <p>
                    Tiempo: {{ $activity->time_limit }} segundos
                </p>

                <a href="{{ route('student.activities.show', $activity->id) }}">
                    Ver actividad
                </a>
            </div>

            <hr>
        @endforeach
    @endif

</body>
</html>

