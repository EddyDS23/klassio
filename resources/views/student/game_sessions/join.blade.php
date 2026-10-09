<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unirse a una sala</title>
    @include('partials.assets', ['theme' => 'student'])
</head>

<body>

    <div class="page-shell">

        <div class="page-hero">
            <h1 class="fw-bold mb-1">Unirse a una sala</h1>
            <p class="mb-0 opacity-75">Ingresa el código que te compartió tu profesor y únete a la partida.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card surface-card">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">Entrar con código</h2>

                        <form method="POST" action="{{ route('student.game-sessions.join') }}">
                            @csrf

                            <label class="form-label fw-semibold" for="code">Código de la sala</label>
                            <input
                                class="form-control form-control-lg text-center fw-bold mb-4"
                                style="text-transform: uppercase; letter-spacing: .2em;"
                                name="code"
                                id="code"
                                placeholder="KLS482"
                                maxlength="8"
                                required>

                            <button type="submit" class="btn btn-klassio w-100">Unirme a la sala</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">Volver al inicio</a>
        </div>

    </div>

</body>

</html>