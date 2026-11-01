<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\SchoolClass;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitsTest extends TestCase
{
    use RefreshDatabase;

    private function teacher(string $email = 'units-teacher@example.com'): User
    {
        return User::factory()->create([
            'email' => $email,
            'role' => 'teacher',
            'status' => 'active',
        ]);
    }

    private function classFor(User $teacher, string $suffix = ''): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Clase de unidades '.$suffix,
            'description' => 'Unidades',
            'code' => strtoupper(fake()->unique()->bothify('UNIT###')).$suffix,
            'status' => 'active',
        ]);
    }

    private function createClassPayload(array $units = []): array
    {
        $payload = [
            'name' => 'Programación Web',
            'description' => 'Clase con unidades',
        ];

        foreach (array_values($units) as $index => $name) {
            $payload['units'][$index]['name'] = $name;
        }

        return $payload;
    }

    public function test_creating_a_class_persists_its_units_in_order(): void
    {
        $teacher = $this->teacher();

        $this->actingAs($teacher)
            ->post(route('teacher.classes.store'), $this->createClassPayload([
                'Introducción al desarrollo web',
                '', // Fila en blanco: debe ignorarse
                'HTML y CSS',
                null,
                'JavaScript',
            ]))
            ->assertRedirect(route('teacher.classes.index'));

        $class = SchoolClass::where('teacher_id', $teacher->id)->orderByDesc('id')->firstOrFail();
        $units = $class->units;

        $this->assertCount(3, $units);
        $this->assertSame([
            ['number' => 1, 'name' => 'Introducción al desarrollo web'],
            ['number' => 2, 'name' => 'HTML y CSS'],
            ['number' => 3, 'name' => 'JavaScript'],
        ], $units->map(
            fn (Unit $unit): array => ['number' => $unit->number, 'name' => $unit->name]
        )->all());
    }

    public function test_creating_a_class_without_units_works(): void
    {
        $teacher = $this->teacher();

        $this->actingAs($teacher)
            ->post(route('teacher.classes.store'), $this->createClassPayload())
            ->assertRedirect(route('teacher.classes.index'));

        $class = SchoolClass::where('teacher_id', $teacher->id)->orderByDesc('id')->firstOrFail();

        $this->assertCount(0, $class->units);
    }

    public function test_class_units_are_limited_to_eight(): void
    {
        $teacher = $this->teacher();

        $this->actingAs($teacher)
            ->post(route('teacher.classes.store'), $this->createClassPayload(array_fill(0, 9, 'Unidad')))
            ->assertSessionHasErrors('units');
    }

    public function test_activity_can_be_created_with_a_unit_of_its_class(): void
    {
        $teacher = $this->teacher();
        $class = $this->classFor($teacher);
        $unit = $class->units()->create(['number' => 1, 'name' => 'Unidad Uno']);

        $this->actingAs($teacher)
            ->post(route('teacher.activities.store', $class->id), [
                'title' => 'Actividad con unidad',
                'type' => 'kahoot',
                'mode' => 'individual',
                'max_score' => 100,
                'time_limit' => 30,
                'attempts' => 1,
                'unit_id' => $unit->id,
            ])
            ->assertRedirect()
            ->assertSessionMissing('errors');

        $activity = Activity::where('class_id', $class->id)->firstOrFail();
        $this->assertSame($unit->id, $activity->unit->id);
    }

    public function test_activity_cannot_use_a_unit_of_another_class(): void
    {
        $teacher = $this->teacher();
        $class = $this->classFor($teacher, 'A');
        $class->units()->create(['number' => 1, 'name' => 'Unidad Propia']);

        $otherClass = $this->classFor($teacher, 'B');
        $foreignUnit = $otherClass->units()->create(['number' => 1, 'name' => 'Unidad Ajena']);

        $this->actingAs($teacher)
            ->post(route('teacher.activities.store', $class->id), [
                'title' => 'Actividad con unidad ajena',
                'type' => 'kahoot',
                'mode' => 'individual',
                'max_score' => 100,
                'time_limit' => 30,
                'attempts' => 1,
                'unit_id' => $foreignUnit->id,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('unit_id');

        $this->assertNull(Activity::where('class_id', $class->id)->first());
    }

    public function test_activity_update_moves_the_unit_within_its_class(): void
    {
        $teacher = $this->teacher();
        $class = $this->classFor($teacher);
        $unitOne = $class->units()->create(['number' => 1, 'name' => 'Unidad Uno']);
        $unitTwo = $class->units()->create(['number' => 2, 'name' => 'Unidad Dos']);

        $activity = Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Actividad editable',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
            'status' => 'draft',
            'unit_id' => $unitOne->id,
        ]);

        $this->actingAs($teacher)
            ->put(route('teacher.activities.update', $activity->id), [
                'title' => 'Actividad editable',
                'type' => 'kahoot',
                'mode' => 'individual',
                'max_score' => 100,
                'time_limit' => 30,
                'unit_id' => $unitTwo->id,
            ])
            ->assertRedirect()
            ->assertSessionMissing('errors');

        $this->assertSame($unitTwo->id, $activity->fresh()->unit_id);
    }

    public function test_activity_update_rejects_a_unit_of_another_class(): void
    {
        $teacher = $this->teacher();
        $class = $this->classFor($teacher);
        $foreignUnit = $this->classFor($teacher, 'X')->units()->create([
            'number' => 1,
            'name' => 'Unidad Ajena',
        ]);

        $activity = Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Actividad no editable',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
            'status' => 'draft',
        ]);

        $this->actingAs($teacher)
            ->put(route('teacher.activities.update', $activity->id), [
                'title' => 'Actividad no editable',
                'type' => 'kahoot',
                'mode' => 'individual',
                'max_score' => 100,
                'time_limit' => 30,
                'unit_id' => $foreignUnit->id,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('unit_id');

        $this->assertNull($activity->fresh()->unit_id);
    }
}
