<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salas de juego</title>
    @include('partials.assets', ['theme' => 'student'])
</head>

<body>

    <div class="page-shell">

        <div class="page-hero">
            <h1 class="fw-bold mb-1">Salas de juego multijugador</h1>
            <p class="mb-0 opacity-75">Crea una sala con un código o entra a una que ya exista.</p>
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

        <div class="row g-4">

            {{-- Crear sala --}}
            <div class="col-lg-6">
                <div class="card surface-card h-100">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">Crear sala</h2>

                        <form method="POST" action="{{ route('student.game-sessions.store') }}">
                            @csrf

                            <label class="form-label fw-semibold" for="activity_id">Actividad</label>

                            @if ($activities->isEmpty())
                                <div class="alert alert-warning mb-3">
                                    No tienes actividades publicadas en tus clases.
                                </div>
                            @else
                                <select name="activity_id" id="activity_id" class="form-select mb-3" required>
                                    @foreach ($activities as $activity)
                                        <option value="{{ $activity->id }}"
                                            @selected($selectedActivityId === $activity->id)>
                                            {{ $activity->title }}
                                            ({{ $activity->schoolClass?->name }})
                                        </option>
                                    @endforeach
                                </select>

                                <label class="form-label fw-semibold" for="max_players">Máximo de jugadores</label>
                                <select name="max_players" id="max_players" class="form-select mb-4">
                                    @foreach ([2, 3, 4, 5, 6, 8] as $max)
                                        <option value="{{ $max }}" @selected($max === 4)>{{ $max }}</option>
                                    @endforeach
                                </select>

                                <button type="submit" class="btn btn-klassio w-100">
                                    Crear sala
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            {{-- Unirse por código --}}
            <div class="col-lg-6">
                <div class="card surface-card h-100">
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