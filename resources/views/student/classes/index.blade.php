<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis clases</title>
</head>

<body>

    <header>
        <h1>Mis clases</h1>

        <a href="{{ route('student.dashboard') }}">
            Volver al dashboard
        </a>
    </header>

    <main>

        @if (session('success'))
            <p>{{ session('success') }}</p>
        @endif

        @forelse ($classes as $class)

            <article>

                <h2>
                    {{ $class->name }}
                </h2>

                <p>
                    {{ $class->description }}
                </p>

                <p>
                    Estado: {{ $class->status }}
                </p>

                <a href="{{ route('student.class.show', $class->id) }}">
                    Ver clase
                </a>

            </article>

        @empty

            <p>
                No estás inscrito en ninguna clase.
            </p>

        @endforelse

    </main>

</body>

</html>