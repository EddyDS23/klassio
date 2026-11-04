<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PeriodsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Fecha fija: abril de 2026 → periodo actual "2026-H1".
        $this->travelTo(Carbon::parse('2026-04-10'));
    }

    private function teacher(string $email = 'periods-teacher@example.com'): User
    {
        return User::factory()->create([
            'email' => $email,
            'role' => 'teacher',
            'status' => 'active',
        ]);
    }

    private function classFor(User $teacher, string $name, ?Carbon $date): SchoolClass
    {
        $class = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => $name,
            'description' => 'Clase de periodos',
            'code' => strtoupper(fake()->unique()->bothify('PERI###')),
            'status' => 'active',
        ]);

        if ($date !== null) {
            SchoolClass::whereKey($class->id)->update(['created_at' => $date]);
            $class->refresh();
        }

        return $class;
    }

    public function test_teacher_index_groups_classes_by_period_with_current_first(): void
    {
        $teacher = $this->teacher();
        $past = $this->classFor($teacher, 'Clase antigua', Carbon::parse('2025-08-15'));
        $current = $this->classFor($teacher, 'Clase actual', Carbon::parse('2026-03-10'));

        $response = $this->actingAs($teacher)
            ->get(route('teacher.classes.index'))
            ->assertOk();

        // El periodo actual se lista primero (desc) y va abierto con badge.
        $html = $response->getContent();
        $pastPosition = mb_strpos($html, 'Segundo semestre 2025');
        $currentPosition = mb_strpos($html, 'Primer semestre 2026');
        $this->assertTrue($pastPosition !== false && $currentPosition !== false);

        $this->assertGreaterThan($currentPosition, $pastPosition, 'El periodo actual debe aparecer antes que los antiguos.');

        $response->assertSee('Periodo actual')
            ->assertSee($past->name)
            ->assertSee($current->name);
    }

    public function test_teacher_index_filters_by_requested_period(): void
    {
        $teacher = $this->teacher();
        $past = $this->classFor($teacher, 'Clase antigua', Carbon::parse('2025-08-15'));
        $current = $this->classFor($teacher, 'Clase actual', Carbon::parse('2026-03-10'));

        $this->actingAs($teacher)
            ->get(route('teacher.classes.index', ['period' => '2026-H1']))
            ->assertOk()
            ->assertSee($current->name)
            ->assertDontSee($past->name);
    }

    public function test_teacher_index_ignores_invalid_period_filter(): void
    {
        $teacher = $this->teacher();
        $class = $this->classFor($teacher, 'Clase única', Carbon::parse('2026-03-10'));

        $this->actingAs($teacher)
            ->get(route('teacher.classes.index', ['period' => 'no-existe']))
            ->assertOk()
            ->assertSee($class->name);
    }

    public function test_student_index_groups_enrolled_classes_by_period(): void
    {
        $teacher = $this->teacher();
        $student = User::factory()->create([
            'email' => 'periods-student@example.com',
            'role' => 'student',
            'status' => 'active',
        ]);

        $past = $this->classFor($teacher, 'Clase pasada del curso', Carbon::parse('2025-08-15'));
        $current = $this->classFor($teacher, 'Clase presente del curso', Carbon::parse('2026-04-01'));

        foreach ([$past, $current] as $class) {
            Enrollment::create([
                'class_id' => $class->id,
                'student_id' => $student->id,
                'status' => 'active',
            ]);
        }

        $response = $this->actingAs($student)
            ->get(route('student.classes.index'))
            ->assertOk();

        $html = $response->getContent();
        $pastPosition = mb_strpos($html, 'Segundo semestre 2025');
        $currentPosition = mb_strpos($html, 'Primer semestre 2026');
        $this->assertTrue($pastPosition !== false && $currentPosition !== false);

        $this->assertGreaterThan($currentPosition, $pastPosition, 'El periodo actual debe aparecer primero también para el estudiante.');

        $response->assertSee('Clase pasada del curso')
            ->assertSee('Clase presente del curso');
    }

    public function test_student_index_filters_by_requested_period(): void
    {
        $teacher = $this->teacher();
        $student = User::factory()->create([
            'email' => 'periods-student-2@example.com',
            'role' => 'student',
            'status' => 'active',
        ]);

        $past = $this->classFor($teacher, 'Solo segundo 2025', Carbon::parse('2025-08-15'));
        $current = $this->classFor($teacher, 'Solo primero 2026', Carbon::parse('2026-04-01'));

        foreach ([$past, $current] as $class) {
            Enrollment::create([
                'class_id' => $class->id,
                'student_id' => $student->id,
                'status' => 'active',
            ]);
        }

        $this->actingAs($student)
            ->get(route('student.classes.index', ['period' => '2025-H2']))
            ->assertOk()
            ->assertSee($past->name)
            ->assertDontSee($current->name);
    }

    public function test_teacher_index_paginates_the_period_groups(): void
    {
        $teacher = $this->teacher();

        // 5 periodos distintos: la vista muestra 4 acordeones por página.
        $oldest = $this->classFor($teacher, 'Clase 2024', Carbon::parse('2024-10-05'));
        $this->classFor($teacher, 'Clase 2025-H1', Carbon::parse('2025-02-20'));
        $this->classFor($teacher, 'Clase 2025-H2', Carbon::parse('2025-09-12'));
        $this->classFor($teacher, 'Clase 2026-H1', Carbon::parse('2026-03-01'));
        $this->classFor($teacher, 'Clase 2026-H2', Carbon::parse('2026-08-30'));

        $this->actingAs($teacher)
            ->get(route('teacher.classes.index'))
            ->assertOk()
            ->assertSee('Segundo semestre 2026')
            ->assertSee('Primer semestre 2026')
            ->assertSee('Segundo semestre 2025')
            ->assertSee('Clase 2025-H2')
            ->assertSee('Primer semestre 2025')
            ->assertDontSee($oldest->name)
            ->assertSee('page=2');

        $this->actingAs($teacher)
            ->get(route('teacher.classes.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Segundo semestre 2024')
            ->assertSee($oldest->name);
    }

    public function test_student_index_paginates_the_period_groups(): void
    {
        $teacher = $this->teacher();
        $student = User::factory()->create([
            'email' => 'periods-student-3@example.com',
            'role' => 'student',
            'status' => 'active',
        ]);

        $oldest = $this->classFor($teacher, 'Alumno 2024', Carbon::parse('2024-10-05'));
        $this->classFor($teacher, 'Alumno 2025-H1', Carbon::parse('2025-02-20'));
        $this->classFor($teacher, 'Alumno 2025-H2', Carbon::parse('2025-09-12'));
        $this->classFor($teacher, 'Alumno 2026-H1', Carbon::parse('2026-03-01'));
        $this->classFor($teacher, 'Alumno 2026-H2', Carbon::parse('2026-08-30'));

        SchoolClass::where('teacher_id', $teacher->id)->get()->each(
            fn (SchoolClass $class) => Enrollment::create([
                'class_id' => $class->id,
                'student_id' => $student->id,
                'status' => 'active',
            ])
        );

        $this->actingAs($student)
            ->get(route('student.classes.index'))
            ->assertOk()
            ->assertDontSee($oldest->name)
            ->assertSee('page=2');

        $this->actingAs($student)
            ->get(route('student.classes.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Segundo semestre 2024')
            ->assertSee($oldest->name);
    }
}
