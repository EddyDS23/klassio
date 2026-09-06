<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Estudiante</title>
</head>

<body>

    <header>
        <h1>Dashboard del estudiante</h1>

        <p>
            Bienvenido, {{ auth()->user()->name }}
        </p>
    </header>

    <main>

        @if(session('success'))
            <p>
                {{ session('success') }}
            </p>
        @endif

        @if(session('error'))
            <p>
                {{ session('error') }}
            </p>
        @endif

        <section>
            <h2>Mis clases</h2>

            <p>
                Consulta las clases en las que estás inscrito.
            </p>

            <a href="{{ route('student.classes.index') }}">
                Ver mis clases
            </a>
        </section>

        <hr>

        <section>
            <h2>Unirse a una clase</h2>

            <p>
                Ingresa el código proporcionado por tu maestro para
                unirte a una clase.
            </p>

            <form
                action="{{ route('student.class.join') }}"
                method="POST"
            >

                @csrf

                <div>
                    <label for="code">
                        Código de la clase:
                    </label>

                    <input
                        type="text"
                        id="code"
                        name="code"
                        value="{{ old('code') }}"
                        maxlength="6"
                        required
                    >
                </div>

                @error('code')
                    <p>{{ $message }}</p>
                @enderror

                <button type="submit">
                    Unirse a la clase
                </button>

            </form>
        </section>

    </main>

</body>

</html>

