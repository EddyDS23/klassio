<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Alumnos</title>
</head>

<body>

    <h1>Alumnos de {{ $class->name }}</h1>

    <a href="{{ route('teacher.classes.show', $class->id) }}">
        Volver a la clase
    </a>

    <hr>

    @if ($students->isEmpty())

        <p>No hay alumnos inscritos en esta clase.</p>
    @else
        <ul>
            @foreach ($students as $enrollment)
                <div>
                    <strong>
                        {{ $enrollment->student->name }}
                    </strong>

                    <span>
                        {{ $enrollment->student->email }}
                    </span>

                    <form
                        action="{{ route('teacher.classes.students.remove', [
                            'id' => $class->id,
                            'studentId' => $enrollment->student->id,
                        ]) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit">
                            Remover
                        </button>
                    </form>
                    <hr>
                </div>
            @endforeach
        </ul>

    @endif

</body>

</html>
