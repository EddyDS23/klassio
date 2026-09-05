
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades</title>
</head>
<body>

    <h1>Actividades de {{ $class->name }}</h1>

    <a href="{{ route('teacher.activities.create', $class->id) }}">
        Crear actividad
    </a>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if($activities->isEmpty())
        <p>No hay actividades creadas.</p>
    @else
        @foreach($activities as $activity)
            <div>
                <h2>{{ $activity->title }}</h2>

                <p>Tipo: {{ $activity->type }}</p>
                <p>Modo: {{ $activity->mode }}</p>
                <p>Estado: {{ $activity->status }}</p>

                <a href="{{ route('teacher.activities.show', $activity->id) }}">
                    Ver
                </a>

                @if($activity->status === 'draft')
                    <a href="{{ route('teacher.activities.edit', $activity->id) }}">
                        Editar
                    </a>
                @endif
            </div>

            <hr>
        @endforeach
    @endif

</body>
</html>

