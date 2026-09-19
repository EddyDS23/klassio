<?php

namespace Tests\Feature\Admin;

use App\Models\Activity;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertStatus(302)
            ->assertRedirect('/login');
    }

    public function test_students_are_redirected_to_their_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $this->actingAs($student)
            ->get(route('admin.dashboard'))
            ->assertStatus(302)
            ->assertRedirect(route('student.dashboard'));
    }

    public function test_teachers_are_redirected_to_their_dashboard(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $this->actingAs($teacher)
            ->get(route('admin.dashboard'))
            ->assertStatus(302)
            ->assertRedirect(route('teacher.dashboard'));
    }

    public function test_inactive_admins_are_logged_out(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'suspended']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(302)
            ->assertRedirect('/login');
    }

    public function test_active_admin_can_access_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('stats');
    }

    public function test_dashboard_shows_platform_metrics(): void
    {
        $admin = $this->admin();

        User::factory()->count(3)->create(['role' => 'student', 'status' => 'active']);
        User::factory()->count(2)->create(['role' => 'teacher', 'status' => 'active']);
        User::factory()->create(['role' => 'student', 'status' => 'suspended']);

        $teacher = User::where('role', 'teacher')->first();

        $class = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Historia 4B',
            'description' => 'Clase de historia',
            'code' => 'HIST4B',
            'status' => 'active',
        ]);

        $activity = Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Repaso de la independencia',
            'type' => 'crossword',
            'mode' => 'individual',
            'max_score' => 100,
            'status' => 'published',
        ]);

        $student = User::where('role', 'student')->where('email', '!=', $admin->email)->first();

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 80,
            'elapsed_seconds' => 150,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();

        $response->assertViewHas('stats', function (array $stats) {
            return $stats['users'] === 7
                && $stats['students'] === 4
                && $stats['teachers'] === 2
                && $stats['admins'] === 1
                && $stats['suspendedUsers'] === 1
                && $stats['classes'] === 1
                && $stats['activities'] === 1
                && $stats['participations'] === 1;
        });
    }

    public function test_dashboard_exposes_chart_data(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('activityTypeLabels')
            ->assertViewHas('activityTypeTotals')
            ->assertViewHas('userStatusLabels')
            ->assertViewHas('userStatusTotals');
    }
}