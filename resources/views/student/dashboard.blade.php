<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Estudiante</title>
</head>
<body>

    <header>
        <h1>Dashboard</h1>

        <p>
            Bienvenido, {{ auth()->user()->name }}
        </p>
    </header>

    <main>

        <section>
            <h2>Mis clases</h2>

            <p>
                Consulta las clases en las que estás inscrito.
            </p>

            <a href="{{ route('student.classes.index') }}">
                Ver mis clases
            </a>
        </section>

        <section>
            <h2>Unirse a una clase</h2>

            <form
                action="{{ route('student.class.join') }}"
                method="POST"
            >
                @csrf

                <label for="code">
                    Código de la clase
                </label>

                <input
                    type="text"
                    id="code"
                    name="code"
                    value="{{ old('code') }}"
                    maxlength="6"
                    required
                >

                @error('code')
                    <p>{{ $message }}</p>
                @enderror

                <button type="submit">
                    Unirse
                </button>
            </form>
        </section>

    </main>

</body>
</html>