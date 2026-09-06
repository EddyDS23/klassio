
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis clases</title>
</head>

<body>

    <header>

        <h1>Mis clases</h1>

        <p>
            Administra las clases que has creado.
        </p>

        <nav>
            <a href="{{ route('teacher.dashboard') }}">
                Volver al dashboard
            </a>

            <br>

            <a href="{{ route('teacher.classes.create') }}">
                Crear clase
            </a>
        </nav>

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

            <h2>Clases creadas</h2>

            @forelse($classes as $class)

                <article>

                    <h3>{{ $class->name }}</h3>

                    @if($class->description)
                        <p>
                            {{ $class->description }}
                        </p>
                    @else
                        <p>
                            Sin descripción.
                        </p>
                    @endif

                    <dl>

                        <dt>Código de acceso:</dt>
                        <dd>{{ $class->code }}</dd>

                        <dt>Estado:</dt>
                        <dd>{{ $class->status }}</dd>

                        <dt>Creada:</dt>
                        <dd>
                            {{ $class->created_at->format('d/m/Y') }}
                        </dd>

                    </dl>

                    <nav>

                        <a href="{{ route('teacher.classes.show', $class->id) }}">
                            Ver clase
                        </a>

                        @if($class->status === 'active')

                            <br>

                            <a href="{{ route('teacher.classes.edit', $class->id) }}">
                                Editar clase
                            </a>

                            <form
                                action="{{ route('teacher.classes.archive', $class->id) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit">
                                    Archivar clase
                                </button>
                            </form>

                        @else

                            <form
                                action="{{ route('teacher.classes.unarchive', $class->id) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit">
                                    Desarchivar clase
                                </button>
                            </form>

                        @endif

                    </nav>

                </article>

                <hr>

            @empty

                <p>
                    No tienes clases creadas.
                </p>

                <a href="{{ route('teacher.classes.create') }}">
                    Crear mi primera clase
                </a>

            @endforelse

        </section>

    </main>

</body>

</html>
