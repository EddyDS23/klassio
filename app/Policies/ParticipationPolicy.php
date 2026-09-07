<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\Participation;
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
     * La inscripción se valida en el service porque requiere query.
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
     * Solo el dueño de la participación puede responder,
     * y la participación debe estar activa.
     */
    public function answer(User $user, Participation $participation): bool
    {
        return $participation->student_id === $user->id
            && $participation->status === 'started';
    }
 
    /**
     * Solo el dueño puede finalizar su participación activa.
     */
    public function finish(User $user, Participation $participation): bool
    {
        return $participation->student_id === $user->id
            && $participation->status === 'started';
    }
 
    /**
     * Solo el dueño puede abandonar su participación activa.
     */
    public function abandon(User $user, Participation $participation): bool
    {
        return $participation->student_id === $user->id
            && $participation->status === 'started';
    }
 
    /**
     * El resultado lo puede ver:
     * - el estudiante dueño del intento
     * - el maestro propietario de la actividad
     */
    public function result(User $user, Participation $participation): bool
    {
        if ($participation->student_id === $user->id) {
            return true;
        }
 
        return $participation->activity->teacher_id === $user->id;
    }
}
