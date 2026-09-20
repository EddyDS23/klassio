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

class AdminResultTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    private function activity(User $teacher): Activity
    {
        $class = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Física 4C',
            'description' => 'Mecánica',
            'code' => 'FIS4C',
            'status' => 'active',
        ]);

        return Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Leyes de Newton',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'status' => 'published',
        ]);
    }

    public function test_only_admins_can_list_results(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $this->get(route('admin.results.index'))->assertRedirect('/login');

        $this->actingAs($teacher)
            ->get(route('admin.results.index'))
            ->assertRedirect(route('teacher.dashboard'));

        $this->actingAs($this->admin())
            ->get(route('admin.results.index'))
            ->assertOk()
            ->assertViewHas('results');
    }

    public function test_admin_can_list_individual_and_team_results(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $student = User::factory()->create(['name' => 'Diana Cruz', 'role' => 'student', 'status' => 'active']);

        $activity = Activity::create([
            'class_id' => SchoolClass::create([
                'teacher_id' => $teacher->id,
                'name' => 'Física 4D',
                'description' => 'Mecánica II',
                'code' => 'FIS4D',
                'status' => 'active',
            ])->id,
            'teacher_id' => $teacher->id,
            'title' => 'Evaluación rápida',
            'type' => 'kahoot',
            'mode' => 'team',
            'max_score' => 100,
            'status' => 'published',
        ]);

        $team = Team::create(['activity_id' => $activity->id, 'name' => 'Los Neutrones']);
        TeamMember::create(['team_id' => $team->id, 'student_id' => $student->id]);
        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => User::factory()->create(['name' => 'Omar Ríos', 'role' => 'student', 'status' => 'active'])->id,
        ]);

        $this->activity($teacher);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 80,
            'elapsed_seconds' => 120,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'team_id' => $team->id,
            'attempt' => 2,
            'status' => 'started',
            'score' => 0,
            'elapsed_seconds' => 45,
        ]);

        $this->get(route('admin.results.index'))
            ->assertOk()
            ->assertSee('Los Neutrones')
            ->assertSee('Diana Cruz')
            ->assertSee('Omar Ríos')
            ->assertViewHas('results', fn ($results) => $results->total() === 2);
    }

    public function test_admin_can_filter_results_by_status(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $activity = $this->activity($teacher);

        Participation::create(['activity_id' => $activity->id, 'student_id' => User::factory()->create(['role' => 'student'])->id, 'attempt' => 1, 'status' => 'completed', 'score' => 100, 'elapsed_seconds' => 90]);
        Participation::create(['activity_id' => $activity->id, 'student_id' => User::factory()->create(['role' => 'student'])->id, 'attempt' => 1, 'status' => 'expired', 'score' => 0, 'elapsed_seconds' => 600]);

        $this->get(route('admin.results.index', ['status' => 'expired']))
            ->assertOk()
            ->assertViewHas('results', fn ($results) => $results->total() === 1);
    }

    public function test_admin_can_filter_results_by_activity_and_type(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $activity = $this->activity($teacher);

        $musicClass = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Música 1B',
            'description' => '',
            'code' => 'MUS1B',
            'status' => 'active',
        ]);

        $secondActivity = Activity::create([
            'class_id' => $musicClass->id,
            'teacher_id' => $teacher->id,
            'title' => 'Notas musicales',
            'type' => 'crossword',
            'mode' => 'individual',
            'max_score' => 50,
            'status' => 'published',
        ]);

        Participation::create(['activity_id' => $activity->id, 'student_id' => User::factory()->create(['role' => 'student'])->id, 'attempt' => 1, 'status' => 'completed', 'score' => 90]);
        Participation::create(['activity_id' => $secondActivity->id, 'student_id' => User::factory()->create(['role' => 'student'])->id, 'attempt' => 1, 'status' => 'completed', 'score' => 40]);

        $this->get(route('admin.results.index', ['activity_id' => $activity->id]))
            ->assertOk()
            ->assertViewHas('results', fn ($results) => $results->total() === 1);

        $this->get(route('admin.results.index', ['type' => 'crossword']))
            ->assertOk()
            ->assertViewHas('results', fn ($results) => $results->total() === 1);
    }

    public function test_admin_can_view_individual_result(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $activity = $this->activity($teacher);
        $student = User::factory()->create(['name' => 'Paula Sáenz', 'role' => 'student', 'status' => 'active']);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 85,
            'elapsed_seconds' => 300,
        ]);

        $this->get(route('admin.results.show', $participation->id))
            ->assertOk()
            ->assertSee('Paula Sáenz')
            ->assertSee('Leyes de Newton');
    }

    public function test_admin_can_view_team_result_with_members(): void
    {
        $this->actingAs($this->admin());

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $activity = $this->activity($teacher);

        $team = Team::create(['activity_id' => $activity->id, 'name' => 'Los Átomos']);
        TeamMember::create(['team_id' => $team->id, 'student_id' => User::factory()->create(['name' => 'Ana Polo', 'role' => 'student'])->id]);
        TeamMember::create(['team_id' => $team->id, 'student_id' => User::factory()->create(['name' => 'Luis Neto', 'role' => 'student'])->id]);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 60,
            'elapsed_seconds' => 500,
        ]);

        $this->get(route('admin.results.show', $participation->id))
            ->assertOk()
            ->assertSee('Los Átomos')
            ->assertSee('Ana Polo')
            ->assertSee('Luis Neto');
    }

    public function test_non_existent_result_returns_404(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.results.show', 9999))
            ->assertNotFound();
    }
}