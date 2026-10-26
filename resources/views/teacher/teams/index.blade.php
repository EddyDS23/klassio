@extends('teacher.layouts.session')

@section('title')Equipos — {{ $activity->title }}@endsection

@section('head')
    <style>
        .teams-page,
        .teams-page * {
            box-sizing: border-box;
        }

        body:has(.teams-page) {
            margin: 0;
            padding: 28px clamp(14px, 3vw, 36px) 48px;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: radial-gradient(circle at 92% 2%, rgb(249 115 22 / .13), transparent 24rem), #f6f4ff;
            color: #1e293b;
        }

        .teams-container {
            width: min(1180px, 100%);
            margin: 0 auto;
        }

        .teams-page .header {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            margin: 4px 0 20px;
            padding: clamp(24px, 4vw, 38px);
            border-radius: 24px;
            color: #fff;
            background: linear-gradient(118deg, #4c1d95, #7c3aed 58%, #c2410c);
            box-shadow: 0 18px 42px rgb(76 29 149 / .2);
        }

        .teams-page .header::after {
            position: absolute;
            z-index: -1;
            top: -80px;
            right: -40px;
            width: 280px;
            height: 280px;
            border: 1px solid rgb(255 255 255 / .2);
            border-radius: 50%;
            box-shadow: 0 0 0 28px rgb(255 255 255 / .06), 0 0 0 58px rgb(255 255 255 / .04);
            content: "";
        }

        .teams-page .header h1 {
            margin: 0 0 10px;
            font-size: clamp(1.9rem, 4vw, 2.7rem);
            line-height: 1.1;
            letter-spacing: -0.04em;
            color: #fff;
        }

        .teams-page .header p {
            margin: 0;
            color: rgb(255 255 255 / .84);
        }

        .teams-page .header p strong { color: #fff; }
        .teams-page .header .eyebrow { color: #fed7aa; font-size: .72rem; letter-spacing: .14em; }

        .teams-page a:focus-visible,
        .teams-page button:focus-visible,
        .teams-page input:focus-visible,
        .teams-page select:focus-visible {
            outline: 3px solid #f97316;
            outline-offset: 3px;
        }

        .eyebrow {
            margin: 0 0 8px;
            color: #fed7aa;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .workflow {
            margin-bottom: 20px;
            padding: 14px 18px;
            border: 1px solid #e5dcf6;
            border-radius: 16px;
            background: rgb(255 255 255 / .88);
            box-shadow: 0 8px 20px rgb(49 36 79 / .04);
        }

        .workflow ol {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 24px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .workflow-step {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .workflow-step span {
            display: inline-flex;
            width: 26px;
            height: 26px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #e2e8f0;
            color: #475569;
            font-size: 12px;
        }

        .workflow-step.is-complete,
        .workflow-step.is-current {
            color: #5b21b6;
        }

        .workflow-step.is-complete span,
        .workflow-step.is-current span {
            background: #7c3aed;
            color: #fff;
        }

        .workflow-step.is-current {
            font-weight: 900;
        }

        .workflow-step.is-blocked { color: #94a3b8; }
        .workflow-step.is-blocked span { background: #f1f5f9; color: #94a3b8; }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .panel {
            background: white;
            border: 1px solid #e9e4f5;
            border-radius: 20px;
            padding: clamp(18px, 3vw, 24px);
            margin-bottom: 20px;
            box-shadow: 0 12px 30px rgb(49 36 79 / .06);
        }

        .panel h2 {
            margin: 0 0 16px;
            color: #271747;
            font-size: clamp(1.08rem, 2vw, 1.25rem);
            font-weight: 850;
            letter-spacing: -.02em;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .section-intro {
            margin: -8px 0 18px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .summary-stat {
            min-width: 0;
            padding: 14px 16px;
            border: 1px solid #e9e4f5;
            border-radius: 15px;
            background: linear-gradient(145deg, #fff, #faf8ff);
            transition: transform .16s, box-shadow .16s;
        }

        .summary-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgb(49 36 79 / .08); }

        .summary-stat strong {
            display: block;
            color: #0f172a;
            font-size: 1.4rem;
            line-height: 1.2;
        }

        .summary-stat span {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.4;
        }

        .summary-stat.is-pending {
            border-color: #fed7aa;
            background: #fff7ed;
        }

        .summary-stat.is-pending strong {
            color: #9a3412;
        }

        .summary-stat.is-clear {
            border-color: #a7f3d0;
            background: #ecfdf5;
        }

        .summary-stat.is-clear strong {
            color: #047857;
        }

        .teams-page form {
            margin: 0;
        }

        .teams-page input,
        .teams-page select {
            width: 100%;
            min-height: 44px;
            padding: 9px 11px;
            border: 1px solid #cbd5e1;
            border-radius: 11px;
            font-size: 14px;
            background: white;
            transition: border-color .15s, box-shadow .15s;
        }

        .teams-page input:focus,
        .teams-page select:focus { border-color: #8b5cf6; box-shadow: 0 0 0 4px rgb(139 92 246 / .14); }

        .teams-page button {
            border: 0;
            border-radius: 11px;
            min-height: 44px;
            padding: 9px 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary {
            background: #6d28d9;
            color: white;
            box-shadow: 0 5px 12px rgb(109 40 217 / .18);
        }

        .btn-primary:hover {
            background: #5b21b6;
        }

        .next-step-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            text-decoration: none;
        }

        .next-step-link:focus-visible {
            outline: 3px solid #f97316;
            outline-offset: 3px;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-secondary {
            background: #475569;
            color: white;
        }

        .btn-secondary:hover {
            background: #334155;
        }

        .btn-warning {
            background: #ea580c;
            color: white;
            box-shadow: 0 5px 12px rgb(234 88 12 / .16);
        }

        .btn-warning:hover {
            background: #c2410c;
        }

        .create-team {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: end;
            gap: 10px;
        }

        .field-label {
            display: block;
            margin-bottom: 6px;
            color: #334155;
            font-size: 13px;
            font-weight: 800;
        }

        .form-field {
            min-width: 0;
        }

        .distribution-tools {
            margin-top: 22px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .distribution-tools h3 {
            margin: 0 0 6px;
            color: #0f172a;
            font-size: 15px;
        }

        .distribution-tools p {
            margin: 0 0 12px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
        }

        .randomize-form {
            display: grid;
            grid-template-columns: 180px auto;
            gap: 10px;
            max-width: 500px;
        }

        .teams-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
            gap: 18px;
        }

        .team-card {
            background: white;
            border: 1px solid #e9e4f5;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 7px 18px rgb(49 36 79 / .05);
            transition: transform .18s, box-shadow .18s;
        }

        .team-card:hover { transform: translateY(-3px); box-shadow: 0 14px 28px rgb(49 36 79 / .1); }

        .team-header {
            padding: 16px;
            background: linear-gradient(110deg, #f5f0ff, #fff7ed);
            border-bottom: 1px solid #eee8f8;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .team-header h3 {
            margin: 0;
            font-size: 18px;
            overflow-wrap: anywhere;
        }

        .team-count {
            padding: 5px 9px;
            border: 1px solid #ddd6fe;
            border-radius: 999px;
            background: #fff;
            color: #64748b;
            font-size: 13px;
            white-space: nowrap;
        }

        .members-title {
            margin: 0 0 10px;
            color: #475569;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .team-body {
            padding: 16px;
        }

        .members {
            list-style: none;
            padding: 0;
            margin: 0 0 18px;
        }

        .member {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 9px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .member:last-child {
            border-bottom: 0;
        }

        .member-name {
            min-width: 0;
            font-size: 14px;
            overflow-wrap: anywhere;
        }

        .member-email {
            color: #64748b;
            font-size: 12px;
            display: block;
            margin-top: 2px;
        }

        .remove-button {
            min-height: 36px;
            padding: 7px 10px;
            font-size: 12px;
            background: #fee2e2;
            color: #991b1b;
        }

        .remove-button:hover {
            background: #fecaca;
        }

        .add-member {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
        }

        .empty {
            color: #94a3b8;
            font-size: 14px;
            padding: 10px 0;
        }

        .unassigned-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .unassigned-list li {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            overflow-wrap: anywhere;
        }

        .notice {
            margin-top: 14px;
            padding: 13px 15px;
            border: 1px solid;
            border-radius: 11px;
            font-size: 14px;
            line-height: 1.5;
        }

        .notice.is-warning {
            border-color: #fed7aa;
            background: #fff7ed;
            color: #9a3412;
        }

        .notice.is-success {
            border-color: #a7f3d0;
            background: #ecfdf5;
            color: #065f46;
        }

        .next-step-panel {
            border-color: #ddd6fe;
            background: linear-gradient(125deg, #f3e8ff, #fff 58%, #fff7ed);
        }

        .small {
            font-size: 13px;
            color: #64748b;
        }

        .locked {
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        html[data-theme="dark"] body:has(.teams-page) { background: #111827; color: #e5e7eb; }
        html[data-theme="dark"] .teams-page .panel,
        html[data-theme="dark"] .teams-page .team-card { background: #1f2937; border-color: #374151; color: #e5e7eb; }
        html[data-theme="dark"] .teams-page .panel h2,
        html[data-theme="dark"] .teams-page .team-header h3,
        html[data-theme="dark"] .teams-page .summary-stat strong { color: #f3f4f6; }
        html[data-theme="dark"] .teams-page .summary-stat,
        html[data-theme="dark"] .teams-page .unassigned-list li { background: #273449; border-color: #374151; }
        html[data-theme="dark"] .teams-page input,
        html[data-theme="dark"] .teams-page select { background: #111827; color: #f9fafb; border-color: #4b5563; }
        html[data-theme="dark"] .teams-page .team-header { background: linear-gradient(110deg, #30234a, #422c2a); border-color: #374151; }
        html[data-theme="dark"] .teams-page .next-step-panel { background: linear-gradient(125deg, #30234a, #1f2937 58%, #422c2a); }

        @media (max-width: 900px) {
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            body:has(.teams-page) {
                padding: 14px;
            }

            .create-team,
            .randomize-form,
            .add-member {
                grid-template-columns: 1fr;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .workflow ol {
                display: grid;
                gap: 9px;
            }

            .team-header {
                align-items: flex-start;
            }

            .teams-page .header { border-radius: 19px; }
        }
    </style>
@endsection

@section('content')
<div class="teams-container teams-page">

        @php
            $hasParticipations = $activity->participations()->exists();
            $canEditTeams = $activity->status === 'draft' && !$hasParticipations;
            $assignedCount = $students->filter(fn($student) => $assignedStudentIds->contains($student->id))->count();
            $unassignedStudents = $students->filter(fn($student) => !$assignedStudentIds->contains($student->id));
            $unassignedCount = $unassignedStudents->count();
            $emptyTeamCount = $teams->filter(fn($team) => $team->members->isEmpty())->count();
            $teamsReadyForConfiguration = $teams->isNotEmpty() && $unassignedCount === 0;

            $gameConfig = match ($activity->type) {
                'crossword' => [
                    'label' => 'Crucigrama',
                    'configured' => $activity->crossword?->words()->exists() ?? false,
                    'configure' => 'teacher.crossword.configure',
                    'edit' => 'teacher.crossword.edit',
                ],
                'kahoot' => [
                    'label' => 'Kahoot',
                    'configured' => $activity->kahoot?->questions()->exists() ?? false,
                    'configure' => 'teacher.kahoot.configure',
                    'edit' => 'teacher.kahoot.edit',
                ],
                'wordsearch', 'word_search' => [
                    'label' => 'Sopa de letras',
                    'configured' => $activity->wordsearch?->words()->exists() ?? false,
                    'configure' => 'teacher.wordsearch.configure',
                    'edit' => 'teacher.wordsearch.edit',
                ],
                'matching' => [
                    'label' => 'Unir conceptos',
                    'configured' => $activity->matching?->items()->exists() ?? false,
                    'configure' => 'teacher.matching.configure',
                    'edit' => 'teacher.matching.edit',
                ],
                'roulette' => [
                    'label' => 'Ruleta',
                    'configured' => $activity->roulette?->items()->exists() ?? false,
                    'configure' => 'teacher.roulette.configure',
                    'edit' => 'teacher.roulette.edit',
                ],
                default => null,
            };

            $nextGameRoute = $canEditTeams && $gameConfig
                ? route($gameConfig['configured'] ? $gameConfig['edit'] : $gameConfig['configure'], $activity->id)
                : route('teacher.activities.show', $activity->id);
        @endphp

        <a class="klassio-back-button mb-4" href="{{ route('teacher.activities.show', $activity->id) }}">
            ← Volver al resumen de la actividad
        </a>

        <div class="header">
            <p class="eyebrow">Preparación de actividad · Paso 2 de 3</p>
            <h1>Organiza los equipos</h1>

            <p>
                Actividad: <strong>{{ $activity->title }}</strong>
            </p>
        </div>

        @if ($canEditTeams)
            <nav class="workflow" aria-label="Pasos para preparar la actividad">
                <ol>
                    <li class="workflow-step is-complete"><span aria-hidden="true">✓</span> Actividad creada</li>
                    <li class="workflow-step is-current" aria-current="step"><span>2</span> Organizar equipos</li>
                    <li class="workflow-step {{ $teamsReadyForConfiguration ? '' : 'is-blocked' }}">
                        <span>3</span> Configurar {{ $gameConfig['label'] ?? 'juego' }}
                    </li>
                </ol>
            </nav>
        @endif

        <div class="panel" aria-labelledby="team-review-title">
            <h2 id="team-review-title">Resumen de equipos</h2>
            <p class="section-intro">Comprueba cuántos estudiantes están asignados y cuáles faltan por ubicar. Cada cambio se guarda al realizar la acción.</p>

            <div class="summary-grid" aria-label="Estado actual de la distribución">
                <div class="summary-stat">
                    <strong>{{ $teams->count() }}</strong>
                    <span>{{ $teams->count() === 1 ? 'equipo creado' : 'equipos creados' }}</span>
                </div>
                <div class="summary-stat {{ $emptyTeamCount > 0 ? 'is-pending' : '' }}">
                    <strong>{{ $emptyTeamCount }}</strong>
                    <span>{{ $emptyTeamCount === 1 ? 'equipo sin integrantes' : 'equipos sin integrantes' }}</span>
                </div>
                <div class="summary-stat">
                    <strong>{{ $assignedCount }} / {{ $students->count() }}</strong>
                    <span>estudiantes asignados</span>
                </div>
                <div class="summary-stat {{ $unassignedCount > 0 ? 'is-pending' : 'is-clear' }}">
                    <strong>{{ $unassignedCount }}</strong>
                    <span>{{ $unassignedCount === 1 ? 'estudiante pendiente' : 'estudiantes pendientes' }}</span>
                </div>
            </div>

            @if ($unassignedCount > 0)
                <div class="notice is-warning" role="status">
                    Hay estudiantes sin equipo. Revisa la lista al final y asígnalos antes de continuar si deben participar.
                </div>
            @elseif ($teams->isEmpty())
                <div class="notice is-warning" role="status">
                    Aún no hay equipos creados. Puedes crear uno por uno o distribuir al grupo automáticamente.
                </div>
            @elseif ($emptyTeamCount > 0)
                <div class="notice is-warning" role="status">
                    Hay {{ $emptyTeamCount }} {{ $emptyTeamCount === 1 ? 'equipo sin integrantes' : 'equipos sin integrantes' }}. Revisa si deseas agregar estudiantes o eliminarlos.
                </div>
            @else
                <div class="notice is-success" role="status">
                    Todos los estudiantes activos están asignados. Revisa también el tamaño de cada equipo.
                </div>
            @endif

            @if (!$canEditTeams)
                <div class="alert alert-error" role="status" style="margin-top: 14px; margin-bottom: 0;">
                    La composición ya no se puede modificar porque la actividad no está en borrador o ya tiene participaciones.
                </div>
            @endif
        </div>


        {{-- ============================================================
         MENSAJES
         ============================================================ --}}

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- ============================================================
         ACCIONES
         ============================================================ --}}

        @if ($canEditTeams)
        <div class="panel" aria-labelledby="team-actions-title">

            <h2 id="team-actions-title">Crear y distribuir equipos</h2>
            <p class="section-intro">Puedes nombrar los equipos manualmente o repartir a los estudiantes en grupos equilibrados.</p>

            <form method="POST" action="{{ route('teacher.teams.store', $activity->id) }}" class="create-team">
                    @csrf

                    <div class="form-field">
                        <label class="field-label" for="new-team-name">Nombre del equipo</label>
                        <input type="text" name="name" id="new-team-name" placeholder="Ej. Exploradores"
                            value="{{ old('name') }}" required>
                    </div>

                    <button type="submit" class="btn-primary">
                        Crear equipo
                    </button>
            </form>

            <div class="distribution-tools">
                <h3>Distribuir automáticamente</h3>
                <p>Elige cuántos equipos crear. Esta acción reemplaza la distribución actual y pide confirmación antes de aplicarse.</p>
                <form method="POST" action="{{ route('teacher.teams.randomize', $activity->id) }}"
                    class="randomize-form">
                    @csrf

                    <div class="form-field">
                        <label class="field-label" for="team-count">Cantidad de equipos</label>
                        <input type="number" name="team_count" id="team-count" min="1" max="{{ $students->count() }}"
                            placeholder="Ej. 4" required>
                    </div>

                    <button type="submit" class="btn-warning"
                        onclick="return confirm(
                        'Esto reemplazará la distribución actual de equipos. ¿Continuar?'
                    )">
                        Crear equipos equilibrados
                    </button>
                </form>
            </div>

        </div>
        @endif


        {{-- ============================================================
         EQUIPOS
         ============================================================ --}}

        <div class="panel" aria-labelledby="current-teams-title">

            <h2>
                <span id="current-teams-title">Equipos actuales</span>
                <span class="small">({{ $teams->count() }})</span>
            </h2>
            <p class="section-intro">Revisa integrantes y tamaños. Puedes agregar o quitar estudiantes mientras la actividad siga en preparación.</p>

            @if ($teams->isEmpty())

                <div class="notice is-warning" role="status">
                    No hay equipos todavía. Usa “Crear equipo” o “Crear equipos equilibrados” para empezar.
                </div>
            @else
                <div class="teams-grid">

                    @foreach ($teams as $team)
                        <div class="team-card">

                            <div class="team-header">

                                <h3>
                                    {{ $team->name }}
                                </h3>

                                <span class="team-count">
                                    {{ $team->members->count() }}
                                    {{ $team->members->count() === 1 ? 'alumno' : 'alumnos' }}
                                </span>

                            </div>


                            <div class="team-body">

                                {{-- ====================================================
                                 MIEMBROS
                                 ==================================================== --}}

                                @if ($team->members->isEmpty())
                                    <div class="empty">
                                        Este equipo no tiene alumnos.
                                    </div>
                                @else
                                    <p class="members-title">Integrantes</p>
                                    <ul class="members">

                                        @foreach ($team->members as $member)
                                            <li class="member">

                                                <div class="member-name">

                                                    {{ $member->student->name }}

                                                    @if ($member->student->email)
                                                        <span class="member-email">
                                                            {{ $member->student->email }}
                                                        </span>
                                                    @endif

                                                </div>


                                                {{-- Quitar alumno --}}
                                                @if ($canEditTeams)
                                                <form method="POST"
                                                    action="{{ route('teacher.teams.members.remove', [
                                                        'id' => $activity->id,
                                                        'teamId' => $team->id,
                                                        'studentId' => $member->student_id,
                                                    ]) }}">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="remove-button"
                                                        onclick="return confirm(
                                                        '¿Quitar este alumno del equipo?'
                                                    )">
                                                        Quitar
                                                    </button>

                                                </form>
                                                @endif

                                            </li>
                                        @endforeach

                                    </ul>
                                @endif


                                {{-- ====================================================
                                 AGREGAR ALUMNO
                                 ==================================================== --}}

                                @if ($canEditTeams)
                                <form method="POST"
                                    action="{{ route('teacher.teams.members.add', [
                                        'id' => $activity->id,
                                        'teamId' => $team->id,
                                    ]) }}"
                                    class="add-member">

                                    @csrf

                                    <div class="form-field">
                                    <label class="field-label" for="team-student-{{ $team->id }}">Agregar integrante</label>
                                    <select name="student_id" id="team-student-{{ $team->id }}" required>

                                        <option value="">
                                            Seleccionar alumno...
                                        </option>

                                        @foreach ($students as $student)
                                            @if (!$assignedStudentIds->contains($student->id))
                                                <option value="{{ $student->id }}">
                                                    {{ $student->name }}

                                                    @if ($student->email)
                                                        — {{ $student->email }}
                                                    @endif
                                                </option>
                                            @endif
                                        @endforeach

                                    </select>
                                    </div>

                                    <button type="submit" class="btn-primary">
                                        Agregar
                                    </button>

                                </form>
                                @endif


                                {{-- ====================================================
                                 ELIMINAR EQUIPO
                                 ==================================================== --}}

                                @if ($canEditTeams)
                                <form method="POST"
                                    action="{{ route('teacher.teams.destroy', [
                                        'id' => $activity->id,
                                        'teamId' => $team->id,
                                    ]) }}"
                                    style="margin-top: 12px;">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn-danger"
                                        onclick="return confirm(
                                        '¿Eliminar este equipo? También se quitarán sus integrantes.'
                                    )">
                                        Eliminar equipo
                                    </button>

                                </form>
                                @endif

                            </div>

                        </div>
                    @endforeach

                </div>

            @endif

        </div>


        {{-- ============================================================
         ALUMNOS SIN EQUIPO
         ============================================================ --}}

        <div class="panel" aria-labelledby="unassigned-title">

            <h2 id="unassigned-title">Estudiantes sin equipo ({{ $unassignedCount }})</h2>
            <p class="section-intro">Estos estudiantes activos todavía no están asignados a un equipo.</p>

            @if ($unassignedStudents->isEmpty())

                <div class="notice is-success" role="status">
                    Todos los estudiantes activos están incluidos en un equipo.
                </div>
            @else
                <ul class="unassigned-list">

                    @foreach ($unassignedStudents as $student)
                        <li>
                            {{ $student->name }}

                            @if ($student->email)
                                <span class="small">
                                    — {{ $student->email }}
                                </span>
                            @endif
                        </li>
                    @endforeach

                </ul>

            @endif

        </div>

        <section class="panel next-step-panel" aria-labelledby="next-step-title">
            @if ($canEditTeams && $gameConfig)
                <h2 id="next-step-title">Siguiente paso: configurar {{ $gameConfig['label'] }}</h2>
                @if (!$teamsReadyForConfiguration)
                    <p class="small" role="status">
                        @if ($teams->isEmpty())
                            Para continuar, crea al menos un equipo y asigna a todos los estudiantes. Después podrás configurar {{ $gameConfig['label'] }}.
                        @else
                            Asigna a los {{ $unassignedCount === 1 ? 'estudiante pendiente' : 'estudiantes pendientes' }} a un equipo antes de configurar {{ $gameConfig['label'] }}.
                        @endif
                    </p>
                    <a href="{{ $teams->isEmpty() ? '#team-actions-title' : '#current-teams-title' }}" class="btn-primary next-step-link">
                        {{ $teams->isEmpty() ? 'Crear el primer equipo' : 'Asignar estudiantes' }}
                        <span aria-hidden="true">↑</span>
                    </a>
                @else
                    <p class="small">
                        Cuando termines de revisar la distribución, continúa con el contenido del juego.
                        Los equipos se guardan al crear o modificar cada uno.
                    </p>
                    <a href="{{ $nextGameRoute }}" class="btn-primary next-step-link">
                        {{ $gameConfig['configured'] ? 'Continuar a la configuración' : 'Siguiente: configurar el juego' }}
                        <span aria-hidden="true">→</span>
                    </a>
                @endif
            @else
                <h2 id="next-step-title">Preparación de equipos</h2>
                <p class="small">
                    Esta actividad ya no está en preparación. Puedes regresar al resumen para consultar sus acciones disponibles.
                </p>
                <a href="{{ route('teacher.activities.show', $activity->id) }}" class="klassio-back-button">
                    Volver al resumen de actividad
                </a>
            @endif
        </section>

    </div>
@endsection
