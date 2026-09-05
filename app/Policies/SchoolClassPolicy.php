<?php

namespace App\Policies;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SchoolClassPolicy
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
    public function view(User $user, SchoolClass $schoolClass): bool
    {

        if ($schoolClass->teacher_id === $user->id) {
            return true;
        }

        return $schoolClass->enrollments()
            ->where('student_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'teacher';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SchoolClass $schoolClass): bool
    {
        return $user->role === 'teacher';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SchoolClass $schoolClass): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SchoolClass $schoolClass): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SchoolClass $schoolClass): bool
    {
        return false;
    }

    public function archive(User $user, SchoolClass $class): bool
    {
        return $class->teacher_id === $user->id;
    }

    public function unarchive(User $user, SchoolClass $class): bool
    {
        return $class->teacher_id === $user->id;
    }

    public function regenerateCode(User $user, SchoolClass $class): bool
    {
        return $class->teacher_id === $user->id;
    }

    public function viewStudents(User $user, SchoolClass $class): bool
    {
        return $class->teacher_id === $user->id;
    }

    public function viewClassmates(User $user, SchoolClass $class): bool
    {
        return $class->enrollments()
            ->where('student_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }

    public function removeStudent(User $user, SchoolClass $class): bool
    {
        return $class->teacher_id === $user->id;
    }
}
