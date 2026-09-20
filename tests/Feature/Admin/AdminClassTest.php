<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminClassTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    public function test_only_admins_can_list_classes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $this->get(route('admin.classes.index'))->assertRedirect('/login');

        $this->actingAs($teacher)
            ->get(route('admin.classes.index'))
            ->assertRedirect(route('teacher.dashboard'));

        $this->actingAs($this->admin())
            ->get(route('admin.classes.index'))
            ->assertOk()
            ->assertViewHas('classes');
    }

    public function test_admin_can_list_classes_with_teacher_and_counts(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['name' => 'Sofía Rojas', 'role' => 'teacher', 'status' => 'active']);

        $class = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Matemáticas 3A',
            'description' => 'Álgebra básica',
            'code' => 'MATE3A',
            'status' => 'active',
        ]);

        Enrollment::create(['class_id' => $class->id, 'student_id' => User::factory()->create(['role' => 'student'])->id, 'status' => 'active']);
        Enrollment::create(['class_id' => $class->id, 'student_id' => User::factory()->create(['role' => 'student'])->id, 'status' => 'removed']);

        Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Sopa de números',
            'type' => 'word_search',
            'mode' => 'individual',
            'max_score' => 50,
            'status' => 'published',
        ]);

        $this->get(route('admin.classes.index'))
            ->assertOk()
            ->assertSee('Matemáticas 3A')
            ->assertSee('MATE3A')
            ->assertSee('Sofía Rojas')
            ->assertViewHas('classes', function ($classes) use ($class) {
                $found = $classes->firstWhere('id', $class->id);

                return $found
                    && $found->active_students_count === 1
                    && $found->enrollments_count === 2
                    && $found->activities_count === 1;
            });
    }

    public function test_admin_can_search_classes_by_name_and_code(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        SchoolClass::create(['teacher_id' => $teacher->id, 'name' => 'Ciencias Naturales', 'code' => 'CIEN01', 'status' => 'active']);
        SchoolClass::create(['teacher_id' => $teacher->id, 'name' => 'Educación Física', 'code' => 'EDUFIS', 'status' => 'active']);

        $this->get(route('admin.classes.index', ['search' => 'Ciencias']))
            ->assertOk()
            ->assertSee('Ciencias Naturales')
            ->assertDontSee('Educación Física');

        $this->get(route('admin.classes.index', ['search' => 'EDUFIS']))
            ->assertOk()
            ->assertSee('Educación Física')
            ->assertDontSee('Ciencias Naturales');
    }

    public function test_admin_can_filter_classes_by_status(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        SchoolClass::create(['teacher_id' => $teacher->id, 'name' => 'Clase Activa', 'code' => 'CA01', 'status' => 'active']);
        SchoolClass::create(['teacher_id' => $teacher->id, 'name' => 'Clase Archivada', 'code' => 'CAR01', 'status' => 'archived']);

        $this->get(route('admin.classes.index', ['status' => 'archived']))
            ->assertOk()
            ->assertSee('Clase Archivada')
            ->assertDontSee('Clase Activa');
    }

    public function test_admin_can_filter_classes_by_teacher(): void
    {
        $this->actingAs($this->admin());

        $teacherA = User::factory()->create(['name' => 'Profe Ana', 'role' => 'teacher', 'status' => 'active']);
        $teacherB = User::factory()->create(['name' => 'Profe Beto', 'role' => 'teacher', 'status' => 'active']);

        SchoolClass::create(['teacher_id' => $teacherA->id, 'name' => 'Clase de Ana', 'code' => 'ANA01', 'status' => 'active']);
        SchoolClass::create(['teacher_id' => $teacherB->id, 'name' => 'Clase de Beto', 'code' => 'BET01', 'status' => 'active']);

        $this->get(route('admin.classes.index', ['teacher_id' => $teacherA->id]))
            ->assertOk()
            ->assertSee('Clase de Ana')
            ->assertDontSee('Clase de Beto');
    }

    public function test_admin_can_view_class_detail(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['name' => 'Iván Paz', 'role' => 'teacher', 'status' => 'active']);
        $student = User::factory()->create(['name' => 'Lucía Mora', 'role' => 'student', 'status' => 'active']);

        $class = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Geografía 5C',
            'description' => 'Continentes y océanos',
            'code' => 'GEO5C',
            'status' => 'active',
        ]);

        Enrollment::create(['class_id' => $class->id, 'student_id' => $student->id, 'status' => 'active']);

        $activity = Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Mapa de América',
            'type' => 'matching',
            'mode' => 'individual',
            'max_score' => 30,
            'status' => 'published',
        ]);

        $this->get(route('admin.classes.show', $class->id))
            ->assertOk()
            ->assertSee('Geografía 5C')
            ->assertSee('Lucía Mora')
            ->assertSee('Mapa de América')
            ->assertViewHas('class', fn ($loaded) => $loaded->id === $class->id);
    }

    public function test_non_existent_class_returns_404(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.classes.show', 9999))
            ->assertNotFound();
    }
}