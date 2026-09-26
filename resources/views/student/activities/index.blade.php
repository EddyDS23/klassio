<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades · {{ $class->name }}</title>
    @include('partials.assets', ['theme' => 'student'])
</head>
<body>
    <main class="page-shell">
        <section class="page-hero">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div><span class="badge rounded-pill text-bg-light text-primary mb-3">MI AULA</span><h1 class="display-6 fw-bold mb-2">Actividades de {{ $class->name }}</h1><p class="mb-0 opacity-75">Aprende jugando, supera retos y reúne puntos.</p></div>
                <div class="display-3">🚀</div>
            </div>
        </section>

        <section class="card surface-card mb-4"><div class="card-body p-4">
            <form method="GET" action="{{ route('student.activities.index', $class->id) }}" class="row g-3 align-items-end">
                <div class="col-md-5"><label class="form-label fw-semibold" for="search">Buscar actividad</label><input class="form-control form-control-lg" type="search" name="search" id="search" value="{{ request('search') }}" placeholder="Ej. animales, fracciones…"></div>
                <div class="col-md-4"><label class="form-label fw-semibold" for="type">Tipo de reto</label><select class="form-select form-select-lg" name="type" id="type"><option value="">Todos los retos</option><option value="kahoot" @selected(request('type') === 'kahoot')>Kahoot</option><option value="crossword" @selected(request('type') === 'crossword')>Crucigrama</option><option value="word_search" @selected(request('type') === 'word_search')>Sopa de letras</option><option value="matching" @selected(request('type') === 'matching')>Unir conceptos</option><option value="roulette" @selected(request('type') === 'roulette')>Ruleta</option></select></div>
                <div class="col-md-auto d-flex gap-2"><button class="btn btn-klassio btn-lg px-4" type="submit">Buscar</button>@if (request()->filled('search') || request()->filled('type'))<a class="btn btn-outline-secondary btn-lg" href="{{ route('student.activities.index', $class->id) }}">Limpiar</a>@endif</div>
            </form>
        </div></section>

        @if ($activities->isEmpty())
            <section class="card surface-card text-center"><div class="card-body p-5"><div class="display-3 mb-3">🎈</div><h2 class="h3 fw-bold">Aún no hay actividades</h2><p class="text-secondary mb-0">Cuando tu maestro publique una, aparecerá aquí.</p></div></section>
        @else
            <div class="row g-4">
                @foreach ($activities as $activity)
                    @php($icon = match($activity->type) { 'kahoot' => '⚡', 'crossword' => '🧩', 'word_search' => '🔎', 'matching' => '🔗', 'roulette' => '🎡', default => '🎯' })
                    <div class="col-md-6 col-xl-4"><article class="card surface-card h-100"><div class="card-body p-4 d-flex flex-column"><div class="d-flex align-items-start justify-content-between gap-3 mb-3"><div class="activity-icon">{{ $icon }}</div><span class="badge rounded-pill text-bg-info">{{ $activity->mode === 'team' ? 'En equipo' : 'Individual' }}</span></div><h2 class="h4 fw-bold">{{ $activity->title }}</h2><p class="text-secondary flex-grow-1">{{ $activity->description ?: 'Un nuevo reto para poner a prueba lo que aprendiste.' }}</p><div class="d-flex flex-wrap gap-2 small text-secondary mb-4"><span class="badge text-bg-light">🏆 {{ $activity->max_score }} pts</span>@if ($activity->time_limit)<span class="badge text-bg-light">⏱ {{ $activity->time_limit }} s</span>@endif</div><a class="btn btn-klassio w-100" href="{{ route('student.activities.show', $activity->id) }}">Ver reto <span aria-hidden="true">→</span></a></div></article></div>
                @endforeach
            </div>
        @endif
        @if ($activities->hasPages())
            <div class="mt-4 d-flex justify-content-center">{{ $activities->links() }}</div>
        @endif
        <nav class="mt-4"><a class="btn btn-outline-secondary" href="{{ route('student.class.show', $class->id) }}">← Volver a la clase</a></nav>
    </main>
</body>
</html>
