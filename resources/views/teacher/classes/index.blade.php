
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis clases</title>
</head>
<body>

    <h1>Mis clases</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <a href="{{ route('teacher.classes.create') }}">
        Crear clase
    </a>

    <hr>

    @forelse ($classes as $class)

        <article>
            <h2>{{ $class->name }}</h2>

            <p>
                {{ $class->description ?? 'Sin descripción' }}
            </p>

            <p>
                <strong>Código:</strong> {{ $class->code }}
            </p>

            <p>
                <strong>Estado:</strong> {{ $class->status }}
            </p>

            <a href="{{ route('teacher.classes.show', $class->id) }}">
                Ver clase
            </a>

            @if ($class->status === 'active')

                <a href="{{ route('teacher.classes.edit', $class->id) }}">
                    Editar
                </a>

                <form action="{{ route('teacher.classes.archive', $class->id) }}"
                      method="POST"
                      style="display:inline;">
                    @csrf
                    @method('PATCH')

                    <button type="submit">
                        Archivar
                    </button>
                </form>

            @else

                <form action="{{ route('teacher.classes.unarchive', $class->id) }}"
                      method="POST"
                      style="display:inline;">
                    @csrf
                    @method('PATCH')

                    <button type="submit">
                        Desarchivar
                    </button>
                </form>

            @endif

        </article>

        <hr>

    @empty

        <p>No tienes clases creadas.</p>

    @endforelse

</body>
</html>

