<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compañeros</title>
</head>
<body>

    <h1>Compañeros de {{ $class->name }}</h1>

    <a href="{{ route('student.class.show', $class->id) }}">
        Volver a la clase
    </a>

    <hr>

    @if ($students->isEmpty())
        <p>No hay compañeros en esta clase.</p>
    @else
        <ul>
            @foreach ($students as $enrollment)
                <li>
                    {{ $enrollment->student->name }}
                    -
                    {{ $enrollment->student->email }}
                </li>
            @endforeach
        </ul>
    @endif

</body>
</html>