<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TeamService
{
    public function __construct(
        protected RandomSelectionService $randomSelection
    ) {}

    /**
     * Obtener equipos de una actividad.
     */
    public function getTeams(Activity $activity): Collection
    {
        return $activity->teams()
            ->with('members.student')
            ->get();
    }

    /**
     * Crear un equipo manualmente.
     */
    public function create(
        Activity $activity,
        string $name
    ): Team {
        $this->ensureTeamMode($activity);
        $this->ensureNoParticipation($activity);

        $name = trim($name);

        if ($name === '') {
            throw new RuntimeException(
                'El nombre del equipo es obligatorio.'
            );
        }

        return $activity->teams()->create([
            'name' => $name,
        ]);
    }

    /**
     * Agregar alumno manualmente.
     */
    public function addMember(
        Activity $activity,
        Team $team,
        User $student
    ): TeamMember {
        $this->validateTeam($activity, $team);
        $this->ensureTeamMode($activity);
        $this->ensureNoParticipation($activity);

        if ($student->role !== 'student') {
            throw new RuntimeException(
                'El usuario seleccionado no es un alumno.'
            );
        }

        /*
         * El alumno debe estar inscrito activamente
         * en la clase de la actividad.
         */
        $enrolled = $activity->schoolClass
            ->enrollments()
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->exists();

        if (! $enrolled) {
            throw new RuntimeException(
                'El alumno no está inscrito activamente en la clase.'
            );
        }

        /*
         * Un alumno solamente puede pertenecer
         * a un equipo dentro de esta actividad.
         */
        $alreadyAssigned = TeamMember::query()
            ->where('student_id', $student->id)
            ->whereHas(
                'team',
                fn ($query) => $query->where(
                    'activity_id',
                    $activity->id
                )
            )
            ->exists();

        if ($alreadyAssigned) {
            throw new RuntimeException(
                'El alumno ya pertenece a un equipo de esta actividad.'
            );
        }

        return TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $student->id,
        ]);
    }

    /**
     * Quitar alumno de un equipo.
     */
    public function removeMember(
        Activity $activity,
        Team $team,
        User $student
    ): void {
        $this->validateTeam($activity, $team);
        $this->ensureTeamMode($activity);
        $this->ensureNoParticipation($activity);

        TeamMember::query()
            ->where('team_id', $team->id)
            ->where('student_id', $student->id)
            ->delete();
    }

    /**
     * Eliminar un equipo.
     */
    public function delete(
        Activity $activity,
        Team $team
    ): void {
        $this->validateTeam($activity, $team);
        $this->ensureTeamMode($activity);
        $this->ensureNoParticipation($activity);

        DB::transaction(function () use ($team) {
            /*
             * Eliminamos primero los miembros para evitar
             * problemas de integridad referencial si la BD
             * no tiene cascade configurado.
             */
            $team->members()->delete();

            $team->delete();
        });
    }

    /**
     * Generar equipos aleatorios y equilibrados.
     *
     * Ejemplos:
     *
     * 17 alumnos / 2 equipos = 9 + 8
     * 17 alumnos / 3 equipos = 6 + 6 + 5
     * 17 alumnos / 4 equipos = 5 + 4 + 4 + 4
     */
    public function randomize(
        Activity $activity,
        int $teamCount
    ): Collection {
        $this->ensureTeamMode($activity);
        $this->ensureNoParticipation($activity);

        if ($teamCount < 1) {
            throw new RuntimeException(
                'La cantidad de equipos debe ser al menos 1.'
            );
        }

        /*
         * Obtener únicamente alumnos activos
         * de la clase de la actividad.
         */
        $students = $activity->schoolClass
            ->enrollments()
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->values();

        if ($students->isEmpty()) {
            throw new RuntimeException(
                'No hay estudiantes activos en la clase.'
            );
        }

        /*
         * No puede haber más equipos que alumnos.
         */
        if ($teamCount > $students->count()) {
            throw new RuntimeException(
                'La cantidad de equipos no puede ser mayor que la cantidad de estudiantes.'
            );
        }

        /*
         * RandomSelectionService se encarga de:
         *
         * 1. Mezclar aleatoriamente los alumnos.
         * 2. Distribuirlos de forma equilibrada.
         */
        $groups = $this->randomSelection
            ->distributeEqually(
                $students,
                $teamCount
            );

        return DB::transaction(function () use (
            $activity,
            $groups
        ) {
            /*
             * Eliminamos la distribución anterior.
             *
             * Esto permite generar una combinación completamente
             * nueva mientras todavía no existan participaciones.
             */
            $activity->teams()
                ->get()
                ->each(function (Team $team) {
                    $team->members()->delete();
                    $team->delete();
                });

            $teams = collect();

            foreach ($groups as $index => $studentsGroup) {
                $team = $activity->teams()->create([
                    'name' => 'Equipo ' . ($index + 1),
                ]);

                foreach ($studentsGroup as $student) {
                    $team->members()->create([
                        'student_id' => $student->id,
                    ]);
                }

                $teams->push(
                    $team->load('members.student')
                );
            }

            return $teams;
        });
    }

    /**
     * Verifica que la actividad esté configurada
     * en modo equipo.
     */
    private function ensureTeamMode(
        Activity $activity
    ): void {
        if ($activity->mode !== 'team') {
            throw new RuntimeException(
                'Esta actividad no está configurada en modo equipo.'
            );
        }
    }

    /**
     * Impide modificar la estructura de equipos
     * después de que exista cualquier participación.
     *
     * Incluso una participación abandoned/expired
     * mantiene bloqueada la estructura para preservar
     * los resultados históricos.
     */
    private function ensureNoParticipation(
        Activity $activity
    ): void {
        $hasParticipation = $activity
            ->participations()
            ->exists();

        if ($hasParticipation) {
            throw new RuntimeException(
                'Los equipos ya no pueden modificarse porque la actividad tiene participaciones.'
            );
        }
    }

    /**
     * Verifica que el equipo pertenezca
     * a la actividad indicada.
     */
    private function validateTeam(
        Activity $activity,
        Team $team
    ): void {
        if (
            (int) $team->activity_id !==
            (int) $activity->id
        ) {
            throw new RuntimeException(
                'El equipo no pertenece a esta actividad.'
            );
        }
    }
}