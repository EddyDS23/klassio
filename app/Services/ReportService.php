<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Participation;

class ReportService
{
    /**
     * Obtiene el resumen general de una actividad.
     */
    public function getActivitySummary(Activity $activity): array
    {
        $participations = Participation::where(
            'activity_id',
            $activity->id
        )->get();

        /*
         * =========================================================
         * MODO TEAM
         * =========================================================
         */
        if ($activity->mode === 'team') {
            return $this->getTeamSummary(
                $activity,
                $participations
            );
        }

        /*
         * =========================================================
         * MODO INDIVIDUAL
         * =========================================================
         *
         * Esta parte conserva la lógica que ya funcionaba.
         */

        $totalStudents = $activity->schoolClass
            ->enrollments()
            ->where('status', 'active')
            ->count();

        $participated = $participations
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->count();

        $completed = $participations
            ->where('status', 'completed')
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->count();

        $started = $participations
            ->where('status', 'started')
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->count();

        $abandoned = $participations
            ->where('status', 'abandoned')
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->count();

        $expired = $participations
            ->where('status', 'expired')
            ->pluck('student_id')
            ->filter()
            ->unique()
            ->count();

        $notParticipated = max(
            0,
            $totalStudents - $participated
        );

        /*
         * Para rendimiento usamos únicamente
         * participaciones completadas.
         */
        $completedParticipations = $participations
            ->where('status', 'completed');

        $scores = $completedParticipations
            ->pluck('score')
            ->filter(fn ($score) => $score !== null);

        $times = $completedParticipations
            ->pluck('elapsed_seconds')
            ->filter(fn ($time) => $time !== null);

        return [
            'total_students' => $totalStudents,

            'participated' => $participated,

            'not_participated' => $notParticipated,

            'completed' => $completed,

            'started' => $started,

            'abandoned' => $abandoned,

            'expired' => $expired,

            'average_score' => $scores->isNotEmpty()
                ? round($scores->avg(), 2)
                : null,

            'best_score' => $scores->isNotEmpty()
                ? $scores->max()
                : null,

            'average_time' => $times->isNotEmpty()
                ? round($times->avg())
                : null,
        ];
    }

    /**
     * Obtiene el resumen para una actividad en modo team.
     */
    protected function getTeamSummary(
        Activity $activity,
        $participations
    ): array {
        /*
         * Los equipos pertenecen directamente a la actividad.
         */
        $totalTeams = $activity->teams()->count();

        /*
         * Participaciones de equipos.
         *
         * Una participación de team tiene:
         *
         * student_id = null
         * team_id    = ID del equipo
         */
        $teamParticipations = $participations
            ->filter(fn (Participation $participation) =>
                $participation->team_id !== null
            );

        /*
         * Cada equipo cuenta una sola vez.
         */
        $participated = $teamParticipations
            ->pluck('team_id')
            ->unique()
            ->count();

        /*
         * =========================================================
         * ESTADOS
         * =========================================================
         */

        $completed = $teamParticipations
            ->where('status', 'completed')
            ->pluck('team_id')
            ->unique()
            ->count();

        $started = $teamParticipations
            ->where('status', 'started')
            ->pluck('team_id')
            ->unique()
            ->count();

        $abandoned = $teamParticipations
            ->where('status', 'abandoned')
            ->pluck('team_id')
            ->unique()
            ->count();

        $expired = $teamParticipations
            ->where('status', 'expired')
            ->pluck('team_id')
            ->unique()
            ->count();

        $notParticipated = max(
            0,
            $totalTeams - $participated
        );

        /*
         * =========================================================
         * RENDIMIENTO
         * =========================================================
         *
         * Para mantener la misma lógica que el reporte individual,
         * aquí usamos participaciones completadas.
         *
         * Pero evitamos contar varias veces al mismo equipo.
         *
         * Si un equipo tiene varios intentos, tomamos el mejor.
         */

        $bestByTeam = $teamParticipations
            ->where('status', 'completed')
            ->groupBy('team_id')
            ->map(function ($teamAttempts) {

                return $teamAttempts
                    ->filter(fn (Participation $participation) =>
                        $participation->score !== null
                    )
                    ->sort(function (
                        Participation $a,
                        Participation $b
                    ) {
                        /*
                         * Mayor puntuación primero.
                         */
                        if ($a->score !== $b->score) {
                            return $b->score <=> $a->score;
                        }

                        /*
                         * En empate, menor tiempo.
                         */
                        return ($a->elapsed_seconds ?? PHP_INT_MAX)
                            <=> ($b->elapsed_seconds ?? PHP_INT_MAX);
                    })
                    ->first();
            })
            ->filter();

        $scores = $bestByTeam
            ->pluck('score');

        $times = $bestByTeam
            ->pluck('elapsed_seconds')
            ->filter(fn ($time) => $time !== null);

        return [
            /*
             * La vista de reporte utilizará total_teams
             * cuando la actividad sea team.
             */
            'total_teams' => $totalTeams,

            'participated' => $participated,

            'not_participated' => $notParticipated,

            'completed' => $completed,

            'started' => $started,

            'abandoned' => $abandoned,

            'expired' => $expired,

            'average_score' => $scores->isNotEmpty()
                ? round($scores->avg(), 2)
                : null,

            'best_score' => $scores->isNotEmpty()
                ? $scores->max()
                : null,

            'average_time' => $times->isNotEmpty()
                ? round($times->avg())
                : null,
        ];
    }
}