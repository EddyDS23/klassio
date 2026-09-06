
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

        <p>
            Clases en las que estás inscrito.
        </p>

        <nav>
            <a href="{{ route('student.dashboard') }}">
                Volver al dashboard
            </a>
        </nav>

    </header>

    <main>

        @if(session('success'))
            <p>
                {{ session('success') }}
            </p>
        @endif

        @if(session('error'))
            <p>
                {{ session('error') }}
            </p>
        @endif

        @forelse ($classes as $class)

            <article>

                <h2>
                    {{ $class->name }}
                </h2>

                @if($class->description)
                    <p>
                        {{ $class->description }}
                    </p>
                @else
                    <p>
                        Sin descripción.
                    </p>
                @endif

                <dl>

                    <dt>Estado:</dt>
                    <dd>{{ $class->status }}</dd>

                </dl>

                <a href="{{ route('student.class.show', $class->id) }}">
                    Ver clase
                </a>

            </article>

            <hr>

        @empty

            <p>
                No estás inscrito en ninguna clase.
            </p>

            <p>
                Puedes unirte a una clase utilizando el código
                proporcionado por tu maestro.
            </p>

            <a href="{{ route('student.dashboard') }}">
                Ir al dashboard
            </a>

        @endforelse

    </main>

</body>

</html>