<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActivityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    private function publishedActivity(User $teacher): Activity
    {
        $class = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Lengua 2B',
            'description' => 'Lectura y escritura',
            'code' => 'LEN2B',
            'status' => 'active',
        ]);

        return Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Vocabulario del poema',
            'type' => 'word_search',
            'mode' => 'individual',
            'max_score' => 100,
            'status' => 'published',
        ]);
    }

    public function test_only_admins_can_list_activities(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $this->get(route('admin.activities.index'))->assertRedirect('/login');

        $this->actingAs($student)
            ->get(route('admin.activities.index'))
            ->assertRedirect(route('student.dashboard'));

        $this->actingAs($this->admin())
            ->get(route('admin.activities.index'))
            ->assertOk()
            ->assertViewHas('activities');
    }

    public function test_admin_can_list_activities_with_details(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['name' => 'Nora Vidal', 'role' => 'teacher', 'status' => 'active']);

        $activity = $this->publishedActivity($teacher);

        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 90,
            'elapsed_seconds' => 200,
        ]);

        $this->get(route('admin.activities.index'))
            ->assertOk()
            ->assertSee('Vocabulario del poema')
            ->assertSee('Lengua 2B')
            ->assertSee('Nora Vidal')
            ->assertViewHas('activities', fn ($activities) => $activities->first()?->participations_count === 1);
    }

    public function test_admin_can_search_activities(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $this->publishedActivity($teacher);

        $englishClass = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Inglés 1A',
            'description' => 'Idioma extranjero',
            'code' => 'ING1A',
            'status' => 'active',
        ]);

        Activity::create([
            'class_id' => $englishClass->id,
            'teacher_id' => $teacher->id,
            'title' => 'Irregular verbs quiz',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 40,
            'status' => 'draft',
        ]);

        $this->get(route('admin.activities.index', ['search' => 'Irregular']))
            ->assertOk()
            ->assertSee('Irregular verbs quiz')
            ->assertDontSee('Vocabulario del poema');
    }

    public function test_admin_can_filter_activities_by_type_and_status(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $classA = SchoolClass::create(['teacher_id' => $teacher->id, 'name' => 'Clase A', 'code' => 'CLA', 'status' => 'active']);
        $classB = SchoolClass::create(['teacher_id' => $teacher->id, 'name' => 'Clase B', 'code' => 'CLB', 'status' => 'active']);
        $classC = SchoolClass::create(['teacher_id' => $teacher->id, 'name' => 'Clase C', 'code' => 'CLC', 'status' => 'active']);

        Activity::create(['class_id' => $classA->id, 'teacher_id' => $teacher->id, 'title' => 'Sopa publicada', 'type' => 'word_search', 'mode' => 'individual', 'max_score' => 10, 'status' => 'published']);
        Activity::create(['class_id' => $classB->id, 'teacher_id' => $teacher->id, 'title' => 'Quiz borrador', 'type' => 'kahoot', 'mode' => 'individual', 'max_score' => 10, 'status' => 'draft']);
        Activity::create(['class_id' => $classC->id, 'teacher_id' => $teacher->id, 'title' => 'Crucigrama cerrado', 'type' => 'crossword', 'mode' => 'individual', 'max_score' => 10, 'status' => 'closed']);

        $this->get(route('admin.activities.index', ['type' => 'kahoot']))
            ->assertOk()
            ->assertSee('Quiz borrador')
            ->assertDontSee('Sopa publicada');

        $this->get(route('admin.activities.index', ['status' => 'closed']))
            ->assertOk()
            ->assertSee('Crucigrama cerrado')
            ->assertDontSee('Quiz borrador');
    }

    public function test_admin_can_view_activity_detail_with_teams_and_participations(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $student = User::factory()->create(['name' => 'Martín Vega', 'role' => 'student', 'status' => 'active']);

        $class = SchoolClass::create(['teacher_id' => $teacher->id, 'name' => 'Historia 6A', 'description' => 'Historia moderna', 'code' => 'HIS6A', 'status' => 'active']);

        $activity = Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Batallas históricas',
            'description' => 'Identifica cada batalla',
            'type' => 'matching',
            'mode' => 'team',
            'max_score' => 60,
            'time_limit' => 15,
            'attempts' => 2,
            'status' => 'published',
        ]);

        $team = Team::create(['activity_id' => $activity->id, 'name' => 'Los Conquistadores']);
        TeamMember::create(['team_id' => $team->id, 'student_id' => $student->id]);

        Participation::create([
            'activity_id' => $activity->id,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 45,
            'elapsed_seconds' => 400,
        ]);

        $this->get(route('admin.activities.show', $activity->id))
            ->assertOk()
            ->assertSee('Batallas históricas')
            ->assertSee('Los Conquistadores')
            ->assertSee('Martín Vega')
            ->assertViewHas('activity', fn ($loaded) => $loaded->id === $activity->id);
    }

    public function test_non_existent_activity_returns_404(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.activities.show', 9999))
            ->assertNotFound();
    }
}