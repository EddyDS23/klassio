<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\Participation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ParticipationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Participation $participation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Participation $participation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Participation $participation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Participation $participation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Participation $participation): bool
    {
        return false;
    }

    /**
     * El estudiante puede iniciar si:
     * - su rol es student
     * - la actividad está publicada
     * - no ha pasado due_at
     * La inscripción y la pertenencia al equipo se validan en el service.
     */
    public function start(User $user, Activity $activity): bool
    {
        if ($user->role !== 'student') {
            return false;
        }

        if ($activity->status !== 'published') {
            return false;
        }

        if ($activity->due_at !== null && now()->isAfter($activity->due_at)) {
            return false;
        }

        return true;
    }

    /**
     * Solo puede responder quien sea dueño de la participación activa.
     * Soporta modo individual (student_id) y equipo (team_id).
     */
    public function answer(User $user, Participation $participation): bool
    {
        if ($participation->status !== 'started') {
            return false;
        }

        return $this->owns($user, $participation);
    }

    /**
     * Solo el dueño puede finalizar su participación activa.
     */
    public function finish(User $user, Participation $participation): bool
    {
        if ($participation->status !== 'started') {
            return false;
        }

        return $this->owns($user, $participation);
    }

    /**
     * Solo el dueño puede abandonar su participación activa.
     */
    public function abandon(User $user, Participation $participation): bool
    {
        if ($participation->status !== 'started') {
            return false;
        }

        return $this->owns($user, $participation);
    }

    /**
     * El resultado lo puede ver:
     * - el estudiante dueño del intento (individual o equipo)
     * - el maestro propietario de la actividad
     */
    public function result(User $user, Participation $participation): bool
    {
        if ($this->owns($user, $participation)) {
            return true;
        }

        return $participation->activity->teacher_id === $user->id;
    }
 
    // -------------------------------------------------------------------------
    // Helper: determinar si el usuario es dueño de la participación
    // -------------------------------------------------------------------------

    /**
     * Modo individual: participation.student_id === user.id
     * Modo equipo:     el usuario pertenece al equipo de la participación
     */
    private function owns(User $user, Participation $participation): bool
    {
        // Modo individual
        if ($participation->student_id !== null) {
            return $participation->student_id === $user->id;
        }

        // Modo equipo
        if ($participation->team_id !== null) {
            return Team::where('id', $participation->team_id)
                ->whereHas('members', fn($q) => $q->where('student_id', $user->id))
                ->exists();
        }

        return false;
    }
}
