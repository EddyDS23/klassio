<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $class->name }}</title>
</head>

<body>

    <header>

        <nav>
            <a href="{{ route('student.classes.index') }}">
                Volver a mis clases
            </a>
        </nav>

        <h1>{{ $class->name }}</h1>

    </header>

    <main>

        <section>

            <h2>Información de la clase</h2>

            @if($class->description)
                <p>
                    <strong>Descripción:</strong>
                    {{ $class->description }}
                </p>
            @else
                <p>
                    <strong>Descripción:</strong>
                    Sin descripción.
                </p>
            @endif

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

        <hr>

        <section>

            <h2>Actividades</h2>

            @forelse($activities as $activity)

                <article>

                    <h3>{{ $activity->title }}</h3>

                    @if($activity->description)
                        <p>
                            {{ $activity->description }}
                        </p>
                    @else
                        <p>
                            Sin descripción.
                        </p>
                    @endif

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
                            <dd>
                                {{ $activity->due_at->format('d/m/Y H:i') }}
                            </dd>
                        @endif

                    </dl>

                    <a href="{{ route('student.activities.show', $activity->id) }}">
                        Ver actividad
                    </a>

                </article>

                <hr>

            @empty

                <p>
                    No hay actividades disponibles todavía.
                </p>

            @endforelse

        </section>

    </main>

</body>

</html>

