
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear clase</title>
</head>

<body>

    <header>
        <h1>Crear clase</h1>

        <nav>
            <a href="{{ route('teacher.classes.index') }}">
                Volver a mis clases
            </a>
        </nav>
    </header>

    <main>

        <section>

            <h2>Información de la clase</h2>

            @if($errors->any())

                <div>
                    <h3>No se pudo crear la clase</h3>

                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            @endif

            <form
                action="{{ route('teacher.classes.store') }}"
                method="POST"
            >

                @csrf

                <div>
                    <label for="name">
                        Nombre de la clase:
                    </label>

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
                    <label for="description">
                        Descripción:
                    </label>

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

        </section>

    </main>

</body>

</html>

