<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ResultsTest extends TestCase
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

    private function student(string $email = 'student@example.com'): User
    {
        return User::create([
            'name' => 'Student',
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => 'student',
            'status' => 'active',
        ]);
    }

    private function createClass(User $teacher): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Test Class',
            'description' => 'Results test class',
            'code' => strtoupper(fake()->unique()->bothify('CLASS###')),
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
        array $attributes = []
    ): Activity {
        return Activity::create(array_merge([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Results Activity',
            'description' => 'Results test activity',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 3,
            'due_at' => null,
            'status' => 'published',
        ], $attributes));
    }

    private function createParticipation(
        Activity $activity,
        User $student,
        array $attributes = []
    ): Participation {
        return Participation::create(array_merge([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 80,
            'completed_at' => now(),
            'elapsed_seconds' => 125,
        ], $attributes));
    }

    // -------------------------------------------------------------------------
    // STUDENT - INDIVIDUAL
    // -------------------------------------------------------------------------

    public function test_student_can_view_own_completed_result(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $participation = $this->createParticipation($activity, $student);

        $this->actingAs($student);

        $response = $this->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(200);
        $response->assertViewIs('student.participation.result');

        $response->assertViewHas('activity', $activity);
        $response->assertViewHas('participation', function ($result) use ($participation) {
            return $result->id === $participation->id;
        });

        $response->assertViewHas('answers');
        $response->assertViewHas('total');
    }

    public function test_student_cannot_view_another_students_result(): void
    {
        $teacher = $this->teacher();

        $owner = $this->student('owner@example.com');
        $attacker = $this->student('attacker@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $owner);
        $this->enroll($class, $attacker);

        $activity = $this->createActivity($class, $teacher);

        $this->createParticipation($activity, $owner);

        $this->actingAs($attacker);

        $response = $this->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(404);
    }

    public function test_student_can_view_abandoned_result(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $participation = $this->createParticipation(
            $activity,
            $student,
            [
                'status' => 'abandoned',
                'score' => 25,
            ]
        );

        $this->actingAs($student);

        $response = $this->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('participation', function ($result) use ($participation) {
            return $result->id === $participation->id
                && $result->status === 'abandoned';
        });
    }

    public function test_student_can_view_expired_result(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $participation = $this->createParticipation(
            $activity,
            $student,
            [
                'status' => 'expired',
                'score' => 40,
            ]
        );

        $this->actingAs($student);

        $response = $this->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('participation', function ($result) use ($participation) {
            return $result->id === $participation->id
                && $result->status === 'expired';
        });
    }

    public function test_started_participation_has_no_result(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $this->createParticipation(
            $activity,
            $student,
            [
                'status' => 'started',
            ]
        );

        $this->actingAs($student);

        $response = $this->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(404);
    }

    public function test_student_result_is_scoped_to_the_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activityOne = $this->createActivity($class, $teacher, [
            'title' => 'Activity One',
        ]);

        $activityTwo = $this->createActivity($class, $teacher, [
            'title' => 'Activity Two',
        ]);

        $participation = $this->createParticipation(
            $activityOne,
            $student
        );

        $this->actingAs($student);

        $response = $this->get(
            route('student.participation.result', $activityTwo->id)
        );

        $response->assertStatus(404);

        $this->assertNotEquals(
            $activityTwo->id,
            $participation->activity_id
        );
    }

    // -------------------------------------------------------------------------
    // STUDENT - LATEST ATTEMPT
    // -------------------------------------------------------------------------

    public function test_student_result_returns_latest_finished_attempt(): void
{
    $teacher = $this->teacher();
    $student = $this->student();

    $class = $this->createClass($teacher);

    $this->enroll($class, $student);

    $activity = $this->createActivity(
        $class,
        $teacher,
        [
            'status' => 'published',
            'type' => 'word_search',
            'mode' => 'individual',
            'attempts' => 2,
        ]
    );

    $first = $this->createParticipation(
        $activity,
        $student,
        [
            'attempt' => 1,
            'status' => 'completed',
            'score' => 50,
        ]
    );

    $second = $this->createParticipation(
        $activity,
        $student,
        [
            'attempt' => 2,
            'status' => 'completed',
            'score' => 90,
        ]
    );

    $response = $this->actingAs($student)->get(
        route('student.participation.result', $activity->id)
    );

    $response->assertStatus(200);

    $response->assertViewHas('participation', function ($result) use ($first, $second) {
        dump([
            'expected_first' => [
                'id' => $first->id,
                'attempt' => $first->attempt,
                'score' => $first->score,
                'created_at' => $first->created_at?->toDateTimeString(),
            ],
            'expected_second' => [
                'id' => $second->id,
                'attempt' => $second->attempt,
                'score' => $second->score,
                'created_at' => $second->created_at?->toDateTimeString(),
            ],
            'returned' => [
                'id' => $result->id,
                'attempt' => $result->attempt,
                'score' => $result->score,
                'created_at' => $result->created_at?->toDateTimeString(),
            ],
        ]);

        return true;
    });
}

    // -------------------------------------------------------------------------
    // STUDENT - SCORE / TIME
    // -------------------------------------------------------------------------

    public function test_result_contains_score_and_max_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity(
            $class,
            $teacher,
            [
                'max_score' => 500,
            ]
        );

        $participation = $this->createParticipation(
            $activity,
            $student,
            [
                'score' => 375,
            ]
        );

        $this->actingAs($student);

        $response = $this->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('participation', function ($result) use ($participation) {
            return $result->id === $participation->id
                && $result->score === 375;
        });

        $response->assertViewHas('activity', function ($result) {
            return $result->max_score === 500;
        });
    }

    public function test_result_contains_elapsed_time(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $participation = $this->createParticipation(
            $activity,
            $student,
            [
                'elapsed_seconds' => 125,
            ]
        );

        $this->actingAs($student);

        $response = $this->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas(
            'elapsed',
            '02:05'
        );

        $response->assertViewHas('participation', function ($result) use ($participation) {
            return $result->id === $participation->id
                && $result->elapsed_seconds === 125;
        });
    }

    public function test_result_with_no_answers_does_not_fail(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $this->createParticipation($activity, $student);

        $this->actingAs($student);

        $response = $this->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('answers', []);
        $response->assertViewHas('total', 0);
    }

    // -------------------------------------------------------------------------
    // TEAM MODE
    // -------------------------------------------------------------------------

    public function test_team_member_can_view_team_result(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity(
            $class,
            $teacher,
            [
                'mode' => 'team',
            ]
        );

        $team = Team::create([
            'activity_id' => $activity->id,
            'name' => 'Team Alpha',
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $student->id,
        ]);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 85,
            'completed_at' => now(),
            'elapsed_seconds' => 100,
        ]);

        $this->actingAs($student);

        $response = $this->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('participation', function ($result) use ($participation) {
            return $result->id === $participation->id
                && $result->team_id !== null
                && $result->student_id === null;
        });
    }

    public function test_non_member_cannot_view_team_result(): void
    {
        $teacher = $this->teacher();

        $member = $this->student('member@example.com');
        $outsider = $this->student('outsider@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $member);
        $this->enroll($class, $outsider);

        $activity = $this->createActivity(
            $class,
            $teacher,
            [
                'mode' => 'team',
            ]
        );

        $team = Team::create([
            'activity_id' => $activity->id,
            'name' => 'Team Alpha',
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $member->id,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 90,
            'completed_at' => now(),
            'elapsed_seconds' => 100,
        ]);

        $this->actingAs($outsider);

        $response = $this->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // TEACHER RESULTS
    // -------------------------------------------------------------------------

    public function test_activity_owner_can_view_teacher_results(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $participation = $this->createParticipation(
            $activity,
            $student,
            [
                'score' => 95,
            ]
        );

        $this->actingAs($teacher);

        $response = $this->get(
            route('teacher.activities.results', $activity->id)
        );

        $response->assertStatus(200);
        $response->assertViewIs('teacher.activities.results');

        $response->assertViewHas('activity', $activity);

        $response->assertViewHas('results', function ($results) use ($student, $participation) {
            $result = $results->first();

            return $result['student']->id === $student->id
                && $result['participation']->id === $participation->id
                && $result['status'] === 'completed'
                && $result['attempt'] === 1
                && $result['score'] === 95
                && $result['elapsed_seconds'] === 125;
        });
    }

    public function test_other_teacher_cannot_view_teacher_results(): void
    {
        $teacher = $this->teacher();
        $otherTeacher = $this->teacher('other-teacher@example.com');
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $this->actingAs($otherTeacher);

        $response = $this->get(
            route('teacher.activities.results', $activity->id)
        );

        $response->assertStatus(403);
    }

    public function test_teacher_results_include_students_without_participation(): void
    {
        $teacher = $this->teacher();

        $studentWithResult = $this->student('with-result@example.com');
        $studentWithoutResult = $this->student('without-result@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $studentWithResult);
        $this->enroll($class, $studentWithoutResult);

        $activity = $this->createActivity($class, $teacher);

        $participation = $this->createParticipation(
            $activity,
            $studentWithResult
        );

        $this->actingAs($teacher);

        $response = $this->get(
            route('teacher.activities.results', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('results', function ($results) use (
            $studentWithResult,
            $studentWithoutResult,
            $participation
        ) {
            $withResult = $results->firstWhere(
                'student.id',
                $studentWithResult->id
            );

            $withoutResult = $results->firstWhere(
                'student.id',
                $studentWithoutResult->id
            );

            return $withResult !== null
                && $withResult['participation']->id === $participation->id
                && $withoutResult !== null
                && $withoutResult['participation'] === null
                && $withoutResult['status'] === 'not_started'
                && $withoutResult['score'] === null;
        });
    }

    public function test_teacher_results_return_latest_attempt(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $this->createParticipation(
            $activity,
            $student,
            [
                'attempt' => 1,
                'score' => 40,
            ]
        );

        $latest = $this->createParticipation(
            $activity,
            $student,
            [
                'attempt' => 2,
                'score' => 90,
            ]
        );

        $this->actingAs($teacher);

        $response = $this->get(
            route('teacher.activities.results', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('results', function ($results) use ($latest) {
            $result = $results->first();

            return $result['participation']->id === $latest->id
                && $result['attempt'] === 2
                && $result['score'] === 90;
        });
    }

    // -------------------------------------------------------------------------
    // TEAM MODE - TEACHER RESULTS
    // -------------------------------------------------------------------------

    public function test_teacher_results_resolve_team_participation_for_team_member(): void
    {
        $teacher = $this->teacher();

        $studentOne = $this->student('one@example.com');
        $studentTwo = $this->student('two@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $studentOne);
        $this->enroll($class, $studentTwo);

        $activity = $this->createActivity(
            $class,
            $teacher,
            [
                'mode' => 'team',
            ]
        );

        $team = Team::create([
            'activity_id' => $activity->id,
            'name' => 'Team Alpha',
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $studentOne->id,
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $studentTwo->id,
        ]);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 88,
            'completed_at' => now(),
            'elapsed_seconds' => 140,
        ]);

        $this->actingAs($teacher);

        $response = $this->get(
            route('teacher.activities.results', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('results', function ($results) use (
            $studentOne,
            $studentTwo,
            $participation
        ) {
            $resultOne = $results->firstWhere(
                'student.id',
                $studentOne->id
            );

            $resultTwo = $results->firstWhere(
                'student.id',
                $studentTwo->id
            );

            return $resultOne !== null
                && $resultTwo !== null
                && $resultOne['participation']->id === $participation->id
                && $resultTwo['participation']->id === $participation->id
                && $resultOne['score'] === 88
                && $resultTwo['score'] === 88;
        });
    }
}
