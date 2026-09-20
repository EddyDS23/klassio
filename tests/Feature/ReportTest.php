<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function teacher(): User
    {
        return User::factory()->create([
            'role' => 'teacher',
            'status' => 'active',
        ]);
    }

    private function student(): User
    {
        return User::factory()->create([
            'role' => 'student',
            'status' => 'active',
        ]);
    }

    private function createClass(User $teacher): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Clase de prueba',
            'code' => strtoupper(fake()->bothify('???###')),
            'status' => 'active',
        ]);
    }

    private function enroll(
        SchoolClass $class,
        User $student,
        string $status = 'active'
    ): Enrollment {
        return Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => $status,
        ]);
    }

    private function createActivity(
        SchoolClass $class,
        User $teacher,
        array $overrides = []
    ): Activity {
        return Activity::create(array_merge([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Actividad de prueba',
            'description' => 'Actividad para pruebas',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
            'due_at' => null,
            'status' => 'published',
        ], $overrides));
    }

    private function createTeam(
        Activity $activity,
        string $name = 'Equipo 1'
    ): Team {
        return Team::create([
            'activity_id' => $activity->id,
            'name' => $name,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Individual Reports
    |--------------------------------------------------------------------------
    */

    public function test_teacher_can_view_individual_activity_report(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'individual',
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.activities.report', $activity->id));

        $response->assertOk();

        $response->assertViewIs('teacher.rankings.report');

        $response->assertViewHas('activity', function ($viewActivity) use ($activity) {
            return $viewActivity->id === $activity->id;
        });

        $response->assertViewHas('summary');
    }

    public function test_other_teacher_cannot_view_activity_report(): void
    {
        $teacher = $this->teacher();
        $otherTeacher = $this->teacher();

        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'individual',
        ]);

        $response = $this
            ->actingAs($otherTeacher)
            ->get(route('teacher.activities.report', $activity->id));

        $response->assertForbidden();
    }

    public function test_student_cannot_view_activity_report(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);

        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'individual',
        ]);

        /*
         * The report route belongs to the teacher middleware group.
         * Therefore a student is redirected before reaching the policy.
         */
        $response = $this
            ->actingAs($student)
            ->get(route('teacher.activities.report', $activity->id));

        $response->assertRedirect();
    }

    public function test_individual_report_counts_students_by_participation_status(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $students = [
            $this->student(),
            $this->student(),
            $this->student(),
            $this->student(),
            $this->student(),
        ];

        foreach ($students as $student) {
            $this->enroll($class, $student);
        }

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'individual',
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $students[0]->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 80,
            'completed_at' => now(),
            'elapsed_seconds' => 30,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $students[1]->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
            'completed_at' => null,
            'elapsed_seconds' => 10,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $students[2]->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'abandoned',
            'score' => 20,
            'completed_at' => null,
            'elapsed_seconds' => 20,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $students[3]->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'expired',
            'score' => 30,
            'completed_at' => null,
            'elapsed_seconds' => 25,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.activities.report', $activity->id));

        $response->assertOk();

        $response->assertViewHas('summary', function ($summary) {
            return $summary['total_students'] == 5
                && $summary['participated'] == 4
                && $summary['not_participated'] == 1
                && $summary['completed'] == 1
                && $summary['started'] == 1
                && $summary['abandoned'] == 1
                && $summary['expired'] == 1;
        });
    }

    public function test_individual_report_calculates_performance_from_completed_participations(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $studentOne = $this->student();
        $studentTwo = $this->student();
        $studentThree = $this->student();

        $this->enroll($class, $studentOne);
        $this->enroll($class, $studentTwo);
        $this->enroll($class, $studentThree);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'individual',
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $studentOne->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 80,
            'completed_at' => now(),
            'elapsed_seconds' => 20,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $studentTwo->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 100,
            'completed_at' => now(),
            'elapsed_seconds' => 40,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $studentThree->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'started',
            'score' => 50,
            'completed_at' => null,
            'elapsed_seconds' => 100,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.activities.report', $activity->id));

        $response->assertOk();

        $response->assertViewHas('summary', function ($summary) {
            return $summary['average_score'] == 90
                && $summary['best_score'] == 100
                && $summary['average_time'] == 30;
        });
    }

    public function test_individual_report_is_scoped_to_activity(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $student = $this->student();

        $this->enroll($class, $student);

        $targetActivity = $this->createActivity($class, $teacher, [
            'mode' => 'individual',
            'title' => 'Actividad objetivo',
        ]);

        $otherActivity = $this->createActivity($class, $teacher, [
            'mode' => 'individual',
            'title' => 'Otra actividad',
        ]);

        Participation::create([
            'activity_id' => $targetActivity->id,
            'student_id' => $student->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 80,
            'completed_at' => now(),
            'elapsed_seconds' => 40,
        ]);

        Participation::create([
            'activity_id' => $otherActivity->id,
            'student_id' => $student->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 20,
            'completed_at' => now(),
            'elapsed_seconds' => 100,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.activities.report', $targetActivity->id));

        $response->assertOk();

        $response->assertViewHas('summary', function ($summary) {
            return $summary['participated'] == 1
                && $summary['completed'] == 1
                && $summary['average_score'] == 80
                && $summary['best_score'] == 80
                && $summary['average_time'] == 40;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Team Reports
    |--------------------------------------------------------------------------
    */

    public function test_teacher_can_view_team_activity_report(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
        ]);

        $this->createTeam($activity, 'Equipo 1');

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.activities.report', $activity->id));

        $response->assertOk();

        $response->assertViewIs('teacher.rankings.report');

        $response->assertViewHas('activity', function ($viewActivity) use ($activity) {
            return $viewActivity->id === $activity->id;
        });

        $response->assertViewHas('summary');
    }

    public function test_team_report_counts_teams_instead_of_students(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $studentOne = $this->student();
        $studentTwo = $this->student();
        $studentThree = $this->student();

        $this->enroll($class, $studentOne);
        $this->enroll($class, $studentTwo);
        $this->enroll($class, $studentThree);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
        ]);

        $teamOne = $this->createTeam($activity, 'Equipo 1');
        $this->createTeam($activity, 'Equipo 2');

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $teamOne->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 90,
            'completed_at' => now(),
            'elapsed_seconds' => 50,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.activities.report', $activity->id));

        $response->assertOk();

        $response->assertViewHas('summary', function ($summary) {
            return $summary['total_teams'] == 2
                && $summary['participated'] == 1
                && $summary['not_participated'] == 1
                && $summary['completed'] == 1;
        });
    }

    public function test_team_report_includes_team_participations(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
        ]);

        $team = $this->createTeam($activity, 'Equipo 1');

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 85,
            'completed_at' => now(),
            'elapsed_seconds' => 45,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.activities.report', $activity->id));

        $response->assertOk();

        $response->assertViewHas('summary', function ($summary) {
            return $summary['total_teams'] == 1
                && $summary['participated'] == 1
                && $summary['completed'] == 1
                && $summary['average_score'] == 85
                && $summary['best_score'] == 85
                && $summary['average_time'] == 45;
        });
    }

    public function test_team_report_uses_best_attempt_per_team(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
        ]);

        $team = $this->createTeam($activity, 'Equipo 1');

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 70,
            'completed_at' => now(),
            'elapsed_seconds' => 20,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 2,
            'status' => 'completed',
            'score' => 90,
            'completed_at' => now(),
            'elapsed_seconds' => 40,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.activities.report', $activity->id));

        $response->assertOk();

        $response->assertViewHas('summary', function ($summary) {
            return $summary['total_teams'] == 1
                && $summary['participated'] == 1
                && $summary['completed'] == 1
                && $summary['average_score'] == 90
                && $summary['best_score'] == 90
                && $summary['average_time'] == 40;
        });
    }

    public function test_team_report_uses_faster_attempt_when_score_is_equal(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
        ]);

        $team = $this->createTeam($activity, 'Equipo 1');

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 90,
            'completed_at' => now(),
            'elapsed_seconds' => 60,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 2,
            'status' => 'completed',
            'score' => 90,
            'completed_at' => now(),
            'elapsed_seconds' => 30,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.activities.report', $activity->id));

        $response->assertOk();

        $response->assertViewHas('summary', function ($summary) {
            return $summary['total_teams'] == 1
                && $summary['participated'] == 1
                && $summary['completed'] == 1
                && $summary['average_score'] == 90
                && $summary['best_score'] == 90
                && $summary['average_time'] == 30;
        });
    }

    public function test_team_report_ignores_participations_from_another_activity(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $targetActivity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
            'title' => 'Actividad objetivo',
        ]);

        $otherActivity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
            'title' => 'Otra actividad',
        ]);

        $targetTeam = $this->createTeam($targetActivity, 'Equipo objetivo');
        $otherTeam = $this->createTeam($otherActivity, 'Equipo externo');

        Participation::create([
            'activity_id' => $targetActivity->id,
            'student_id' => null,
            'team_id' => $targetTeam->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 80,
            'completed_at' => now(),
            'elapsed_seconds' => 40,
        ]);

        Participation::create([
            'activity_id' => $otherActivity->id,
            'student_id' => null,
            'team_id' => $otherTeam->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 100,
            'completed_at' => now(),
            'elapsed_seconds' => 10,
        ]);

        $response = $this
            ->actingAs($teacher)
            ->get(route('teacher.activities.report', $targetActivity->id));

        $response->assertOk();

        $response->assertViewHas('summary', function ($summary) {
            return $summary['total_teams'] == 1
                && $summary['participated'] == 1
                && $summary['completed'] == 1
                && $summary['average_score'] == 80
                && $summary['best_score'] == 80
                && $summary['average_time'] == 40;
        });
    }
}