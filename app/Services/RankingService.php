<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Participation;
use Illuminate\Support\Collection;

class RankingService
{
    

    /**
     * Obtiene el ranking de una actividad.
     *
     * Reglas:
     * - Solo participaciones completadas.
     * - Se toma la mejor participación de cada estudiante/equipo.
     * - Mayor puntuación primero.
     * - En empate, menor tiempo primero.
     */
    public function getRanking(Activity $activity): Collection
    {
        $participations = Participation::query()
            ->where('activity_id', $activity->id)
            ->where('status', 'completed')
            ->with([
                'student:id,name,email',
                'team:id,name',
            ])
            ->get();

        if ($activity->mode === 'team') {
            return $this->buildTeamRanking($participations);
        }

        return $this->buildIndividualRanking($participations);
    }

    /**
     * Ranking individual.
     */
    protected function buildIndividualRanking(
        Collection $participations
    ): Collection {
        return $participations
            ->filter(fn (Participation $participation) =>
                $participation->student_id !== null
            )
            ->groupBy('student_id')
            ->map(function (Collection $studentParticipations) {
                return $studentParticipations
                    ->sortBy([
                        ['score', 'desc'],
                        ['elapsed_seconds', 'asc'],
                    ])
                    ->first();
            })
            ->sort(function (
                Participation $a,
                Participation $b
            ) {
                if ($a->score !== $b->score) {
                    return $b->score <=> $a->score;
                }

                return ($a->elapsed_seconds ?? PHP_INT_MAX)
                    <=> ($b->elapsed_seconds ?? PHP_INT_MAX);
            })
            ->values()
            ->map(function (
                Participation $participation,
                int $index
            ) {
                return $this->formatEntry(
                    $participation,
                    $index + 1
                );
            });
    }

    /**
     * Ranking por equipos.
     *
     * Un equipo aparece una sola vez aunque tenga varios integrantes.
     */
    protected function buildTeamRanking(
        Collection $participations
    ): Collection {
        return $participations
            ->filter(fn (Participation $participation) =>
                $participation->team_id !== null
            )
            ->groupBy('team_id')
            ->map(function (Collection $teamParticipations) {
                return $teamParticipations
                    ->sortBy([
                        ['score', 'desc'],
                        ['elapsed_seconds', 'asc'],
                    ])
                    ->first();
            })
            ->sort(function (
                Participation $a,
                Participation $b
            ) {
                if ($a->score !== $b->score) {
                    return $b->score <=> $a->score;
                }

                return ($a->elapsed_seconds ?? PHP_INT_MAX)
                    <=> ($b->elapsed_seconds ?? PHP_INT_MAX);
            })
            ->values()
            ->map(function (
                Participation $participation,
                int $index
            ) {
                return $this->formatEntry(
                    $participation,
                    $index + 1
                );
            });
    }

    /**
     * Formatea una entrada del ranking.
     */
    protected function formatEntry(
        Participation $participation,
        int $position
    ): array {
        return [
            'position' => $position,

            'participation' => $participation,

            'student' => $participation->student,

            'team' => $participation->team,

            'score' => $participation->score ?? 0,

            'elapsed_seconds' =>
                $participation->elapsed_seconds,

            'attempt' => $participation->attempt,
        ];
    }

    /**
     * Obtiene los tres primeros lugares.
     */
    public function getTopThree(Activity $activity): Collection
    {
        return $this->getRanking($activity)
            ->take(3)
            ->values();
    }
}