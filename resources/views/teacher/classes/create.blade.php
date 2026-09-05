
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear clase</title>
</head>
<body>

    <h1>Crear clase</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('teacher.classes.store') }}" method="POST">

        @csrf

        <div>
            <label for="name">Nombre</label>
            <br>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
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
            >{{ old('description') }}</textarea>
        </div>

        <br>

        <button type="submit">
            Crear clase
        </button>

        <a href="{{ route('teacher.classes.index') }}">
            Cancelar
        </a>

    </form>

</body>
</html>

