<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Crear sala con flujo clase → actividad: el profesor elige la clase en un
 * selector (solo clases activas, agrupadas por periodo) y después se listan
 * únicamente las actividades publicadas de esa clase. Nunca se listan
 * actividades mezcladas de varias clases.
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
            // El class_id ajeno se ignora: vuelve al estado "elige una clase"
            // y ninguna actividad (ni propia ni ajena) se lista.
            ->assertSee('Elige primero una clase')
            ->assertDontSee('Actividad Propia')
            ->assertDontSee('Actividad Ajena');
    }

    public function test_create_form_hides_activities_until_a_class_is_chosen(): void
    {
        $teacher = $this->teacher();
        $classA = $this->createClass($teacher, 'Clase A');
        $classB = $this->createClass($teacher, 'Clase B');
        $this->createActivity($classA, $teacher, ['title' => 'Actividad A', 'status' => 'published']);
        $this->createActivity($classB, $teacher, ['title' => 'Actividad B', 'status' => 'published']);

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.create'))
            ->assertOk()
            ->assertSee('Elige primero una clase')
            // Ninguna actividad se lista sin clase elegida.
            ->assertDontSee('Actividad A')
            ->assertDontSee('Actividad B')
            // El selector ofrece las clases activas por nombre.
            ->assertSee('Clase A')
            ->assertSee('Clase B');
    }

    public function test_archived_classes_are_not_offered_in_the_class_selector(): void
    {
        $teacher = $this->teacher();
        $this->createClass($teacher, 'Clase Activa');
        $this->createClass($teacher, 'Clase Archivada', ['status' => 'archived']);

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.create'))
            ->assertOk()
            ->assertSee('Clase Activa')
            ->assertDontSee('Clase Archivada');
    }

    public function test_activity_id_preselects_its_class_and_the_activity(): void
    {
        $teacher = $this->teacher();
        $classA = $this->createClass($teacher, 'Clase A');
        $classB = $this->createClass($teacher, 'Clase B');
        $activity = $this->createActivity($classA, $teacher, ['title' => 'Actividad Elegida', 'status' => 'published']);
        $this->createActivity($classB, $teacher, ['title' => 'Actividad Otra', 'status' => 'published']);

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.create', ['activity_id' => $activity->id]))
            ->assertOk()
            ->assertSee('Crear sala · Clase A')
            ->assertSee('Actividad Elegida')
            ->assertDontSee('Actividad Otra');
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

    private function createClass(
        User $teacher,
        string $name = 'Test Class',
        array $attributes = []
    ): SchoolClass {
        return SchoolClass::create(array_merge([
            'teacher_id' => $teacher->id,
            'name' => $name,
            'description' => 'Scoping tests',
            'code' => strtoupper(fake()->unique()->bothify('SCOPE###')),
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
