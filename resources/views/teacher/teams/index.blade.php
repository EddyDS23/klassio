<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Equipos — {{ $activity->title }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0 0 6px;
            font-size: 28px;
        }

        .header p {
            margin: 0;
            color: #64748b;
        }

        .back {
            display: inline-block;
            margin-bottom: 16px;
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
        }

        .back:hover {
            text-decoration: underline;
        }

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
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .panel h2 {
            margin: 0 0 16px;
            font-size: 19px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        form {
            margin: 0;
        }

        input,
        select {
            width: 100%;
            padding: 9px 11px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
            background: white;
        }

        button {
            border: 0;
            border-radius: 7px;
            padding: 9px 14px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
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
            background: #d97706;
            color: white;
        }

        .btn-warning:hover {
            background: #b45309;
        }

        .create-team {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
        }

        .randomize-form {
            display: grid;
            grid-template-columns: 180px auto;
            gap: 10px;
            max-width: 500px;
        }

        .teams-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 18px;
        }

        .team-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .team-header {
            padding: 16px;
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .team-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .team-count {
            color: #64748b;
            font-size: 13px;
            white-space: nowrap;
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
            font-size: 14px;
        }

        .member-email {
            color: #64748b;
            font-size: 12px;
            display: block;
            margin-top: 2px;
        }

        .remove-button {
            padding: 5px 8px;
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

        .random-result {
            margin-top: 14px;
            padding: 14px;
            border-radius: 8px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }

        .random-result strong {
            display: block;
            font-size: 18px;
            margin-top: 4px;
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

        @media (max-width: 600px) {
            body {
                padding: 14px;
            }

            .create-team,
            .randomize-form,
            .add-member {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <a href="{{ route("teacher.activities.show",$activity->id) }}">
            ← Volver
        </a>

        <div class="header">
            <h1>Equipos</h1>

            <p>
                Actividad:
                <strong>{{ $activity->title }}</strong>
            </p>
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
         RANDOM RESULT
         ============================================================ --}}

        @if (session('random_student'))
            <div class="random-result">
                Alumno seleccionado aleatoriamente:

                <strong>
                    {{ session('random_student')['name'] }}
                </strong>
            </div>
        @endif

        @if (session('random_team'))
            <div class="random-result">
                Equipo seleccionado aleatoriamente:

                <strong>
                    {{ session('random_team')['name'] }}
                </strong>
            </div>
        @endif


        {{-- ============================================================
         ACCIONES
         ============================================================ --}}

        <div class="panel">

            <h2>Administrar equipos</h2>

            <div class="actions">

                {{-- Crear equipo --}}
                <form method="POST" action="{{ route('teacher.teams.store', $activity->id) }}" class="create-team"
                    style="width: 100%;">
                    @csrf

                    <input type="text" name="name" placeholder="Nombre del nuevo equipo"
                        value="{{ old('name') }}" required>

                    <button type="submit" class="btn-primary">
                        Crear equipo
                    </button>
                </form>


                {{-- Randomizar equipos --}}
                <form method="POST" action="{{ route('teacher.teams.randomize', $activity->id) }}"
                    class="randomize-form" style="margin-top: 14px;">
                    @csrf

                    <input type="number" name="team_count" min="1" max="{{ $students->count() }}"
                        placeholder="Cantidad de equipos" required>

                    <button type="submit" class="btn-warning"
                        onclick="return confirm(
                        'Esto reemplazará la distribución actual de equipos. ¿Continuar?'
                    )">
                        Randomizar equipos
                    </button>
                </form>

            </div>

        </div>


        {{-- ============================================================
         SELECCIÓN ALEATORIA
         ============================================================ --}}

        <div class="panel">

            <h2>Selección aleatoria</h2>

            <p class="small">
                Puedes seleccionar aleatoriamente un alumno o un equipo
                para dinámicas en clase.
            </p>

            <div class="actions">

                <form method="POST" action="{{ route('teacher.teams.random-student', $activity->id) }}">
                    @csrf

                    <button type="submit" class="btn-secondary">
                        Elegir alumno aleatorio
                    </button>
                </form>


                <form method="POST" action="{{ route('teacher.teams.random-team', $activity->id) }}">
                    @csrf

                    <button type="submit" class="btn-secondary">
                        Elegir equipo aleatorio
                    </button>
                </form>

            </div>

        </div>


        {{-- ============================================================
         EQUIPOS
         ============================================================ --}}

        <div class="panel">

            <h2>
                Equipos actuales
                <span class="small">
                    ({{ $teams->count() }})
                </span>
            </h2>

            @if ($teams->isEmpty())

                <div class="empty">
                    No hay equipos creados todavía.
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

                                            </li>
                                        @endforeach

                                    </ul>
                                @endif


                                {{-- ====================================================
                                 AGREGAR ALUMNO
                                 ==================================================== --}}

                                <form method="POST"
                                    action="{{ route('teacher.teams.members.add', [
                                        'id' => $activity->id,
                                        'teamId' => $team->id,
                                    ]) }}"
                                    class="add-member">

                                    @csrf

                                    <select name="student_id" required>

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

                                    <button type="submit" class="btn-primary">
                                        Agregar
                                    </button>

                                </form>


                                {{-- ====================================================
                                 ELIMINAR EQUIPO
                                 ==================================================== --}}

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

                            </div>

                        </div>
                    @endforeach

                </div>

            @endif

        </div>


        {{-- ============================================================
         ALUMNOS SIN EQUIPO
         ============================================================ --}}

        <div class="panel">

            <h2>Alumnos sin equipo</h2>

            @php
                $unassignedStudents = $students->filter(fn($student) => !$assignedStudentIds->contains($student->id));
            @endphp

            @if ($unassignedStudents->isEmpty())

                <div class="empty">
                    Todos los alumnos activos tienen un equipo.
                </div>
            @else
                <ul style="margin: 0; padding-left: 20px;">

                    @foreach ($unassignedStudents as $student)
                        <li style="margin-bottom: 6px;">
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

    </div>

</body>

</html>
