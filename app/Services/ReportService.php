<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Participation;

class ReportService
{
    /**
     * Obtiene el resumen general de una actividad.
     *
     * El estado se determina por estudiante y no por intento.
     *
     * Prioridad:
     * completed
     * started
     * expired
     * abandoned
     * not_participated
     */
    public function getActivitySummary(Activity $activity): array
    {
        $participations = Participation::query()
            ->where('activity_id', $activity->id)
            ->get();

        $totalStudents = $activity->schoolClass
            ->enrollments()
            ->where('status', 'active')
            ->count();

        /*
         * Agrupamos las participaciones por estudiante.
         */
        $byStudent = $participations
            ->filter(fn (Participation $participation) =>
                $participation->student_id !== null
            )
            ->groupBy('student_id');

        /*
         * Un estudiante cuenta como participante si
         * tiene al menos una participación.
         */
        $participated = $byStudent->count();

        /*
         * Determinamos un único estado para cada estudiante.
         *
         * Esto NO cambia aunque tenga múltiples intentos.
         */
        $studentStatuses = $byStudent->map(
            fn ($studentParticipations) =>
                $this->resolveStudentStatus($studentParticipations)
        );

        $completed = $studentStatuses
            ->where('completed')
            ->count();

        $started = $studentStatuses
            ->where('started')
            ->count();

        $expired = $studentStatuses
            ->where('expired')
            ->count();

        $abandoned = $studentStatuses
            ->where('abandoned')
            ->count();

        $notParticipated = max(
            0,
            $totalStudents - $participated
        );

        /*
         * --------------------------------------------
         * RENDIMIENTO
         * --------------------------------------------
         *
         * Para el rendimiento tomamos un solo resultado
         * por estudiante: su mayor puntuación.
         *
         * Se consideran:
         * - completed
         * - expired
         * - abandoned
         *
         * Siempre que tengan score.
         *
         * Un estudiante nunca se cuenta dos veces.
         */
        $bestResults = $byStudent
            ->map(function ($studentParticipations) {

                return $studentParticipations
                    ->filter(fn (Participation $participation) =>
                        in_array(
                            $participation->status,
                            ['completed', 'expired', 'abandoned']
                        )
                        && $participation->score !== null
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
                         * Si empatan, tomamos el menor tiempo.
                         */
                        return ($a->elapsed_seconds ?? PHP_INT_MAX)
                            <=> ($b->elapsed_seconds ?? PHP_INT_MAX);
                    })
                    ->first();

            })
            ->filter();

        $scores = $bestResults
            ->pluck('score');

        $times = $bestResults
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
     * Determina el estado global de un estudiante.
     *
     * Un estudiante puede tener múltiples intentos,
     * pero solamente tendrá un estado en el resumen.
     */
    protected function resolveStudentStatus($participations): string
    {
        /*
         * Si alguna vez completó, permanece como completado.
         */
        if ($participations->contains(
            fn (Participation $participation) =>
                $participation->status === 'completed'
        )) {
            return 'completed';
        }

        /*
         * Si no completó pero tiene un intento activo.
         */
        if ($participations->contains(
            fn (Participation $participation) =>
                $participation->status === 'started'
        )) {
            return 'started';
        }

        /*
         * Si no completó ni está activo, pero expiró.
         */
        if ($participations->contains(
            fn (Participation $participation) =>
                $participation->status === 'expired'
        )) {
            return 'expired';
        }

        /*
         * Si solamente abandonó sus intentos.
         */
        if ($participations->contains(
            fn (Participation $participation) =>
                $participation->status === 'abandoned'
        )) {
            return 'abandoned';
        }

        return 'not_participated';
    }
}