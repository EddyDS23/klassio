<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades · {{ $class->name }}</title>
    @include('partials.assets', ['theme' => 'student'])
</head>
<body class="student-kids">
    <main class="page-shell">
        <section class="page-hero">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div><span class="badge rounded-pill text-bg-light text-primary mb-3">MI AULA 🎒</span><h1 class="display-4 fw-bold mb-2">¡A jugar! 🎮</h1><p class="mb-0 opacity-75 fs-5">Elige un reto de {{ $class->name }}.</p></div>
                <div class="display-3">🚀</div>
            </div>
        </section>

        <section class="card surface-card mb-4"><div class="card-body p-4">
            <form method="GET" action="{{ route('student.activities.index', $class->id) }}" class="row g-3 align-items-end">
                <div class="col-md-5"><label class="form-label fw-bold fs-5" for="search">🔎 Buscar actividad</label><input class="form-control form-control-lg" type="search" name="search" id="search" value="{{ request('search') }}" placeholder="Ej. animales, fracciones…"></div>
                <div class="col-md-4"><label class="form-label fw-bold fs-5" for="type">🎮 Tipo de reto</label><select class="form-select form-select-lg" name="type" id="type"><option value="">Todos los retos</option><option value="kahoot" @selected(request('type') === 'kahoot')>Kahoot</option><option value="crossword" @selected(request('type') === 'crossword')>Crucigrama</option><option value="word_search" @selected(request('type') === 'word_search')>Sopa de letras</option><option value="matching" @selected(request('type') === 'matching')>Unir conceptos</option><option value="roulette" @selected(request('type') === 'roulette')>Ruleta</option></select></div>
                <div class="col-md-auto d-flex gap-2"><button class="btn btn-klassio btn-lg px-5 py-3 fw-bold" type="submit">Buscar 🔎</button>@if (request()->filled('search') || request()->filled('type'))<a class="btn btn-outline-secondary btn-lg py-3" href="{{ route('student.activities.index', $class->id) }}">Limpiar</a>@endif</div>
            </form>
        </div></section>

        @if ($activities->isEmpty())
            <section class="card surface-card text-center"><div class="card-body p-5"><div class="display-3 mb-3">🎈</div><h2 class="h3 fw-bold">¡Todavía no hay actividades!</h2><p class="text-secondary mb-0 fs-5">Cuando tu maestro publique una, aparecerá aquí.</p></div></section>
        @else
            @php
                $pageActivities = $activities->getCollection();
                $unitGroups = $units
                    ->map(fn ($unit) => ['unit' => $unit, 'activities' => $pageActivities->where('unit_id', $unit->id)->values()])
                    ->reject(fn ($group) => $group['activities']->isEmpty())
                    ->values();
                $unassigned = $pageActivities->filter(fn ($activity) => $activity->unit_id === null)->values();
            @endphp

            @forelse ($unitGroups as $group)
                <details class="card surface-card mb-4" @if ($loop->first) open @endif>
                    <summary class="d-flex align-items-center justify-content-between gap-3 px-4 py-3 user-select-none" style="cursor: pointer;">
                        <span class="d-flex align-items-center gap-2 fw-bold text-primary text-uppercase" style="letter-spacing: .05em;">
                            <span>📂</span>
                            <span class="text-truncate">Unidad {{ $group['unit']->number }}{{ filled($group['unit']->name) ? ' — '.$group['unit']->name : '' }}</span>
                        </span>
                        <span class="badge rounded-pill text-bg-light">{{ $group['activities']->count() }} {{ $group['activities']->count() === 1 ? 'actividad' : 'actividades' }}</span>
                    </summary>
                    <div class="border-top p-3"><div class="row g-3">@each('partials.activity-card-student', $group['activities'], 'activity')</div></div>
                </details>
            @empty
            @endforelse

            @if ($unassigned->isNotEmpty())
                <details class="card surface-card mb-4" @if ($unitGroups->isEmpty()) open @endif>
                    <summary class="d-flex align-items-center justify-content-between gap-3 px-4 py-3 user-select-none" style="cursor: pointer;">
                        <span class="d-flex align-items-center gap-2 fw-bold text-secondary text-uppercase" style="letter-spacing: .05em;">
                            <span>🗂️</span>
                            <span class="text-truncate">Sin unidad</span>
                        </span>
                        <span class="badge rounded-pill text-bg-light">{{ $unassigned->count() }} {{ $unassigned->count() === 1 ? 'actividad' : 'actividades' }}</span>
                    </summary>
                    <div class="border-top p-3"><div class="row g-3">@each('partials.activity-card-student', $unassigned, 'activity')</div></div>
                </details>
            @endif
        @endif
        @if ($activities->hasPages())
            <div class="mt-4 d-flex justify-content-center">{{ $activities->links() }}</div>
        @endif
        <nav class="mt-4"><a class="btn btn-outline-secondary" href="{{ route('student.class.show', $class->id) }}">← Volver a la clase</a></nav>
    </main>
</body>
</html>
