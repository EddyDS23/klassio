
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Alumnos - {{ $class->name }}</title>
</head>

<body>

    <header>

        <nav>
            <a href="{{ route('teacher.classes.show', $class->id) }}">
                Volver a la clase
            </a>

            <br>

            <a href="{{ route('teacher.classes.index') }}">
                Volver a mis clases
            </a>
        </nav>

        <h1>Alumnos de {{ $class->name }}</h1>

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

            <h2>Alumnos inscritos</h2>

            @if($students->isEmpty())

                <p>
                    No hay alumnos inscritos en esta clase.
                </p>

            @else

                <ul>

                    @foreach($students as $enrollment)

                        <li>

                            <strong>
                                {{ $enrollment->student->name }}
                            </strong>

                            <span>
                                - {{ $enrollment->student->email }}
                            </span>

                            <form
                                action="{{ route(
                                    'teacher.classes.students.remove',
                                    [
                                        'id' => $class->id,
                                        'studentId' => $enrollment->student->id
                                    ]
                                ) }}"
                                method="POST"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit">
                                    Retirar alumno
                                </button>
                            </form>

                        </li>

                    @endforeach

                </ul>

            @endif

        </section>

    </main>

</body>

</html>

