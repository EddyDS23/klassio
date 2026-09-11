<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar actividad</title>
</head>

<body>

    <h1>Editar actividad</h1>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <div>
            <h2>Errores</h2>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2>{{ $activity->title }}</h2>

    <form action="{{ route('teacher.activities.update', $activity->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="title">Título:</label>
            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title', $activity->title) }}"
                required
            >
        </div>

        <br>

        <div>
            <label for="description">Descripción:</label>
            <textarea
                id="description"
                name="description"
                rows="5"
            >{{ old('description', $activity->description) }}</textarea>
        </div>

        <br>

        <div>
            <label>Tipo de actividad:</label>

            <p>{{ $activity->type }}</p>

            <input
                type="hidden"
                name="type"
                value="{{ $activity->type }}"
            >
        </div>

        <br>

        <div>
            <label for="mode">Modo:</label>

            <select id="mode" name="mode" required>
                <option
                    value="individual"
                    {{ old('mode', $activity->mode) === 'individual' ? 'selected' : '' }}
                >
                    Individual
                </option>

                <option
                    value="team"
                    {{ old('mode', $activity->mode) === 'team' ? 'selected' : '' }}
                >
                    Equipo
                </option>
            </select>
        </div>

        <br>

        <div>
            <label for="max_score">Puntuación máxima:</label>

            <input
                type="number"
                id="max_score"
                name="max_score"
                value="{{ old('max_score', $activity->max_score) }}"
                min="1"
                required
            >
        </div>

        <br>

        <div>
            <label for="attempts">Intentos permitidos</label>

            <input type="number" name="attempts" id="attempts" min="1" value="{{ old('attempts') }}">
        </div>

        <br>

        <div>
            <label for="time_limit">Límite de tiempo (minutos):</label>

            <input
                type="number"
                id="time_limit"
                name="time_limit"
                value="{{ old('time_limit', $activity->time_limit) }}"
                min="1"
            >
        </div>

        <br>

        <div>
            <label for="due_at">Fecha límite:</label>

            <input
                type="datetime-local"
                id="due_at"
                name="due_at"
                value="{{ old(
                    'due_at',
                    $activity->due_at
                        ? $activity->due_at->format('Y-m-d\TH:i')
                        : ''
                ) }}"
            >
        </div>

        <br>

        <button type="submit">
            Guardar cambios
        </button>
    </form>

    <hr>

    <nav>
        <a href="{{ route('teacher.activities.show', $activity->id) }}">
            Cancelar
        </a>

        <br>

        <a href="{{ route('teacher.activities.index', $activity->class_id) }}">
            Volver a actividades
        </a>
    </nav>

</body>

</html>

