
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
            <a href="{{ route('teacher.classes.index') }}">
                Volver a mis clases
            </a>
        </nav>

        <h1>{{ $class->name }}</h1>

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
                <strong>Estado:</strong>
                {{ $class->status }}
            </p>

            <p>
                <strong>Fecha de creación:</strong>
                {{ $class->created_at->format('d/m/Y') }}
            </p>

        </section>

        <hr>

        <section>

            <h2>Código de acceso</h2>

            <p>
                Comparte este código con tus estudiantes para que puedan
                unirse a la clase:
            </p>

            <p>
                <strong>{{ $class->code }}</strong>
            </p>

            <form
                action="{{ route('teacher.classes.regenerate-code', $class->id) }}"
                method="POST"
            >
                @csrf
                @method('PATCH')

                <button type="submit">
                    Regenerar código
                </button>
            </form>

        </section>

        <hr>

        <section>

            <h2>Alumnos</h2>

            <p>
                Consulta y administra los alumnos inscritos en esta clase.
            </p>

            <a href="{{ route('teacher.classes.students', $class->id) }}">
                Ver alumnos
            </a>

        </section>

        <hr>

        <section>

            <h2>Actividades</h2>

            <p>
                Crea y administra las actividades de esta clase.
            </p>

            <a href="{{ route('teacher.activities.index', $class->id) }}">
                Ver actividades
            </a>

            <br>

            <a href="{{ route('teacher.activities.create', $class->id) }}">
                Crear actividad
            </a>

        </section>

        <hr>

        <section>

            <h2>Administrar clase</h2>

            <a href="{{ route('teacher.classes.edit', $class->id) }}">
                Editar clase
            </a>

            @if($class->status === 'active')

                <form
                    action="{{ route('teacher.classes.archive', $class->id) }}"
                    method="POST"
                >
                    @csrf
                    @method('PATCH')

                    <button type="submit">
                        Archivar clase
                    </button>
                </form>

            @else

                <form
                    action="{{ route('teacher.classes.unarchive', $class->id) }}"
                    method="POST"
                >
                    @csrf
                    @method('PATCH')

                    <button type="submit">
                        Desarchivar clase
                    </button>
                </form>

            @endif

        </section>

    </main>

</body>

</html>

