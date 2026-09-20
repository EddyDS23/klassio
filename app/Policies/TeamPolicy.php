<?php
namespace App\Policies;

use App\Models\Activity;
use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Ver equipos de una actividad.
     */
    public function view(
        User $user,
        Activity $activity
    ): bool {
        return $this->manage(
            $user,
            $activity
        );
    }

    /**
     * Crear equipos para una actividad.
     */
    public function create(
        User $user,
        Activity $activity
    ): bool {
        return $this->manage(
            $user,
            $activity
        );
    }

    /**
     * Actualizar un equipo.
     */
    public function update(
        User $user,
        Team $team
    ): bool {
        return $user->role === 'teacher'
            && $team->activity
            && (int) $team->activity->teacher_id === (int) $user->id;
    }

    /**
     * Eliminar un equipo.
     */
    public function delete(
        User $user,
        Team $team
    ): bool {
        return $this->update(
            $user,
            $team
        );
    }

    /**
     * Determina si el profesor puede administrar
     * los equipos de una actividad.
     */
    public function manage(
        User $user,
        Activity $activity
    ): bool {
        return $user->role === 'teacher'
            && (int) $activity->teacher_id === (int) $user->id;
    }

    /**
     * No utilizamos restore actualmente.
     */
    public function restore(
        User $user,
        Team $team
    ): bool {
        return false;
    }

    /**
     * No utilizamos force delete actualmente.
     */
    public function forceDelete(
        User $user,
        Team $team
    ): bool {
        return false;
    }

    /**
     * No utilizamos viewAny actualmente.
     */
    public function viewAny(
        User $user
    ): bool {
        return false;
    }
}

