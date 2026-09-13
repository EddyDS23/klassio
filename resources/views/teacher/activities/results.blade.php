<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Resultados - {{ $activity->title }}</title>
</head>

<body>

    <h1>Resultados</h1>

    <h2>{{ $activity->title }}</h2>

    <p>
        <strong>Tipo:</strong>
        {{ $activity->type }}
    </p>

    <p>
        <strong>Modo:</strong>
        {{ $activity->mode }}
    </p>

    <p>
        <strong>Puntuación máxima:</strong>
        {{ $activity->max_score }}
    </p>

    <p>
        <strong>Tiempo límite:</strong>

        @if ($activity->time_limit)
            {{ $activity->time_limit }} segundos
        @else
            Sin límite
        @endif
    </p>

    <hr>

    <h2>Alumnos</h2>

    @if ($results->isEmpty())

        <p>
            No hay alumnos inscritos en esta clase.
        </p>

    @else

        <table border="1" cellpadding="8" cellspacing="0">

            <thead>

                <tr>
                    <th>Alumno</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th>Intento</th>
                    <th>Puntuación</th>
                    <th>Tiempo</th>
                </tr>

            </thead>

            <tbody>

                @foreach ($results as $result)

                    <tr>

                        <td>
                            {{ $result['student']->name }}
                        </td>

                        <td>
                            {{ $result['student']->email }}
                        </td>

                        <td>

                            @switch($result['status'])

                                @case('completed')
                                    Completado
                                    @break

                                @case('abandoned')
                                    Abandonado
                                    @break

                                @case('expired')
                                    Expirado
                                    @break

                                @case('started')
                                    En progreso
                                    @break

                                @default
                                    Sin realizar

                            @endswitch

                        </td>

                        <td>
                            {{ $result['attempt'] ?? '—' }}
                        </td>

                        <td>

                            @if ($result['score'] !== null)

                                {{ $result['score'] }}
                                /
                                {{ $activity->max_score }}

                            @else

                                —

                            @endif

                        </td>

                        <td>

                            @if ($result['elapsed_seconds'] !== null)

                                {{ sprintf(
                                    '%02d:%02d',
                                    intdiv($result['elapsed_seconds'], 60),
                                    $result['elapsed_seconds'] % 60
                                ) }}

                            @else

                                —

                            @endif

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    @endif

    <hr>

    <p>
        <a href="{{ route('teacher.activities.show', $activity->id) }}">
            Volver a la actividad
        </a>
    </p>

</body>

</html>