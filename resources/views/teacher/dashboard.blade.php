
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Maestro</title>
</head>

<body>

    <header>

        <h1>Dashboard del maestro</h1>

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
                Administra tus clases, estudiantes y actividades.
            </p>

            <a href="{{ route('teacher.classes.index') }}">
                Ver mis clases
            </a>

            <br>

            <a href="{{ route('teacher.classes.create') }}">
                Crear clase
            </a>

        </section>

    </main>

</body>

</html>

