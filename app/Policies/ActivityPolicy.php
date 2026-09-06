<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActivityPolicy
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
    public function view(User $user, Activity $activity): bool
    {
        if ($activity->teacher_id === $user->id) {
            return true;
        }

        // Los estudiantes solamente pueden ver actividades publicadas 
        if ($activity->status !== 'published') {
            return false;
        }

        return SchoolClass::where('id', $activity->class_id)
            ->whereHas('enrollments', function ($query) use ($user) {
                $query->where('student_id', $user->id)
                ->where('status', 'active');
            })->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, SchoolClass $class): bool
    {
        return $class->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Activity $activity): bool
    {
        return $activity->teacher_id === $user->id && $activity->status === 'draft';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Activity $activity): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Activity $activity): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Activity $activity): bool
    {
        return false;
    }

    /**
     * El maestro propietario puede publicar.
     */
    public function publish(User $user, Activity $activity): bool
    {
        return $activity->teacher_id === $user->id;
    }


    /**
     * El maestro propietario puede cerrar.
     */
    public function close(User $user, Activity $activity): bool
    {
        return $activity->teacher_id === $user->id;
    }
}
