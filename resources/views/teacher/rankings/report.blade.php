<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reporte - {{ $activity->title }}</title>

    <style>
        section {
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }
    </style>
</head>

<body>

    <h1>Reporte de actividad</h1>

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

        @if ($activity->time_limit)
            <p>
                <strong>Tiempo límite:</strong>
                {{ $activity->time_limit }} segundos
            </p>
        @endif
    </section>


    <section>

        <h2>Participación</h2>

        <table>

            <tr>
                <th>
                    {{ $activity->mode === 'team' ? 'Equipos' : 'Estudiantes inscritos' }}
                </th>

                <td>
                    {{ $activity->mode === 'team' ? $summary['total_teams'] : $summary['total_students'] }}
                </td>
            </tr>

            <tr>
                <th>
                    {{ $activity->mode === 'team' ? 'Equipos que participaron' : 'Estudiantes que participaron' }}
                </th>

                <td>{{ $summary['participated'] }}</td>
            </tr>

            <tr>
                <th>
                    {{ $activity->mode === 'team' ? 'Equipos que no participaron' : 'Estudiantes que no participaron' }}
                </th>

                <td>{{ $summary['not_participated'] }}</td>
            </tr>

        </table>

    </section>


    <section>

        <h2>
            Estado de las
            {{ $activity->mode === 'team' ? 'participaciones por equipo' : 'participaciones' }}
        </h2>

        <table>

            <tr>
                <th>Completados</th>
                <td>{{ $summary['completed'] }}</td>
            </tr>

            <tr>
                <th>En progreso</th>
                <td>{{ $summary['started'] }}</td>
            </tr>

            <tr>
                <th>Abandonados</th>
                <td>{{ $summary['abandoned'] }}</td>
            </tr>

            <tr>
                <th>Tiempo agotado</th>
                <td>{{ $summary['expired'] }}</td>
            </tr>

        </table>

    </section>


    <section>

        <h2>Rendimiento</h2>

        <table>

            <tr>
                <th>Promedio de puntuación</th>

                <td>
                    @if ($summary['average_score'] !== null)
                        {{ $summary['average_score'] }}
                        / {{ $activity->max_score }}
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <th>Mejor puntuación</th>

                <td>
                    @if ($summary['best_score'] !== null)
                        {{ $summary['best_score'] }}
                        / {{ $activity->max_score }}
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <th>Tiempo promedio</th>

                <td>
                    @if ($summary['average_time'] !== null)
                        {{ floor($summary['average_time'] / 60) }}:{{ str_pad($summary['average_time'] % 60, 2, '0', STR_PAD_LEFT) }}
                    @else
                        -
                    @endif
                </td>
            </tr>

        </table>

    </section>


    <section>

        <a href="{{ route('teacher.activities.show', $activity->id) }}">
            Volver a la actividad
        </a>

        |

        <a href="{{ route('teacher.activities.results', $activity->id) }}">
            Ver resultados de estudiantes
        </a>

        |

        <a href="{{ route('teacher.activities.ranking', $activity->id) }}">
            Ver ranking
        </a>

    </section>

</body>

</html>
