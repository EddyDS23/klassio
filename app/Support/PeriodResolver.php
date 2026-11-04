<?php

namespace App\Support;

use DateTimeInterface;
use InvalidArgumentException;

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

    /**
     * Periodo en curso.
     */
    public static function current(): string
    {
        return self::forDate();
    }

    /**
     * Código legible para la UI: "Primer semestre 2026 · Ene–Jun".
     */
    public static function label(string $code): string
    {
        [$year, $half] = self::split($code) ?? throw new InvalidArgumentException(
            "Código de periodo inválido: {$code}"
        );

        return match ($half) {
            1 => "Primer semestre {$year} · Ene–Jun",
            default => "Segundo semestre {$year} · Jul–Dic",
        };
    }

    /**
     * Divide "2026-H1" en [2026, 1]; los códigos inválidos devuelven null.
     *
     * @return array{int, int}|null
     */
    public static function split(string $code): ?array
    {
        if (preg_match('/^(\d{4})-H([12])$/', $code, $matches) !== 1) {
            return null;
        }

        return [(int) $matches[1], (int) $matches[2]];
    }
}
