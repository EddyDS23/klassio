
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar clase</title>
</head>
<body>

    <h1>Editar clase</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('teacher.classes.update', $class->id) }}" method="POST">

        @csrf
        @method('PUT')

        <div>
            <label for="name">Nombre</label>
            <br>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $class->name) }}"
                required
            >
        </div>

        <br>

        <div>
            <label for="description">Descripción</label>
            <br>

            <textarea
                id="description"
                name="description"
                rows="4"
            >{{ old('description', $class->description) }}</textarea>
        </div>

        <br>

        <button type="submit">
            Guardar cambios
        </button>

        <a href="{{ route('teacher.classes.show', $class->id) }}">
            Cancelar
        </a>

    </form>

</body>
</html>

