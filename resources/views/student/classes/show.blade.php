<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $class->name }}</title>
</head>

<body>

    <header>

        <a href="{{ route('student.classes.index') }}">
            Volver a mis clases
        </a>

        <h1>{{ $class->name }}</h1>

    </header>

    <main>

        <section>

            <h2>Información de la clase</h2>

            <p>
                <strong>Descripción:</strong>
                {{ $class->description }}
            </p>

            <p>
                <strong>Maestro:</strong>
                {{ $class->teacher->name }}
            </p>

            <p>
                <strong>Estado:</strong>
                {{ $class->status }}
            </p>

            <a href="{{ route('student.class.students', $class->id) }}">
                Ver compañeros
            </a>

        </section>

        <section>
            <h2>Actividades</h2>
            @forelse ($activities as $activity)
                <article>
                    <h3>{{ $activity->title }}</h3>
                    <p> {{ $activity->description ?? 'Sin descripción' }} </p>
                    <p> <strong>Tipo:</strong> {{ $activity->type }} </p>
                    <p> <strong>Tiempo:</strong> {{ $activity->time_limit }} segundos </p>
                    @if ($activity->due_at)
                        <p> <strong>Fecha límite:</strong> {{ $activity->due_at->format('d/m/Y H:i') }} </p>
                    @endif <a href="{{ route('student.activities.show', $activity->id) }}"> Ver
                        actividad </a>
                </article>
            <hr> @empty <p> No hay actividades disponibles todavía. </p>
            @endforelse
        </section>

    </main>

</body>

</html>
