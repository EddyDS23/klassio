<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function teacher(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'teacher',
            'status' => 'active',
        ], $attributes));
    }

    private function student(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'student',
            'status' => 'active',
        ], $attributes));
    }

    private function createClass(User $teacher, array $attributes = []): SchoolClass
    {
        return SchoolClass::create(array_merge([
            'teacher_id' => $teacher->id,
            'name' => 'Clase de prueba',
            'description' => 'Clase utilizada para pruebas',
            'code' => 'TEST01',
            'status' => 'active',
        ], $attributes));
    }

    private function createActivity(
        SchoolClass $class,
        User $teacher,
        array $attributes = []
    ): Activity {
        return Activity::create(array_merge([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Actividad de prueba',
            'description' => 'Actividad utilizada para pruebas',
            'type' => 'word_search',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => 60,
            'attempts' => 1,
            'status' => 'draft',
        ], $attributes));
    }

    public function test_teacher_can_update_own_class(): void
    {
        $teacher = $this->teacher();

        $class = $this->createClass($teacher);

        $response = $this->actingAs($teacher)
            ->get(route('teacher.classes.edit', $class->id));

        $response->assertOk();
    }

    public function test_teacher_cannot_update_another_teachers_class(): void
    {
        $teacherA = $this->teacher([
            'email' => 'teacher-a@test.com',
        ]);

        $teacherB = $this->teacher([
            'email' => 'teacher-b@test.com',
        ]);

        $class = $this->createClass($teacherB);

        $response = $this->actingAs($teacherA)
            ->get(route('teacher.classes.edit', $class->id));

        $response->assertForbidden();
    }

    public function test_teacher_can_update_own_class_with_put_request(): void
    {
        $teacher = $this->teacher();

        $class = $this->createClass($teacher, [
            'name' => 'Nombre original',
        ]);

        $response = $this->actingAs($teacher)
            ->put(route('teacher.classes.update', $class->id), [
                'name' => 'Nombre actualizado',
                'description' => 'Descripción actualizada',
            ]);

        $response->assertRedirect(route('teacher.classes.index'));

        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'teacher_id' => $teacher->id,
            'name' => 'Nombre actualizado',
        ]);
    }

    public function test_teacher_cannot_update_another_teachers_class_with_put_request(): void
    {
        $teacherA = $this->teacher([
            'email' => 'teacher-a@test.com',
        ]);

        $teacherB = $this->teacher([
            'email' => 'teacher-b@test.com',
        ]);

        $class = $this->createClass($teacherB, [
            'name' => 'Clase protegida',
        ]);

        $response = $this->actingAs($teacherA)
            ->put(route('teacher.classes.update', $class->id), [
                'name' => 'Intento de modificación',
                'description' => 'No debería modificarse',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'teacher_id' => $teacherB->id,
            'name' => 'Clase protegida',
        ]);
    }

    public function test_teacher_can_update_own_draft_activity(): void
    {
        $teacher = $this->teacher();

        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->get(route('teacher.activities.edit', $activity->id));

        $response->assertOk();
    }

    public function test_teacher_cannot_update_another_teachers_activity(): void
    {
        $teacherA = $this->teacher([
            'email' => 'teacher-a@test.com',
        ]);

        $teacherB = $this->teacher([
            'email' => 'teacher-b@test.com',
        ]);

        $class = $this->createClass($teacherB);

        $activity = $this->createActivity($class, $teacherB);

        $response = $this->actingAs($teacherA)
            ->get(route('teacher.activities.edit', $activity->id));

        $response->assertForbidden();
    }

    public function test_teacher_cannot_update_another_teachers_activity_with_put_request(): void
    {
        $teacherA = $this->teacher([
            'email' => 'teacher-a@test.com',
        ]);

        $teacherB = $this->teacher([
            'email' => 'teacher-b@test.com',
        ]);

        $class = $this->createClass($teacherB);

        $activity = $this->createActivity($class, $teacherB, [
            'title' => 'Actividad protegida',
        ]);

        $response = $this->actingAs($teacherA)
            ->put(route('teacher.activities.update', $activity->id), [
                'title' => 'Intento de modificación',
                'description' => 'No debería modificarse',
                'type' => 'word_search',
                'mode' => 'individual',
                'max_score' => 100,
                'time_limit' => 60,
                'attempts' => 1,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'teacher_id' => $teacherB->id,
            'title' => 'Actividad protegida',
        ]);
    }

    public function test_enrolled_student_can_view_class_activities(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);

        Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.activities.index', $class->id));

        $response->assertOk();
    }

    public function test_student_not_enrolled_cannot_view_class_activities(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);

        $response = $this->actingAs($student)
            ->get(route('student.activities.index', $class->id));

        $response->assertForbidden();
    }

    public function test_enrolled_student_can_view_published_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);

        Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'active',
        ]);

        $activity = $this->createActivity($class, $teacher, [
            'status' => 'published',
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.activities.show', $activity->id));

        $response->assertOk();
    }

    public function test_student_not_enrolled_cannot_view_published_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, [
            'status' => 'published',
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.activities.show', $activity->id));

        $response->assertForbidden();
    }

    public function test_student_cannot_view_draft_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);

        Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'active',
        ]);

        $activity = $this->createActivity($class, $teacher, [
            'status' => 'draft',
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.activities.show', $activity->id));

        $response->assertForbidden();
    }

    public function test_student_cannot_access_teacher_class_edit_route(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);

        $response = $this->actingAs($student)
            ->get(route('teacher.classes.edit', $class->id));

        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_student_cannot_access_teacher_activity_edit_route(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher);

        $response = $this->actingAs($student)
            ->get(route('teacher.activities.edit', $activity->id));

        $response->assertRedirect(route('student.dashboard'));
    }
}