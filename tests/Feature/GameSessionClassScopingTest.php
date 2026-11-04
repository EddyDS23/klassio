<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Crear sala desde la clase: el formulario se escopa a las actividades
 * publicadas de la clase pedida; un class_id ajeno o inexistente se ignora.
 */
class GameSessionClassScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_shows_only_published_activities_of_the_selected_class(): void
    {
        $teacher = $this->teacher();
        $classA = $this->createClass($teacher, 'Clase A');
        $classB = $this->createClass($teacher, 'Clase B');

        $this->createActivity($classA, $teacher, ['title' => 'Actividad Publicada A', 'status' => 'published']);
        $this->createActivity($classA, $teacher, ['title' => 'Actividad Borrador A', 'status' => 'draft']);
        $this->createActivity($classB, $teacher, ['title' => 'Actividad Publicada B', 'status' => 'published']);

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.create', ['class_id' => $classA->id]))
            ->assertOk()
            ->assertSee(route('teacher.classes.show', $classA->id))
            ->assertSee('Actividad Publicada A')
            ->assertDontSee('Actividad Borrador A')
            ->assertDontSee('Actividad Publicada B');
    }

    public function test_create_form_ignores_class_id_of_another_teacher(): void
    {
        $teacher = $this->teacher();
        $other = $this->teacher('other@example.com');

        $otherClass = $this->createClass($other, 'Clase Ajena');
        $this->createActivity($otherClass, $other, ['title' => 'Actividad Ajena', 'status' => 'published']);

        $ownClass = $this->createClass($teacher, 'Clase Propia');
        $ownActivity = $this->createActivity($ownClass, $teacher, ['title' => 'Actividad Propia', 'status' => 'published']);

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.create', ['class_id' => $otherClass->id]))
            ->assertOk()
            // El filtro ajeno se ignora: la propia actividad (de otra clase) sigue visible
            ->assertSee('Actividad Propia')
            ->assertDontSee('Actividad Ajena');
    }

    public function test_create_form_shows_empty_state_when_class_has_no_published_activities(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher, 'Clase Vacía');
        $this->createActivity($class, $teacher, ['title' => 'Solo Borrador', 'status' => 'draft']);

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.create', ['class_id' => $class->id]))
            ->assertOk()
            ->assertSee('No tienes actividades publicadas en')
            ->assertSee('Clase Vacía')
            ->assertDontSee('Solo Borrador');
    }

    public function test_teacher_cannot_store_room_for_draft_activity(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $draft = $this->createActivity($class, $teacher, ['status' => 'draft']);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.store'), [
                'activity_id' => $draft->id,
                'max_players' => 4,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('game_sessions', ['activity_id' => $draft->id]);
    }

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

    private function createClass(User $teacher, string $name = 'Test Class'): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => $name,
            'description' => 'Scoping tests',
            'code' => strtoupper(fake()->unique()->bothify('SCOPE###')),
            'status' => 'active',
        ]);
    }

    private function createActivity(
        SchoolClass $class,
        User $teacher,
        array $attributes = []
    ): Activity {
        return Activity::create(array_merge([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Scoping Activity',
            'description' => 'Test activity',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
            'due_at' => null,
            'status' => 'published',
        ], $attributes));
    }
}
