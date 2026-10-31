<?php

namespace App\Support;

use DateTimeInterface;

/**
 * Periodos de seis meses (enero-junio / julio-diciembre) usados para agrupar
 * las clases por ciclo.
 */
class PeriodResolver
{
    /**
     * Periodo de seis meses al que pertenece la fecha: "YYYY-H1" para
     * enero-junio y "YYYY-H2" para julio-diciembre.
     */
    public static function forDate(?DateTimeInterface $date = null): string
    {
        $reference = $date ?? now();

        $year = (int) $reference->format('Y');
        $half = ((int) $reference->format('n')) <= 6 ? 1 : 2;

        return "{$year}-H{$half}";
    }
}
