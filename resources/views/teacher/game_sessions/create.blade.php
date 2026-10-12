<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear sala multijugador</title>
    @include('partials.assets', ['theme' => 'teacher'])
</head>

<body>

    <div class="page-shell">

        <div class="page-hero">
            <h1 class="fw-bold mb-1">Crear sala multijugador</h1>
            <p class="mb-0 opacity-75">Elige una actividad publicada y comparte el código con tus estudiantes.</p>
        </div>

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
                        <h2 class="h5 fw-bold mb-3">Nueva sala</h2>

                        @if ($activities->isEmpty())
                            <div class="alert alert-warning mb-3">
                                Aún no tienes actividades publicadas. Publica una actividad para crear una sala.
                            </div>

                            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">
                                Volver al panel
                            </a>
                        @else
                            <form method="POST" action="{{ route('teacher.game-sessions.store') }}">
                                @csrf

                                <label class="form-label fw-semibold" for="activity_id">Actividad</label>
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
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">Volver al panel</a>
        </div>

    </div>

</body>

</html>