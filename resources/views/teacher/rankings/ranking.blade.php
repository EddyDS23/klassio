<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ranking - {{ $activity->title }}</title>

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        section {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <h1>Ranking</h1>

    <section>
        <h2>{{ $activity->title }}</h2>

        <p>
            <strong>Tipo:</strong>
            {{ ucfirst(str_replace('_', ' ', $activity->type)) }}
        </p>

        <p>
            <strong>Modo:</strong>
            {{ $activity->mode === 'team' ? 'Equipos' : 'Individual' }}
        </p>

        <p>
            <strong>Puntuación máxima:</strong>
            {{ $activity->max_score }}
        </p>

        @if($activity->time_limit)
            <p>
                <strong>Tiempo límite:</strong>
                {{ $activity->time_limit }} segundos
            </p>
        @endif
    </section>


    <section>
        <h2>Top 3</h2>

        @if($topThree->isEmpty())

            <p>No hay participaciones completadas.</p>

        @else

            <table>
                <thead>
                    <tr>
                        <th>Posición</th>

                        @if($activity->mode === 'team')
                            <th>Equipo</th>
                        @else
                            <th>Estudiante</th>
                        @endif

                        <th>Puntuación</th>
                        <th>Tiempo</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($topThree as $entry)

                        <tr>
                            <td>
                                {{ $entry['position'] }}
                            </td>

                            <td>
                                @if($activity->mode === 'team')
                                    {{ $entry['team']->name ?? 'Equipo' }}
                                @else
                                    {{ $entry['student']->name ?? 'Estudiante' }}
                                @endif
                            </td>

                            <td>
                                {{ $entry['score'] }}
                                /
                                {{ $activity->max_score }}
                            </td>

                            <td>
                                @if($entry['elapsed_seconds'] !== null)
                                    {{ floor($entry['elapsed_seconds'] / 60) }}:{{
                                        str_pad(
                                            $entry['elapsed_seconds'] % 60,
                                            2,
                                            '0',
                                            STR_PAD_LEFT
                                        )
                                    }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>

        @endif
    </section>


    <section>
        <h2>Clasificación completa</h2>

        @if($ranking->isEmpty())

            <p>
                No hay participaciones completadas para esta actividad.
            </p>

        @else

            <table>
                <thead>
                    <tr>
                        <th>Posición</th>

                        @if($activity->mode === 'team')

                            <th>Equipo</th>

                        @else

                            <th>Estudiante</th>
                            <th>Correo</th>

                        @endif

                        <th>Puntuación</th>
                        <th>Tiempo</th>
                        <th>Mejor intento</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($ranking as $entry)

                        <tr>

                            <td>
                                {{ $entry['position'] }}
                            </td>


                            @if($activity->mode === 'team')

                                <td>
                                    {{ $entry['team']->name ?? 'Equipo' }}
                                </td>

                            @else

                                <td>
                                    {{ $entry['student']->name ?? 'Estudiante' }}
                                </td>

                                <td>
                                    {{ $entry['student']->email ?? '-' }}
                                </td>

                            @endif


                            <td>
                                {{ $entry['score'] }}
                                /
                                {{ $activity->max_score }}
                            </td>


                            <td>
                                @if($entry['elapsed_seconds'] !== null)

                                    {{ floor($entry['elapsed_seconds'] / 60) }}:{{
                                        str_pad(
                                            $entry['elapsed_seconds'] % 60,
                                            2,
                                            '0',
                                            STR_PAD_LEFT
                                        )
                                    }}

                                @else

                                    -

                                @endif
                            </td>


                            <td>
                                #{{ $entry['attempt'] }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>
            </table>

        @endif
    </section>


    <section>
        <a href="{{ route('teacher.activities.show', $activity->id) }}">
            Volver a la actividad
        </a>

        |

        <a href="{{ route('teacher.activities.results', $activity->id) }}">
            Ver resultados de estudiantes
        </a>
    </section>

</body>
</html>