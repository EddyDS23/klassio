<?php

namespace App\Support;

use App\Models\Participation;
use Illuminate\Support\Collection;

/**
 * Selección consistente de intentos convencionales compartidos por equipo.
 */
class TeamAttemptSelector
{
    public static function bestCompleted(Collection $attempts): ?Participation
    {
        return $attempts
            ->where('status', 'completed')
            ->filter(fn (Participation $participation) =>
                $participation->score !== null
            )
            ->sort(function (Participation $a, Participation $b): int {
                $scoreComparison = ($b->score ?? 0) <=> ($a->score ?? 0);

                if ($scoreComparison !== 0) {
                    return $scoreComparison;
                }

                $timeComparison = ($a->elapsed_seconds ?? PHP_INT_MAX)
                    <=> ($b->elapsed_seconds ?? PHP_INT_MAX);

                if ($timeComparison !== 0) {
                    return $timeComparison;
                }

                return ($b->attempt <=> $a->attempt)
                    ?: ($b->id <=> $a->id);
            })
            ->first();
    }

    /**
     * Resultado visible al equipo: mejor intento completado; si ninguno
     * terminó, conserva la salida más reciente para mostrar abandono/expiración.
     */
    public static function result(Collection $attempts): ?Participation
    {
        return self::bestCompleted($attempts)
            ?? self::latestTerminal($attempts);
    }

    /**
     * En la tabla docente se prioriza un intento activo para reflejar el estado
     * actual del equipo; fuera de una actividad activa se muestra el mejor.
     */
    public static function teacherResult(Collection $attempts): ?Participation
    {
        return self::latestWithStatus($attempts, 'started')
            ?? self::bestCompleted($attempts)
            ?? self::latestTerminal($attempts);
    }

    private static function latestTerminal(Collection $attempts): ?Participation
    {
        return $attempts
            ->whereIn('status', ['abandoned', 'expired'])
            ->sort(fn (Participation $a, Participation $b) =>
                ($b->attempt <=> $a->attempt)
                ?: ($b->id <=> $a->id)
            )
            ->first();
    }

    private static function latestWithStatus(
        Collection $attempts,
        string $status
    ): ?Participation {
        return $attempts
            ->where('status', $status)
            ->sort(fn (Participation $a, Participation $b) =>
                ($b->attempt <=> $a->attempt)
                ?: ($b->id <=> $a->id)
            )
            ->first();
    }
}
