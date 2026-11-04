<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\User;
use App\Support\PeriodResolver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function periodsForTeacher(User $user): Collection
    {
        return $this->groupClassesByPeriod(
            SchoolClass::where('teacher_id', $user->id)
                ->orderByDesc('created_at')
                ->get()
        );
    }

    public function create(array $data, array $units = []): void
    {

        /** @var User $user */
        $user = Auth::user();

        do {
            $code = Str::random(6);
        } while (SchoolClass::where('code', $code)->exists());
        $data['code'] = $code;
        $data['teacher_id'] = $user->id;

        DB::transaction(function () use ($data, $units): void {
            $class = SchoolClass::create($data);

            // Las filas sin nombre se descartan: el profesor las deja
            // vacías al agregar y quitar unidades en el formulario.
            $number = 1;

            foreach (array_values($units) as $unit) {
                $name = $unit['name'] ?? null;
                $name = is_string($name) && trim($name) !== '' ? trim($name) : null;

                if ($name === null) {
                    continue;
                }

                $class->units()->create([
                    'number' => $number++,
                    'name' => $name,
                ]);
            }
        });
    }

    public function update(array $data, int $id, ?array $units = null): void
    {
        DB::transaction(function () use ($data, $id, $units): void {
            $class = SchoolClass::findOrFail($id);
            $class->update($data);

            if ($units === null) {
                return;
            }

            $this->syncUnits($class, $units);
        });
    }

    /**
     * Sincroniza las unidades de una clase con las filas del formulario:
     * renombra las existentes (por id), agrega nuevas al final y elimina
     * las que no llegan en el envío. La petición ya validó que ninguna
     * unidad eliminada tenga actividades asociadas.
     *
     * @param  list<array{id?: int, name: ?string}>  $units
     */
    private function syncUnits(SchoolClass $class, array $units): void
    {
        $existing = $class->units->keyBy('id');
        $nextNumber = (int) $existing->max('number') + 1;
        $keptIds = [];

        foreach (array_values($units) as $row) {
            $unit = isset($row['id']) ? ($existing->get($row['id'])) : null;

            if ($unit === null) {
                $class->units()->create([
                    'number' => $nextNumber++,
                    'name' => $row['name'],
                ]);

                continue;
            }

            $keptIds[] = $unit->id;

            if ($unit->name !== $row['name']) {
                $unit->update(['name' => $row['name']]);
            }
        }

        $existing->keys()
            ->diff($keptIds)
            ->each(fn ($id) => $existing->get($id)->delete());
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
            'code' => $code,
        ]);
    }

    public function getStudents(SchoolClass $class)
    {
        return $class->enrollments()
            ->where('status', 'active')
            ->with('student')
            ->paginate(20);
    }

    public function removeStudent(SchoolClass $class, int $studentId): void
    {
        $enrollment = $class->enrollments()
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->first();

        if (! $enrollment) {
            abort(404, 'El alumno no pertenece a esta clase');
        }

        $enrollment->update([
            'status' => 'removed',
        ]);
    }

    /**
     * @return array{class: SchoolClass, alreadyJoined: bool}
     */
    public function join(string $code): array
    {

        $class = SchoolClass::where('code', $code)->first();

        if (! $class) {
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
                return ['class' => $class, 'alreadyJoined' => true];
            }

            if ($enrollment->status === 'removed') {
                $enrollment->update([
                    'status' => 'active',
                ]);

                return ['class' => $class, 'alreadyJoined' => false];
            }
        }

        Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $user->id,
        ]);

        return ['class' => $class, 'alreadyJoined' => false];
    }

    public function periodsForStudent(User $user): Collection
    {
        return $this->groupClassesByPeriod(
            SchoolClass::whereHas('enrollments', function ($query) use ($user) {
                $query->where('student_id', $user->id)->where('status', 'active');
            })
                ->where('status', 'active')
                ->orderByDesc('created_at')
                ->get()
        );
    }

    /**
     * Agrupa las clases por periodo "AAAA-Hn", ordenado del más reciente
     * al más antiguo. Cada grupo mantiene el orden de las clases.
     *
     * @return Collection<string, mixed>
     */
    private function groupClassesByPeriod(Collection $classes): Collection
    {
        return $classes
            ->groupBy(fn ($class) => PeriodResolver::forDate($class->created_at))
            ->map(fn (Collection $group, string $code): array => [
                'code' => $code,
                'classes' => $group->values(),
            ])
            ->sortKeysDesc()
            ->values();
    }

    public function getClassmates(SchoolClass $class): LengthAwarePaginator
    {
        return $class->enrollments()
            ->where('status', 'active')
            ->with('student')
            ->paginate(20);
    }
}
