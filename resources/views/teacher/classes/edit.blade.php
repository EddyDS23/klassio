
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar clase</title>
</head>

<body>

    <header>

        <h1>Editar clase</h1>

        <nav>
            <a href="{{ route('teacher.classes.show', $class->id) }}">
                Volver a la clase
            </a>

            <br>

            <a href="{{ route('teacher.classes.index') }}">
                Volver a mis clases
            </a>
        </nav>

    </header>

    <main>

        <section>

            <h2>{{ $class->name }}</h2>

            @if($errors->any())

                <div>

                    <h3>No se pudo actualizar la clase</h3>

                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>

            @endif

            <form
                action="{{ route('teacher.classes.update', $class->id) }}"
                method="POST"
            >

                @csrf
                @method('PUT')

                <div>

                    <label for="name">
                        Nombre de la clase:
                    </label>

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

                    <label for="description">
                        Descripción:
                    </label>

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

        </section>

    </main>

</body>

</html>

