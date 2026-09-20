<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTeamMemberRequest;
use App\Http\Requests\RandomizeTeamsRequest;
use App\Http\Requests\StoreTeamRequest;
use App\Models\Activity;
use App\Models\Team;
use App\Models\User;
use App\Services\RandomSelectionService;
use App\Services\TeamService;
use Illuminate\Support\Facades\Gate;
use RuntimeException;

class TeamController extends Controller
{
    public function __construct(
        protected TeamService $teamService,
        protected RandomSelectionService $randomSelection
    ) {}

    /**
     * Mostrar y administrar los equipos de una actividad.
     */
    public function index(int $id)
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('view', $activity);

        if ($activity->mode !== 'team') {
            abort(
                404,
                'Esta actividad no está configurada en modo equipo.'
            );
        }

        $teams = $this->teamService
            ->getTeams($activity);

        /*
         * Obtener alumnos activos de la clase.
         */
        $students = $activity->schoolClass
            ->enrollments()
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->values();

        /*
         * Alumnos que ya pertenecen a un equipo
         * dentro de esta actividad.
         */
        $assignedStudentIds = $teams
            ->flatMap(
                fn($team) =>
                $team->members->pluck('student_id')
            )
            ->unique()
            ->values();

        return view(
            'teacher.teams.index',
            compact(
                'activity',
                'teams',
                'students',
                'assignedStudentIds'
            )
        );
    }

    /**
     * Crear un equipo manualmente.
     */
    public function store(
        StoreTeamRequest $request,
        int $id
    ) {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        try {
            $this->teamService->create(
                $activity,
                $request->validated()['name']
            );
        } catch (RuntimeException $e) {
            return back()
                ->withErrors([
                    'name' => $e->getMessage(),
                ])
                ->withInput();
        }

        return back()->with(
            'success',
            'Equipo creado correctamente.'
        );
    }

    /**
     * Agregar un alumno a un equipo.
     */
    public function addMember(
        AddTeamMemberRequest $request,
        int $id,
        int $teamId
    ) {
        $activity = Activity::findOrFail($id);
        $team = Team::findOrFail($teamId);

        Gate::authorize('update', $activity);

        try {
            $student = User::findOrFail(
                $request->validated()['student_id']
            );

            $this->teamService->addMember(
                $activity,
                $team,
                $student
            );
        } catch (RuntimeException $e) {
            return back()->withErrors([
                'student_id' => $e->getMessage(),
            ]);
        }

        return back()->with(
            'success',
            'Alumno agregado al equipo.'
        );
    }

    /**
     * Quitar un alumno de un equipo.
     */
    public function removeMember(
        int $id,
        int $teamId,
        int $studentId
    ) {
        $activity = Activity::findOrFail($id);
        $team = Team::findOrFail($teamId);
        $student = User::findOrFail($studentId);

        Gate::authorize('update', $activity);

        try {
            $this->teamService->removeMember(
                $activity,
                $team,
                $student
            );
        } catch (RuntimeException $e) {
            return back()->withErrors([
                'team' => $e->getMessage(),
            ]);
        }

        return back()->with(
            'success',
            'Alumno eliminado del equipo.'
        );
    }

    /**
     * Eliminar un equipo.
     */
    public function destroy(
        int $id,
        int $teamId
    ) {
        $activity = Activity::findOrFail($id);
        $team = Team::findOrFail($teamId);

        Gate::authorize('update', $activity);

        try {
            $this->teamService->delete(
                $activity,
                $team
            );
        } catch (RuntimeException $e) {
            return back()->withErrors([
                'team' => $e->getMessage(),
            ]);
        }

        return back()->with(
            'success',
            'Equipo eliminado correctamente.'
        );
    }

    /**
     * Generar equipos aleatoriamente.
     *
     * La distribución es equilibrada.
     *
     * Ejemplos:
     *
     * 17 alumnos / 2 equipos = 9 + 8
     * 17 alumnos / 3 equipos = 6 + 6 + 5
     * 17 alumnos / 4 equipos = 5 + 4 + 4 + 4
     */
    public function randomize(
        RandomizeTeamsRequest $request,
        int $id
    ) {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        try {
            $teams = $this->teamService->randomize(
                $activity,
                (int) $request->validated()['team_count']
            );
        } catch (RuntimeException $e) {
            return back()->withErrors([
                'team_count' => $e->getMessage(),
            ]);
        }

        return back()->with(
            'success',
            "Se generaron {$teams->count()} equipos de forma equilibrada."
        );
    }

    /**
     * Seleccionar un estudiante aleatoriamente.
     */
    public function randomStudent(int $id)
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('view', $activity);

        $student = $this->randomSelection
            ->randomStudent($activity);

        if ($student === null) {
            return back()->withErrors([
                'random' =>
                'No hay estudiantes activos en la clase.'
            ]);
        }

        return back()->with(
            'random_student',
            [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
            ]
        );
    }

    /**
     * Seleccionar un equipo aleatoriamente.
     */
    public function randomTeam(int $id)
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('view', $activity);

        if ($activity->mode !== 'team') {
            return back()->withErrors([
                'random' =>
                'Esta actividad no está configurada en modo equipo.'
            ]);
        }

        $team = $this->randomSelection
            ->randomTeam($activity);

        if ($team === null) {
            return back()->withErrors([
                'random' =>
                'No hay equipos creados en esta actividad.'
            ]);
        }

        return back()->with(
            'random_team',
            [
                'id' => $team->id,
                'name' => $team->name,
            ]
        );
    }
}
