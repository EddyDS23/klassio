<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\SchoolClass;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitTest extends TestCase
{
    use RefreshDatabase;

    private function teacher(string $email = 'teacher@example.com'): User
    {
        return User::create([
            'name' => 'Teacher',
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => 'teacher',
            'status' => 'active',
        ]);
    }

    private function createClass(User $teacher): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Test Class',
            'description' => 'Unit tests',
            'code' => strtoupper(fake()->unique()->bothify('CLASS###')),
            'status' => 'active',
        ]);
    }

    public function test_unit_belongs_to_the_class_and_can_have_activities(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $unit = Unit::create([
            'class_id' => $class->id,
            'number' => 3,
            'name' => 'Unidad 3',
        ]);

        $this->assertSame($class->id, $unit->schoolClass->id);
        $this->assertSame(3, $unit->number);
        $this->assertSame('Unidad 3', $unit->name);
        $this->assertCount(0, $unit->activities);

        $activity = Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Actividad de unidad',
            'description' => 'Actividad perteneciente a la unidad 3',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => 30,
            'attempts' => 1,
            'due_at' => null,
            'status' => 'draft',
            'unit_id' => $unit->id,
        ]);

        $this->assertSame($unit->id, $activity->unit->id);
    }

    public function test_unit_numbers_are_unique_per_class(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        Unit::create([
            'class_id' => $class->id,
            'number' => 1,
            'name' => 'Unidad 1',
        ]);

        $this->expectException(UniqueConstraintViolationException::class);

        Unit::create([
            'class_id' => $class->id,
            'number' => 1,
            'name' => 'Unidad 1 duplicada',
        ]);
    }
}
