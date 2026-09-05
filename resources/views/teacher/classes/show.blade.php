<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ $class->name }}</title>
</head>

<body>

    <a href="{{ route('teacher.classes.index') }}">
        ← Volver a mis clases
    </a>

    <h1>{{ $class->name }}</h1>

    <p>
        <strong>Descripción:</strong>
        {{ $class->description ?? 'Sin descripción' }}
    </p>

    <section>
        <h2>Código de acceso</h2>

        <p>
            Comparte este código con tus estudiantes para que puedan unirse:
        </p>

        <strong>{{ $class->code }}</strong>

        <form action="{{ route('teacher.classes.regenerate-code', $class->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <button type="submit">
                Regenerar código
            </button>
        </form>
    </section>

    <p>
        <strong>Estado:</strong>
        {{ $class->status }}
    </p>

    <p>
        <strong>Creada:</strong>
        {{ $class->created_at->format('d/m/Y') }}
    </p>

    <hr>

    <a href="{{ route('teacher.classes.students', $class->id) }}">
        Ver alumnos
    </a>

    <hr>

    <a href="{{ route('teacher.classes.edit', $class->id) }}">
        Editar clase
    </a>

    @if ($class->status === 'active')
        <form action="{{ route('teacher.classes.archive', $class->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('PATCH')

            <button type="submit">
                Archivar
            </button>
        </form>
    @else
        <form action="{{ route('teacher.classes.unarchive', $class->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('PATCH')

            <button type="submit">
                Desarchivar
            </button>
        </form>
    @endif

</body>

</html>
