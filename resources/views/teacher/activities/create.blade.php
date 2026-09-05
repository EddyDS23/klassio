<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear actividad</title>
</head>
<body>

    <h1>Crear actividad</h1>

    <p>Clase: {{ $class->name }}</p>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form
        action="{{ route('teacher.activities.store', $class->id) }}"
        method="POST"
    >
        @csrf

        <div>
            <label for="title">Título</label>
            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                required
            >
        </div>

        <br>

        <div>
            <label for="description">Descripción</label>
            <textarea
                id="description"
                name="description"
            >{{ old('description') }}</textarea>
        </div>

        <br>

        <div>
            <label for="type">Tipo</label>

            <select id="type" name="type" required>
                <option value="">Seleccionar</option>

                <option value="word_search"
                    {{ old('type') === 'word_search' ? 'selected' : '' }}>
                    Sopa de letras
                </option>

                <option value="crossword"
                    {{ old('type') === 'crossword' ? 'selected' : '' }}>
                    Crucigrama
                </option>

                <option value="matching"
                    {{ old('type') === 'matching' ? 'selected' : '' }}>
                    Relacionar
                </option>

                <option value="kahoot"
                    {{ old('type') === 'kahoot' ? 'selected' : '' }}>
                    Kahoot
                </option>
            </select>
        </div>

        <br>

        <div>
            <label for="mode">Modo</label>

            <select id="mode" name="mode" required>
                <option value="individual"
                    {{ old('mode') === 'individual' ? 'selected' : '' }}>
                    Individual
                </option>

                <option value="team"
                    {{ old('mode') === 'team' ? 'selected' : '' }}>
                    Equipo
                </option>
            </select>
        </div>

        <br>

        <div>
            <label for="max_score">Puntuación máxima</label>

            <input
                type="number"
                id="max_score"
                name="max_score"
                value="{{ old('max_score') }}"
                min="1"
                required
            >
        </div>

        <br>

        <div>
            <label for="time_limit">Tiempo límite (segundos)</label>

            <input
                type="number"
                id="time_limit"
                name="time_limit"
                value="{{ old('time_limit') }}"
                min="1"
                required
            >
        </div>

        <br>

        <div>
            <label for="due_at">Fecha límite</label>

            <input
                type="datetime-local"
                id="due_at"
                name="due_at"
                value="{{ old('due_at') }}"
            >
        </div>

        <br>

        <button type="submit">
            Crear actividad
        </button>

    </form>

    <br>

    <a href="{{ route('teacher.activities.index', $class->id) }}">
        Volver
    </a>

</body>
</html>

