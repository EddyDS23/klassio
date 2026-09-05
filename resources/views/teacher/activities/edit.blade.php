<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar actividad</title>
</head>
<body>

    <h1>Editar actividad</h1>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form
        action="{{ route('teacher.activities.update', $activity->id) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <div>
            <label for="title">Título</label>

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
            <label for="description">Descripción</label>

            <textarea
                id="description"
                name="description"
            >{{ old('description', $activity->description) }}</textarea>
        </div>

        <br>

        <div>
            <label for="type">Tipo</label>

            <select id="type" name="type" required>
                <option value="word_search"
                    {{ $activity->type === 'word_search' ? 'selected' : '' }}>
                    Sopa de letras
                </option>

                <option value="crossword"
                    {{ $activity->type === 'crossword' ? 'selected' : '' }}>
                    Crucigrama
                </option>

                <option value="matching"
                    {{ $activity->type === 'matching' ? 'selected' : '' }}>
                    Relacionar
                </option>

                <option value="kahoot"
                    {{ $activity->type === 'kahoot' ? 'selected' : '' }}>
                    Kahoot
                </option>
            </select>
        </div>

        <br>

        <div>
            <label for="mode">Modo</label>

            <select id="mode" name="mode" required>
                <option value="individual"
                    {{ $activity->mode === 'individual' ? 'selected' : '' }}>
                    Individual
                </option>

                <option value="team"
                    {{ $activity->mode === 'team' ? 'selected' : '' }}>
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
                value="{{ old('max_score', $activity->max_score) }}"
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
                value="{{ old('time_limit', $activity->time_limit) }}"
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
                value="{{ old('due_at', $activity->due_at?->format('Y-m-d\TH:i')) }}"
            >
        </div>

        <br>

        <button type="submit">
            Guardar cambios
        </button>

    </form>

    <br>

    <a href="{{ route('teacher.activities.show', $activity->id) }}">
        Cancelar
    </a>

</body>
</html>

