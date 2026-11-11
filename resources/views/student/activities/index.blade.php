<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades · {{ $class->name }}</title>
    
    <!-- Fuentes Google Gamificadas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @include('partials.assets', ['theme' => 'student'])

    <style>
        :root {
            --bg-main: #f4f7fe;
            --card-bg: #ffffff;
            --primary: #6c5ce7;
            --primary-hover: #5b4bc4;
            --success: #05cd99;
            --warning: #ffb547;
            --danger: #ee5d50;
            --text-dark: #2b3674;
            --text-muted: #707ebe;
            --border: #e9edf7;
            --radius-lg: 20px;
            --radius-md: 14px;
            --shadow-md: 0px 10px 20px rgba(112, 144, 176, 0.07);
        }

        * { box-sizing: border-box; }

        body.student-kids {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
            min-height: 100vh;
        }

        .page-shell {
            max-width: 960px;
            margin: 0 auto;
            padding: 32px 16px;
        }

        /* Botón Volver */
        .klassio-back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            background-color: #ffffff;
            color: var(--text-muted);
            border: 1px solid var(--border);
            transition: all 0.2s ease;
        }
        .klassio-back-button:hover {
            background-color: #f8fafc;
            color: var(--text-dark);
            transform: translateX(-3px);
        }

        /* Hero Header Gamificado */
        .page-hero {
            background: linear-gradient(135deg, #6c5ce7 0%, #4318ff 100%);
            border-radius: var(--radius-lg);
            padding: 32px 36px;
            color: #ffffff;
            margin-bottom: 24px;
            box-shadow: 0 10px 25px rgba(108, 92, 231, 0.25);
            position: relative;
            overflow: hidden;
        }

        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .hero-title {
            font-family: 'Fredoka', cursive;
            font-size: 36px;
            margin-bottom: 6px;
        }

        .hero-sub {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Tarjeta de Filtros */
        .surface-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
        }

        .filter-card {
            padding: 24px;
            margin-bottom: 24px;
        }

        .filter-label {
            font-family: 'Fredoka', cursive;
            font-size: 16px;
            color: var(--text-dark);
            margin-bottom: 8px;
            display: block;
        }

        .form-control-custom, .form-select-custom {
            width: 100%;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            font-size: 15px;
            font-weight: 600;
            color: var(--text-dark);
            background-color: #f8fafc;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            border-color: var(--primary);
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.1);
        }

        .btn-klassio {
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-md);
            padding: 12px 24px;
            font-family: 'Fredoka', cursive;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
        }
        .btn-klassio:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-outline-custom {
            background-color: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 12px 20px;
            font-weight: 700;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
        }
        .btn-outline-custom:hover {
            background-color: #f8fafc;
            color: var(--text-dark);
        }

        /* Acordeones / Unidades (<details>) */
        details.unit-accordion {
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        summary.unit-summary {
            padding: 18px 24px;
            cursor: pointer;
            list-style: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
            background: #ffffff;
            transition: background 0.2s ease;
        }
        summary.unit-summary::-webkit-details-marker { display: none; }
        summary.unit-summary:hover { background: #fcfdfe; }

        .unit-title-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: 'Fredoka', cursive;
            font-size: 18px;
            color: var(--primary);
        }

        .unit-badge {
            background: #eef2ff;
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }

        .unit-content {
            border-top: 1px solid var(--border);
            padding: 20px;
            background: #fafbfc;
        }

        /* Estado Vacío */
        .empty-card {
            padding: 48px 24px;
            text-align: center;
        }
        .empty-icon {
            font-size: 54px;
            margin-bottom: 12px;
        }
        .empty-title {
            font-family: 'Fredoka', cursive;
            font-size: 24px;
            color: var(--text-dark);
            margin-bottom: 6px;
        }
    </style>
</head>
<body class="student-kids">
    <main class="page-shell">
        
        {{-- Botón Volver --}}
        <div class="mb-4">
            <a href="{{ route('student.class.show', $class->id) }}" class="klassio-back-button">
                <i class="fa-solid fa-arrow-left"></i> Volver a la clase
            </a>
        </div>

        {{-- Encabezado Principal --}}
        <section class="page-hero">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="hero-badge">MI AULA 🎒</span>
                    <h1 class="hero-title">¡A jugar! 🎮</h1>
                    <p class="hero-sub mb-0">Elige un reto de <strong>{{ $class->name }}</strong>.</p>
                </div>
                <div style="font-size: 64px; line-height: 1;">🚀</div>
            </div>
        </section>

        {{-- Formulario de Búsqueda y Filtros --}}
        <section class="card surface-card filter-card">
            <form method="GET" action="{{ route('student.activities.index', $class->id) }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="filter-label" for="search">🔎 Buscar actividad</label>
                    <input class="form-control-custom" type="search" name="search" id="search" value="{{ request('search') }}" placeholder="Ej. animales, fracciones…">
                </div>
                
                <div class="col-md-4">
                    <label class="filter-label" for="type">🎮 Tipo de reto</label>
                    <select class="form-select-custom" name="type" id="type">
                        <option value="">Todos los retos</option>
                        <option value="kahoot" @selected(request('type') === 'kahoot')>Kahoot</option>
                        <option value="crossword" @selected(request('type') === 'crossword')>Crucigrama</option>
                        <option value="word_search" @selected(request('type') === 'word_search')>Sopa de letras</option>
                        <option value="matching" @selected(request('type') === 'matching')>Unir conceptos</option>
                        <option value="roulette" @selected(request('type') === 'roulette')>Ruleta</option>
                    </select>
                </div>
                
                <div class="col-md-auto d-flex gap-2">
                    <button class="btn-klassio" type="submit">Buscar 🔎</button>
                    @if (request()->filled('search') || request()->filled('type'))
                        <a class="btn-outline-custom" href="{{ route('student.activities.index', $class->id) }}">Limpiar</a>
                    @endif
                </div>
            </form>
        </section>

        {{-- Lista de Actividades --}}
        @if ($activities->isEmpty())
            <section class="card surface-card empty-card">
                <div class="empty-icon">🎈</div>
                <h2 class="empty-title">¡Todavía no hay actividades!</h2>
                <p class="text-secondary mb-0 fs-5">Cuando tu maestro publique una, aparecerá aquí.</p>
            </section>
        @else
            @php
                $pageActivities = $activities->getCollection();
                $unitGroups = $units
                    ->map(fn ($unit) => ['unit' => $unit, 'activities' => $pageActivities->where('unit_id', $unit->id)->values()])
                    ->reject(fn ($group) => $group['activities']->isEmpty())
                    ->values();
                $unassigned = $pageActivities->filter(fn ($activity) => $activity->unit_id === null)->values();
            @endphp

            {{-- Unidades con Actividades --}}
            @forelse ($unitGroups as $group)
                <details class="card surface-card unit-accordion" @if ($loop->first) open @endif>
                    <summary class="unit-summary">
                        <span class="unit-title-wrap">
                            <span>📂</span>
                            <span class="text-truncate">Unidad {{ $group['unit']->number }}{{ filled($group['unit']->name) ? ' — '.$group['unit']->name : '' }}</span>
                        </span>
                        <span class="unit-badge">{{ $group['activities']->count() }} {{ $group['activities']->count() === 1 ? 'actividad' : 'actividades' }}</span>
                    </summary>
                    <div class="unit-content">
                        <div class="row g-3">
                            @each('partials.activity-card-student', $group['activities'], 'activity')
                        </div>
                    </div>
                </details>
            @empty
            @endforelse

            {{-- Actividades Sin Unidad --}}
            @if ($unassigned->isNotEmpty())
                <details class="card surface-card unit-accordion" @if ($unitGroups->isEmpty()) open @endif>
                    <summary class="unit-summary">
                        <span class="unit-title-wrap" style="color: var(--text-muted);">
                            <span>🗂️</span>
                            <span class="text-truncate">Sin unidad</span>
                        </span>
                        <span class="unit-badge" style="background: #f1f5f9; color: var(--text-muted);">
                            {{ $unassigned->count() }} {{ $unassigned->count() === 1 ? 'actividad' : 'actividades' }}
                        </span>
                    </summary>
                    <div class="unit-content">
                        <div class="row g-3">
                            @each('partials.activity-card-student', $unassigned, 'activity')
                        </div>
                    </div>
                </details>
            @endif
        @endif

        {{-- Paginación --}}
        @if ($activities->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $activities->links() }}
            </div>
        @endif

    </main>
</body>
</html>