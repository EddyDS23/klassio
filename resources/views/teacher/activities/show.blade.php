<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->title }}</title>
</head>

<body>

    <h1>{{ $activity->title }}</h1>

    @if (session('success'))
        <p style="color: green;">
            {{ session('success') }}
        </p>
    @endif

    @if (session('error'))
        <p style="color: red;">
            {{ session('error') }}
        </p>
    @endif

    <h2>Información de la actividad</h2>

    <p>
        <strong>Descripción:</strong>
        {{ $activity->description ?: 'Sin descripción' }}
    </p>

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
        {{ $activity->time_limit }} segundos
    </p>

    <p>
        <strong>Estado:</strong>
        {{ $activity->status }}
    </p>

    @if ($activity->due_at)
        <p>
            <strong>Fecha límite:</strong>
            {{ $activity->due_at }}
        </p>
    @else
        <p>
            <strong>Fecha límite:</strong>
            Sin fecha límite
        </p>
    @endif

    <hr>

    <h2>Acciones</h2>

    {{-- Editar información general --}}
    @if ($activity->status === 'draft')
        <p>
            <a href="{{ route('teacher.activities.edit', $activity->id) }}">
                Editar actividad
            </a>
        </p>
    @endif


    {{-- Configuración específica del juego --}}
    @if ($activity->status === 'draft')

        <h3>Configuración del juego</h3>

        @switch($activity->type)

            {{-- Crucigrama --}}
            @case('crossword')

                @if ($activity->crossword && $activity->crossword->words()->exists())
                    <p>
                        <a href="{{ route('teacher.crossword.edit', $activity->id) }}">
                            Editar crucigrama
                        </a>
                    </p>
                @else
                    <p>
                        <a href="{{ route('teacher.crossword.configure', $activity->id) }}">
                            Configurar crucigrama
                        </a>
                    </p>
                @endif

                @break


            {{-- Kahoot --}}
            @case('kahoot')

                @if ($activity->kahoot && $activity->kahoot->questions()->exists())
                    <p>
                        <a href="{{ route('teacher.kahoot.edit', $activity->id) }}">
                            Editar Kahoot
                        </a>
                    </p>
                @else
                    <p>
                        <a href="{{ route('teacher.kahoot.configure', $activity->id) }}">
                            Configurar Kahoot
                        </a>
                    </p>
                @endif

                @break


            {{-- Sopa de letras --}}
            @case('wordsearch')

                @if ($activity->wordsearch && $activity->wordsearch->words()->exists())
                    <p>
                        <a href="{{ route('teacher.wordsearch.edit', $activity->id) }}">
                            Editar sopa de letras
                        </a>
                    </p>
                @else
                    <p>
                        <a href="{{ route('teacher.wordsearch.configure', $activity->id) }}">
                            Configurar sopa de letras
                        </a>
                    </p>
                @endif

                @break


            {{-- Unir conceptos --}}
            @case('matching')

                @if ($activity->matching && $activity->matching->items()->exists())
                    <p>
                        <a href="{{ route('teacher.matching.edit', $activity->id) }}">
                            Editar unir conceptos
                        </a>
                    </p>
                @else
                    <p>
                        <a href="{{ route('teacher.matching.configure', $activity->id) }}">
                            Configurar unir conceptos
                        </a>
                    </p>
                @endif

                @break

            {{-- Sopa de crucigrama --}}
            @case('word_search')

                @if ($activity->wordsearch && $activity->wordsearch->words()->exists())
                    <p>
                        <a href="{{ route('teacher.wordsearch.edit', $activity->id) }}">
                            Editar sopa de letras
                        </a>
                    </p>
                @else
                    <p>
                        <a href="{{ route('teacher.wordsearch.configure', $activity->id) }}">
                            Configurar sopa de letras
                        </a>
                    </p>
                @endif

                @break

            {{-- Tipo de actividad sin configuración --}}
            @default

                <p>
                    Este tipo de actividad no tiene configuración adicional.
                </p>

        @endswitch

    @endif


    {{-- Publicar --}}
    @if ($activity->status === 'draft')
        <form action="{{ route('teacher.activities.publish', $activity->id) }}" method="POST">
            @csrf

            <button type="submit">
                Publicar actividad
            </button>
        </form>
    @endif


    {{-- Cerrar --}}
    @if ($activity->status === 'published')
        <form action="{{ route('teacher.activities.close', $activity->id) }}" method="POST">
            @csrf

            <button type="submit">
                Cerrar actividad
            </button>
        </form>
    @endif

    <hr>

    <p>
        <a href="{{ route('teacher.activities.index', $activity->class_id) }}">
            Volver a actividades
        </a>
    </p>

</body>

</html>