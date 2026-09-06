<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClassService
{

    public function get(int $id)
    {

        $classe = SchoolClass::find($id);

        if ($classe === null) {
            return abort(404);
        }

        return $classe;
    }

    public function getClassesTeacher(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        return SchoolClass::where('teacher_id', $user->id)->get();
    }

    public function create(array $data): void
    {

        /** @var User $user */
        $user = Auth::user();

        do {
            $code = Str::random(6);
        } while (SchoolClass::where('code', $code)->exists());
        $data['code'] = $code;
        $data['teacher_id'] = $user->id;

        SchoolClass::create($data);
    }


    public function update(array $data, int $id): void
    {
        $class = SchoolClass::findOrFail($id);
        $class->update($data);
        $class->save();
    }

    public function archive(SchoolClass $class): void
    {
        $class->update([
            'status' => 'archived',
        ]);
    }

    public function unarchive(SchoolClass $class): void
    {
        $class->update([
            'status' => 'active',
        ]);
    }

    public function regenerateCode(SchoolClass $class): void
    {
        do {
            $code = Str::upper(Str::random(6));
        } while (SchoolClass::where('code', $code)->exists());

        $class->update([
            'code' => $code
        ]);
    }

    public function getStudents(SchoolClass $class)
    {
        return $class->enrollments()
            ->where('status', 'active')
            ->with('student')
            ->get();
    }

    public function removeStudent(SchoolClass $class, int $studentId): void
    {
        $enrollment = $class->enrollments()
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            abort(404, 'El alumno no pertenece a esta clase');
        }

        $enrollment->update([
            'status' => 'removed'
        ]);
    }


    public function join(string $code)
    {

        $class = SchoolClass::where('code', $code)->first();

        if (!$class) {
            abort(404, 'Clase no existe');
        }

        if ($class->status !== 'active') {
            abort(409, 'Clase no activa');
        }

        /** @var User $user */
        $user = Auth::user();

        $enrollment = Enrollment::where('student_id', $user->id)->where('class_id', $class->id)->first();

        if ($enrollment) {

            if ($enrollment->status === 'active') {
                abort(409, 'Ya estás agregado a esta clase');
            }

            if ($enrollment->status === 'removed') {
                $enrollment->update([
                    'status' => 'active'
                ]);

                return $class;
            }
        }

        Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $user->id,
        ]);

        return $class;
    }

    public function getClassesStudent(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        return SchoolClass::whereHas('enrollments', function ($query) use ($user) {
            $query->where('student_id', $user->id)
                ->where('status', 'active');
        })->get();
    }

    public function getClassmates(SchoolClass $class)
    {
        return $class->enrollments()
            ->where('status', 'active')
            ->with('student')
            ->get();
    }
}
