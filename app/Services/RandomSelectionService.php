<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

class RandomSelectionService
{
    /**
     * Seleccionar un alumno aleatoriamente
     * entre los alumnos activos de la clase.
     */
    public function randomStudent(
        Activity $activity
    ): ?User {
        $students = $activity->schoolClass
            ->enrollments()
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->values();

        if ($students->isEmpty()) {
            return null;
        }

        return $students->random();
    }

    /**
     * Seleccionar un equipo aleatoriamente.
     *
     * Solamente se consideran equipos que tengan
     * al menos un integrante.
     */
    public function randomTeam(
        Activity $activity
    ): ?Team {
        $teams = $activity->teams()
            ->has('members')
            ->with('members.student')
            ->get();

        if ($teams->isEmpty()) {
            return null;
        }

        return $teams->random();
    }

    /**
     * Distribuir una colección de alumnos
     * en grupos aleatorios y equilibrados.
     *
     * Ejemplos:
     *
     * 17 alumnos / 2 equipos = 9 + 8
     * 17 alumnos / 3 equipos = 6 + 6 + 5
     * 17 alumnos / 4 equipos = 5 + 4 + 4 + 4
     *
     * La diferencia máxima entre dos equipos
     * será de un alumno.
     */
    public function distributeEqually(
        Collection $students,
        int $groupCount
    ): Collection {
        if ($groupCount < 1) {
            return collect();
        }

        if ($students->isEmpty()) {
            return collect();
        }

        if ($groupCount > $students->count()) {
            return collect();
        }

        /*
         * Mezclamos los alumnos para que cada
         * distribución sea diferente.
         */
        $students = $students
            ->shuffle()
            ->values();

        $studentCount = $students->count();

        /*
         * Cantidad base de alumnos por equipo.
         */
        $baseSize = intdiv(
            $studentCount,
            $groupCount
        );

        /*
         * Alumnos que sobran después de repartir
         * equitativamente.
         *
         * Ejemplo:
         *
         * 17 / 3
         *
         * baseSize = 5
         * remainder = 2
         *
         * Resultado:
         * 6 + 6 + 5
         */
        $remainder = $studentCount % $groupCount;

        $groups = collect();

        $offset = 0;

        for ($i = 0; $i < $groupCount; $i++) {
            /*
             * Los primeros grupos reciben un alumno
             * adicional cuando existe remainder.
             */
            $size = $baseSize;

            if ($i < $remainder) {
                $size++;
            }

            $groups->push(
                $students
                    ->slice($offset, $size)
                    ->values()
            );

            $offset += $size;
        }

        return $groups;
    }
}