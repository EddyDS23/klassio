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

class RankingTest extends TestCase
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
            'name' => 'Ranking Class',
            'description' => 'Ranking tests',
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
            'title' => 'Ranking Activity',
            'description' => 'Ranking test activity',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 2,
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
            'score' => 0,
            'elapsed_seconds' => 60,
        ], $attributes));
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESS
    |--------------------------------------------------------------------------
    */

    public function test_activity_owner_can_view_ranking(): void
    {
        $teacher = $this->teacher();

        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher);

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);
        $response->assertViewIs('teacher.rankings.ranking');
        $response->assertViewHas('activity');
        $response->assertViewHas('ranking');
        $response->assertViewHas('topThree');
    }

    public function test_other_teacher_cannot_view_ranking(): void
    {
        $owner = $this->teacher('owner@example.com');
        $otherTeacher = $this->teacher('other@example.com');

        $class = $this->createClass($owner);

        $activity = $this->createActivity($class, $owner);

        $response = $this->actingAs($otherTeacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertForbidden();
    }

    public function test_student_cannot_access_teacher_ranking_route(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $response = $this->actingAs($student)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertRedirect();
    }

    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL RANKING
    |--------------------------------------------------------------------------
    */

    public function test_ranking_contains_only_completed_participations(): void
    {
        $teacher = $this->teacher();

        $studentOne = $this->student('one@example.com');
        $studentTwo = $this->student('two@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $studentOne);
        $this->enroll($class, $studentTwo);

        $activity = $this->createActivity($class, $teacher);

        $completed = $this->createParticipation(
            $activity,
            $studentOne,
            [
                'score' => 80,
                'status' => 'completed',
            ]
        );

        $this->createParticipation(
            $activity,
            $studentTwo,
            [
                'score' => 100,
                'status' => 'abandoned',
            ]
        );

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use ($completed) {
            return $ranking->count() === 1
                && $ranking->first()['participation']->id === $completed->id
                && $ranking->first()['score'] === 80;
        });
    }

    public function test_ranking_orders_by_score_descending(): void
    {
        $teacher = $this->teacher();

        $studentOne = $this->student('one@example.com');
        $studentTwo = $this->student('two@example.com');
        $studentThree = $this->student('three@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $studentOne);
        $this->enroll($class, $studentTwo);
        $this->enroll($class, $studentThree);

        $activity = $this->createActivity($class, $teacher);

        $this->createParticipation(
            $activity,
            $studentOne,
            ['score' => 70]
        );

        $this->createParticipation(
            $activity,
            $studentTwo,
            ['score' => 95]
        );

        $this->createParticipation(
            $activity,
            $studentThree,
            ['score' => 85]
        );

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use (
            $studentOne,
            $studentTwo,
            $studentThree
        ) {
            return $ranking->count() === 3
                && $ranking[0]['student']->id === $studentTwo->id
                && $ranking[0]['score'] === 95
                && $ranking[0]['position'] === 1
                && $ranking[1]['student']->id === $studentThree->id
                && $ranking[1]['score'] === 85
                && $ranking[1]['position'] === 2
                && $ranking[2]['student']->id === $studentOne->id
                && $ranking[2]['score'] === 70
                && $ranking[2]['position'] === 3;
        });
    }

    public function test_ranking_uses_lower_elapsed_time_as_tiebreaker(): void
    {
        $teacher = $this->teacher();

        $faster = $this->student('faster@example.com');
        $slower = $this->student('slower@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $faster);
        $this->enroll($class, $slower);

        $activity = $this->createActivity($class, $teacher);

        $this->createParticipation(
            $activity,
            $slower,
            [
                'score' => 90,
                'elapsed_seconds' => 120,
            ]
        );

        $this->createParticipation(
            $activity,
            $faster,
            [
                'score' => 90,
                'elapsed_seconds' => 60,
            ]
        );

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use (
            $faster,
            $slower
        ) {
            return $ranking->count() === 2
                && $ranking[0]['student']->id === $faster->id
                && $ranking[0]['elapsed_seconds'] === 60
                && $ranking[1]['student']->id === $slower->id
                && $ranking[1]['elapsed_seconds'] === 120;
        });
    }

    public function test_ranking_uses_best_participation_for_each_student(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $first = $this->createParticipation(
            $activity,
            $student,
            [
                'attempt' => 1,
                'score' => 70,
                'elapsed_seconds' => 40,
            ]
        );

        $second = $this->createParticipation(
            $activity,
            $student,
            [
                'attempt' => 2,
                'score' => 90,
                'elapsed_seconds' => 100,
            ]
        );

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use (
            $student,
            $first,
            $second
        ) {
            return $ranking->count() === 1
                && $ranking[0]['student']->id === $student->id
                && $ranking[0]['participation']->id === $second->id
                && $ranking[0]['participation']->id !== $first->id
                && $ranking[0]['score'] === 90
                && $ranking[0]['attempt'] === 2;
        });
    }

    public function test_ranking_prefers_faster_attempt_when_score_is_equal(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $slower = $this->createParticipation(
            $activity,
            $student,
            [
                'attempt' => 1,
                'score' => 90,
                'elapsed_seconds' => 120,
            ]
        );

        $faster = $this->createParticipation(
            $activity,
            $student,
            [
                'attempt' => 2,
                'score' => 90,
                'elapsed_seconds' => 60,
            ]
        );

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use (
            $faster,
            $slower
        ) {
            return $ranking->count() === 1
                && $ranking[0]['participation']->id === $faster->id
                && $ranking[0]['participation']->id !== $slower->id
                && $ranking[0]['elapsed_seconds'] === 60;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | TOP THREE
    |--------------------------------------------------------------------------
    */

    public function test_top_three_contains_at_most_three_entries(): void
    {
        $teacher = $this->teacher();

        $students = [
            $this->student('one@example.com'),
            $this->student('two@example.com'),
            $this->student('three@example.com'),
            $this->student('four@example.com'),
            $this->student('five@example.com'),
        ];

        $class = $this->createClass($teacher);

        foreach ($students as $student) {
            $this->enroll($class, $student);
        }

        $activity = $this->createActivity($class, $teacher);

        foreach ($students as $index => $student) {
            $this->createParticipation(
                $activity,
                $student,
                [
                    'score' => 100 - ($index * 10),
                ]
            );
        }

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('topThree', function ($topThree) {
            return $topThree->count() === 3
                && $topThree[0]['position'] === 1
                && $topThree[1]['position'] === 2
                && $topThree[2]['position'] === 3;
        });
    }

    public function test_top_three_is_ordered_by_ranking_position(): void
    {
        $teacher = $this->teacher();

        $first = $this->student('first@example.com');
        $second = $this->student('second@example.com');
        $third = $this->student('third@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $first);
        $this->enroll($class, $second);
        $this->enroll($class, $third);

        $activity = $this->createActivity($class, $teacher);

        $this->createParticipation(
            $activity,
            $first,
            ['score' => 100]
        );

        $this->createParticipation(
            $activity,
            $second,
            ['score' => 90]
        );

        $this->createParticipation(
            $activity,
            $third,
            ['score' => 80]
        );

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('topThree', function ($topThree) use (
            $first,
            $second,
            $third
        ) {
            return $topThree[0]['student']->id === $first->id
                && $topThree[1]['student']->id === $second->id
                && $topThree[2]['student']->id === $third->id;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | TEAM RANKING
    |--------------------------------------------------------------------------
    */

    public function test_team_ranking_contains_one_entry_per_team(): void
    {
        $teacher = $this->teacher();

        $studentOne = $this->student('one@example.com');
        $studentTwo = $this->student('two@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $studentOne);
        $this->enroll($class, $studentTwo);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
        ]);

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

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 85,
            'elapsed_seconds' => 70,
        ]);

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use ($team) {
            return $ranking->count() === 1
                && $ranking[0]['team']->id === $team->id
                && $ranking[0]['score'] === 85
                && $ranking[0]['position'] === 1;
        });
    }

    public function test_team_ranking_uses_best_participation_for_each_team(): void
    {
        $teacher = $this->teacher();

        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
        ]);

        $team = Team::create([
            'activity_id' => $activity->id,
            'name' => 'Team Alpha',
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $student->id,
        ]);

        $first = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 60,
            'elapsed_seconds' => 40,
        ]);

        $second = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 2,
            'status' => 'completed',
            'score' => 95,
            'elapsed_seconds' => 90,
        ]);

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use (
            $team,
            $first,
            $second
        ) {
            return $ranking->count() === 1
                && $ranking[0]['team']->id === $team->id
                && $ranking[0]['participation']->id === $second->id
                && $ranking[0]['participation']->id !== $first->id
                && $ranking[0]['score'] === 95;
        });
    }

    public function test_team_ranking_orders_teams_by_score(): void
    {
        $teacher = $this->teacher();

        $studentOne = $this->student('one@example.com');
        $studentTwo = $this->student('two@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $studentOne);
        $this->enroll($class, $studentTwo);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
        ]);

        $teamOne = Team::create([
            'activity_id' => $activity->id,
            'name' => 'Team One',
        ]);

        $teamTwo = Team::create([
            'activity_id' => $activity->id,
            'name' => 'Team Two',
        ]);

        TeamMember::create([
            'team_id' => $teamOne->id,
            'student_id' => $studentOne->id,
        ]);

        TeamMember::create([
            'team_id' => $teamTwo->id,
            'student_id' => $studentTwo->id,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $teamOne->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 70,
            'elapsed_seconds' => 50,
        ]);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $teamTwo->id,
            'attempt' => 1,
            'status' => 'completed',
            'score' => 95,
            'elapsed_seconds' => 80,
        ]);

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use (
            $teamOne,
            $teamTwo
        ) {
            return $ranking->count() === 2
                && $ranking[0]['team']->id === $teamTwo->id
                && $ranking[0]['score'] === 95
                && $ranking[0]['position'] === 1
                && $ranking[1]['team']->id === $teamOne->id
                && $ranking[1]['score'] === 70
                && $ranking[1]['position'] === 2;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | EMPTY RANKING
    |--------------------------------------------------------------------------
    */

    public function test_ranking_can_be_empty_when_there_are_no_completed_participations(): void
    {
        $teacher = $this->teacher();

        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher);

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) {
            return $ranking->isEmpty();
        });

        $response->assertViewHas('topThree', function ($topThree) {
            return $topThree->isEmpty();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVITY SCOPE
    |--------------------------------------------------------------------------
    */

    public function test_ranking_does_not_include_participations_from_another_activity(): void
    {
        $teacher = $this->teacher();

        $studentOne = $this->student('one@example.com');
        $studentTwo = $this->student('two@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $studentOne);
        $this->enroll($class, $studentTwo);

        $activityOne = $this->createActivity($class, $teacher, [
            'title' => 'Activity One',
        ]);

        $activityTwo = $this->createActivity($class, $teacher, [
            'title' => 'Activity Two',
        ]);

        $participationOne = $this->createParticipation(
            $activityOne,
            $studentOne,
            ['score' => 80]
        );

        $participationTwo = $this->createParticipation(
            $activityTwo,
            $studentTwo,
            ['score' => 100]
        );

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activityOne->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use (
            $participationOne,
            $participationTwo
        ) {
            return $ranking->count() === 1
                && $ranking[0]['participation']->id === $participationOne->id
                && $ranking[0]['participation']->id !== $participationTwo->id;
        });
    }
}
