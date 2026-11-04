<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Calificación general de una clase.
 *
 * Reglas:
 * - Cuentan las actividades publicadas y cerradas.
 * - Cuentan las jugadas terminadas: completed, abandoned y expired (con su
 *   puntaje; null vale 0). Los intentos en curso (waiting/started) no cuentan.
 * - Nota de actividad = la ÚLTIMA jugada del alumno (o de su equipo, en modo
 *   team), por orden de creación, sea individual o de sala multijugador
 *   (misma actividad, una sola nota). Se normaliza a 0-100 según max_score.
 * - Nota de unidad = promedio de las notas con puntaje de sus actividades.
 *   Las unidades sin puntajes no pesan (nota null).
 * - Actividad ya vencida (due_at pasado, o cerrada) sin jugada del alumno:
 *   cuenta como 0. Mientras siga vigente, no pesa.
 * - Calificación final = promedio de las notas de las unidades con nota.
 * - Las actividades sin unidad forman el grupo adicional "Sin unidad".
 */
class GradingService
{
    /** Clave del grupo de actividades sin unidad en las estructuras planas. */
    public const WITHOUT_UNIT_KEY = 0;

    /** Nombre visible del grupo de actividades sin unidad. */
    public const WITHOUT_UNIT_NAME = 'Sin unidad';

    /**
     * Calcula la tabla de calificaciones de una clase.
     *
     * @return array{
     *   units: list<array{id: int|null, key: int, name: string, number: int|null, activities: list<array{id: int, title: string, max_score: mixed, mode: string}>}>,
     *   students: list<array{student: User, activityScores: array<int, float|null>, activityRaw: array<int, int|float>|array<int, null>, unitScores: array<int, float|null>, final: float|null}>,
     * }
     */
    public function forClass(SchoolClass $class): array
    {
        $units = $class->units()->orderBy('number')->get();
        $activities = $class->activities()
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('id')
            ->get();

        $latest = $this->latestParticipations($activities);

        $unitGroups = [];

        foreach ($units as $unit) {
            $unitGroups[$unit->id] = [
                'id' => $unit->id,
                'key' => $unit->id,
                'name' => $unit->name !== '' && $unit->name !== null
                    ? $unit->name
                    : 'Unidad '.$unit->number,
                'number' => $unit->number,
                'activities' => [],
            ];
        }

        $unitGroups[self::WITHOUT_UNIT_KEY] = [
            'id' => null,
            'key' => self::WITHOUT_UNIT_KEY,
            'name' => self::WITHOUT_UNIT_NAME,
            'number' => null,
            'activities' => [],
        ];

        foreach ($activities as $activity) {
            $key = $activity->unit_id ?? self::WITHOUT_UNIT_KEY;

            if (! isset($unitGroups[$key])) {
                $key = self::WITHOUT_UNIT_KEY;
            }

            $unitGroups[$key]['activities'][] = [
                'id' => $activity->id,
                'title' => $activity->title,
                'max_score' => $activity->max_score,
                'mode' => $activity->mode,
            ];
        }

        $groups = array_values($unitGroups);

        $students = $class->enrollments()
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->map(fn ($enrollment) => $enrollment->student)
            ->filter()
            ->values();

        $rows = [];

        foreach ($students as $student) {
            $activityScores = [];
            $activityRaw = [];

            foreach ($activities as $activity) {
                $participation = $latest[$activity->id][$student->id] ?? null;
                $max = (float) $activity->max_score;

                if ($participation !== null && $max > 0) {
                    $activityRaw[$activity->id] = $participation->score ?? 0;
                    $activityScores[$activity->id] = round(($participation->score ?? 0) / $max * 100, 2);

                    continue;
                }

                // Sin jugada (o max_score 0) y la actividad ya venció: cuenta 0.
                if ($max > 0 && $this->isOverdue($activity)) {
                    $activityRaw[$activity->id] = 0;
                    $activityScores[$activity->id] = 0.0;
                }
            }

            $unitScores = [];

            foreach ($groups as $group) {
                $scores = [];

                foreach ($group['activities'] as $activity) {
                    $score = $activityScores[$activity['id']] ?? null;

                    if ($score !== null) {
                        $scores[] = $score;
                    }
                }

                $unitScores[$group['key']] = $scores === []
                    ? null
                    : round(array_sum($scores) / count($scores), 2);
            }

            $withScore = array_values(array_filter($unitScores, fn ($score) => $score !== null));

            $rows[] = [
                'student' => $student,
                'activityScores' => $activityScores,
                'activityRaw' => $activityRaw,
                'unitScores' => $unitScores,
                'final' => $withScore === []
                    ? null
                    : round(array_sum($withScore) / count($withScore), 2),
            ];
        }

        return [
            'units' => $groups,
            'students' => $rows,
        ];
    }

    /**
     * Para cada actividad, la ÚLTIMA jugada terminada por alumno:
     * completed/abandoned/expired, elegida por orden de creación (id más alto).
     *
     * @param  Collection<int, Activity>  $activities
     * @return array<int, array<int, Participation>>
     */
    private function latestParticipations(Collection $activities): array
    {
        if ($activities->isEmpty()) {
            return [];
        }

        $participations = Participation::query()
            ->whereIn('activity_id', $activities->pluck('id'))
            ->whereIn('status', ['completed', 'abandoned', 'expired'])
            ->get();

        // En modo team la nota del alumno es la de su equipo (el equipo aparece
        // en sus participaciones jugadas o en su inscripción a equipos).
        $teamIdsByActivity = Team::query()
            ->whereIn('activity_id', $activities->pluck('id'))
            ->pluck('activity_id', 'id');

        $teamMemberships = [];

        if ($teamIdsByActivity->isNotEmpty()) {
            $teamMemberships = TeamMember::query()
                ->whereIn('team_id', $teamIdsByActivity->keys())
                ->get()
                ->groupBy('student_id')
                ->map(fn (Collection $members) => $members->keyBy('team_id'));
        }

        $latest = [];

        foreach ($activities as $activity) {
            $activityParticipations = $participations->where('activity_id', $activity->id);

            if ($activity->mode === 'team') {
                $latestByTeam = [];

                $activityParticipations
                    ->filter(fn (Participation $participation) => $participation->team_id !== null)
                    ->groupBy('team_id')
                    ->each(function (Collection $teamParticipations, int $teamId) use (&$latestByTeam): void {
                        $teamLatest = $teamParticipations->sortByDesc('id')->first();

                        if ($teamLatest !== null) {
                            $latestByTeam[$teamId] = $teamLatest;
                        }
                    });

                $studentTeamByActivity = [];

                foreach ($teamMemberships as $studentId => $teams) {
                    foreach ($teams as $teamId => $member) {
                        $activityId = (int) $teamIdsByActivity[$teamId];

                        if ($activityId === $activity->id) {
                            $studentTeamByActivity[$studentId] = $teamId;
                        }
                    }
                }

                foreach ($studentTeamByActivity as $studentId => $teamId) {
                    if (isset($latestByTeam[$teamId])) {
                        $latest[$activity->id][$studentId] = $latestByTeam[$teamId];
                    }
                }

                continue;
            }

            $activityParticipations
                ->filter(fn (Participation $participation) => $participation->student_id !== null)
                ->groupBy('student_id')
                ->each(function (Collection $studentParticipations, int $studentId) use (&$latest, $activity): void {
                    $latest[$activity->id][$studentId] = $studentParticipations->sortByDesc('id')->first();
                });
        }

        return $latest;
    }

    /**
     * Una actividad cuenta como 0 para quien no la jugó si el profesor la
     * cerró o si su fecha límite ya pasó.
     */
    private function isOverdue(Activity $activity): bool
    {
        return $activity->status === 'closed'
            || ($activity->due_at !== null && now()->isAfter($activity->due_at));
    }
}
