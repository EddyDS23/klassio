<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades</title>
</head>

<body>

    <h1>Actividades de {{ $class->name }}</h1>

    <form method="GET" action="{{ route('student.activities.index', $class->id) }}">

        <label for="search">
            Buscar:
        </label>

        <input type="text" name="search" id="search" value="{{ request('search') }}"
            placeholder="Nombre de la actividad">

        <label for="type">
            Tipo:
        </label>

        <select name="type" id="type">

            <option value="">Todas</option>

            <option value="kahoot" {{ request('type') === 'kahoot' ? 'selected' : '' }}>
                Kahoot
            </option>

            <option value="crossword" {{ request('type') === 'crossword' ? 'selected' : '' }}>
                Crucigrama
            </option>

            <option value="word_search" {{ request('type') === 'word_search' ? 'selected' : '' }}>
                Sopa de letras
            </option>

            <option value="matching" {{ request('type') === 'matching' ? 'selected' : '' }}>
                Unir conceptos
            </option>

        </select>

        <button type="submit">
            Buscar
        </button>

        @if (request()->filled('search') || request()->filled('type'))
            <a href="{{ route('student.activities.index', $class->id) }}">
                Limpiar
            </a>
        @endif

    </form>

    <hr>

    <p>
        Aquí puedes consultar y realizar las actividades disponibles de esta clase.
    </p>

    @if ($activities->isEmpty())

        <p>No hay actividades disponibles.</p>
    @else
        @foreach ($activities as $activity)
            <article>

                <h2>{{ $activity->title }}</h2>

                @if ($activity->description)
                    <p>
                        {{ $activity->description }}
                    </p>
                @endif

                <dl>
                    <dt>Tipo:</dt>
                    <dd>{{ $activity->type }}</dd>

                    <dt>Modo:</dt>
                    <dd>{{ $activity->mode }}</dd>

                    <dt>Puntuación máxima:</dt>
                    <dd>{{ $activity->max_score }}</dd>

                    @if ($activity->time_limit)
                        <dt>Tiempo límite:</dt>
                        <dd>{{ $activity->time_limit }} segundos</dd>
                    @endif

                    @if ($activity->due_at)
                        <dt>Fecha límite:</dt>
                        <dd>{{ $activity->due_at }}</dd>
                    @endif

                    <dt>Estado:</dt>
                    <dd>{{ $activity->status }}</dd>
                </dl>

                <a href="{{ route('student.activities.show', $activity->id) }}">
                    Ver actividad
                </a>

            </article>

            <hr>
        @endforeach

    @endif

    <nav>
        <a href="{{ route('student.class.show', $class->id) }}">
            Volver a la clase
        </a>
    </nav>

</body>

</html>
