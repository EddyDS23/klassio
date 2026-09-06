<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Compañeros</title>
</head>

<body>

    <header>

        <nav>
            <a href="{{ route('student.class.show', $class->id) }}">
                Volver a la clase
            </a>
        </nav>

        <h1>Compañeros de {{ $class->name }}</h1>

    </header>

    <main>

        <section>

            <h2>Lista de compañeros</h2>

            @if($students->isEmpty())

                <p>
                    No hay compañeros en esta clase.
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
                        </li>

                    @endforeach

                </ul>

            @endif

        </section>

    </main>

</body>

</html>

