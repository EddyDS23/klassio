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
use Tests\TestCase;

class TeamTest extends TestCase
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

    private function student(string $email): User
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
            'description' => 'Team tests',
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

    private function createTeamActivity(
        SchoolClass $class,
        User $teacher,
        array $attributes = []
    ): Activity {
        return Activity::create(array_merge([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Team Activity',
            'description' => 'Team test activity',
            'type' => 'kahoot',
            'mode' => 'team',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
            'due_at' => null,
            'status' => 'draft',
        ], $attributes));
    }

    private function createTeam(
        Activity $activity,
        string $name = 'Team Alpha'
    ): Team {
        return Team::create([
            'activity_id' => $activity->id,
            'name' => $name,
        ]);
    }

    public function test_owner_teacher_can_view_team_management(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createTeamActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->get(route('teacher.teams.index', $activity->id));

        $response->assertOk();
    }

    public function test_non_owner_teacher_cannot_view_team_management(): void
    {
        $owner = $this->teacher('owner@example.com');
        $otherTeacher = $this->teacher('other@example.com');

        $class = $this->createClass($owner);
        $activity = $this->createTeamActivity($class, $owner);

        $response = $this->actingAs($otherTeacher)
            ->get(route('teacher.teams.index', $activity->id));

        $response->assertForbidden();
    }

    public function test_student_cannot_access_team_management(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $class = $this->createClass($teacher);
        $activity = $this->createTeamActivity($class, $teacher);

        $response = $this->actingAs($student)
            ->get(route('teacher.teams.index', $activity->id));

        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_owner_teacher_can_create_team(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createTeamActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.teams.store', $activity->id), [
                'name' => 'Team Alpha',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('teams', [
            'activity_id' => $activity->id,
            'name' => 'Team Alpha',
        ]);
    }

    public function test_other_teacher_cannot_create_team(): void
    {
        $owner = $this->teacher('owner@example.com');
        $otherTeacher = $this->teacher('other@example.com');

        $class = $this->createClass($owner);
        $activity = $this->createTeamActivity($class, $owner);

        $response = $this->actingAs($otherTeacher)
            ->post(route('teacher.teams.store', $activity->id), [
                'name' => 'Unauthorized Team',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('teams', [
            'activity_id' => $activity->id,
            'name' => 'Unauthorized Team',
        ]);
    }

    public function test_student_cannot_create_team(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $class = $this->createClass($teacher);
        $activity = $this->createTeamActivity($class, $teacher);

        $response = $this->actingAs($student)
            ->post(route('teacher.teams.store', $activity->id), [
                'name' => 'Unauthorized Team',
            ]);

        $response->assertRedirect(route('student.dashboard'));

        $this->assertDatabaseMissing('teams', [
            'activity_id' => $activity->id,
            'name' => 'Unauthorized Team',
        ]);
    }

    public function test_team_is_created_for_the_correct_activity(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activityA = $this->createTeamActivity($class, $teacher, [
            'title' => 'Activity A',
        ]);

        $activityB = $this->createTeamActivity($class, $teacher, [
            'title' => 'Activity B',
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.teams.store', $activityA->id), [
                'name' => 'Team A',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('teams', [
            'activity_id' => $activityA->id,
            'name' => 'Team A',
        ]);

        $this->assertDatabaseMissing('teams', [
            'activity_id' => $activityB->id,
            'name' => 'Team A',
        ]);
    }

    public function test_owner_teacher_can_add_enrolled_student_to_team(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createTeamActivity($class, $teacher);
        $team = $this->createTeam($activity);

        $response = $this->actingAs($teacher)
            ->post(
                route('teacher.teams.members.add', [
                    $activity->id,
                    $team->id,
                ]),
                ['student_id' => $student->id]
            );

        $response->assertRedirect();

        $this->assertDatabaseHas('team_members', [
            'team_id' => $team->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_cannot_add_unenrolled_student_to_team(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $class = $this->createClass($teacher);

        $activity = $this->createTeamActivity($class, $teacher);
        $team = $this->createTeam($activity);

        $response = $this->actingAs($teacher)
            ->post(
                route('teacher.teams.members.add', [
                    $activity->id,
                    $team->id,
                ]),
                ['student_id' => $student->id]
            );

        $response->assertRedirect();
        $response->assertSessionHasErrors('student_id');

        $this->assertDatabaseMissing('team_members', [
            'team_id' => $team->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_student_cannot_be_added_twice_to_same_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createTeamActivity($class, $teacher);
        $teamA = $this->createTeam($activity, 'Team A');
        $teamB = $this->createTeam($activity, 'Team B');

        $this->actingAs($teacher)
            ->post(
                route('teacher.teams.members.add', [
                    $activity->id,
                    $teamA->id,
                ]),
                ['student_id' => $student->id]
            )
            ->assertRedirect();

        $response = $this->actingAs($teacher)
            ->post(
                route('teacher.teams.members.add', [
                    $activity->id,
                    $teamB->id,
                ]),
                ['student_id' => $student->id]
            );

        $response->assertRedirect();
        $response->assertSessionHasErrors('student_id');

        $this->assertSame(
            1,
            TeamMember::where('student_id', $student->id)
                ->whereIn('team_id', [$teamA->id, $teamB->id])
                ->count()
        );
    }

    public function test_cannot_add_student_from_another_activity_context(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $classA = $this->createClass($teacher);
        $classB = $this->createClass($teacher);

        $this->enroll($classB, $student);

        $activityA = $this->createTeamActivity($classA, $teacher);
        $teamA = $this->createTeam($activityA);

        $response = $this->actingAs($teacher)
            ->post(
                route('teacher.teams.members.add', [
                    $activityA->id,
                    $teamA->id,
                ]),
                ['student_id' => $student->id]
            );

        $response->assertRedirect();
        $response->assertSessionHasErrors('student_id');

        $this->assertDatabaseMissing('team_members', [
            'team_id' => $teamA->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_other_teacher_cannot_modify_team_members(): void
    {
        $owner = $this->teacher('owner@example.com');
        $otherTeacher = $this->teacher('other@example.com');
        $student = $this->student('student@example.com');

        $class = $this->createClass($owner);
        $this->enroll($class, $student);

        $activity = $this->createTeamActivity($class, $owner);
        $team = $this->createTeam($activity);

        $response = $this->actingAs($otherTeacher)
            ->post(
                route('teacher.teams.members.add', [
                    $activity->id,
                    $team->id,
                ]),
                ['student_id' => $student->id]
            );

        $response->assertForbidden();

        $this->assertDatabaseMissing('team_members', [
            'team_id' => $team->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_owner_teacher_can_remove_team_member(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createTeamActivity($class, $teacher);
        $team = $this->createTeam($activity);

        $member = TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $student->id,
        ]);

        $response = $this->actingAs($teacher)
            ->delete(
                route('teacher.teams.members.remove', [
                    $activity->id,
                    $team->id,
                    $student->id,
                ])
            );

        $response->assertRedirect();

        $this->assertDatabaseMissing('team_members', [
            'id' => $member->id,
        ]);
    }

    public function test_owner_teacher_can_delete_team(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createTeamActivity($class, $teacher);
        $team = $this->createTeam($activity);

        $response = $this->actingAs($teacher)
            ->delete(
                route('teacher.teams.destroy', [
                    $activity->id,
                    $team->id,
                ])
            );

        $response->assertRedirect();

        $this->assertDatabaseMissing('teams', [
            'id' => $team->id,
        ]);
    }

    public function test_other_teacher_cannot_delete_team(): void
    {
        $owner = $this->teacher('owner@example.com');
        $otherTeacher = $this->teacher('other@example.com');

        $class = $this->createClass($owner);
        $activity = $this->createTeamActivity($class, $owner);
        $team = $this->createTeam($activity);

        $response = $this->actingAs($otherTeacher)
            ->delete(
                route('teacher.teams.destroy', [
                    $activity->id,
                    $team->id,
                ])
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
        ]);
    }

    public function test_team_cannot_be_modified_after_participation_exists(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createTeamActivity($class, $teacher);
        $team = $this->createTeam($activity);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.teams.store', $activity->id), [
                'name' => 'Late Team',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('name');

        $this->assertDatabaseMissing('teams', [
            'activity_id' => $activity->id,
            'name' => 'Late Team',
        ]);
    }

    public function test_randomize_creates_balanced_teams(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createTeamActivity($class, $teacher);

        $students = collect([
            'student1@example.com',
            'student2@example.com',
            'student3@example.com',
            'student4@example.com',
            'student5@example.com',
        ])->map(fn(string $email) => $this->student($email));

        $students->each(
            fn(User $student) => $this->enroll($class, $student)
        );

        $response = $this->actingAs($teacher)
            ->post(route('teacher.teams.randomize', $activity->id), [
                'team_count' => 2,
            ]);

        $response->assertRedirect();

        $teams = Team::where('activity_id', $activity->id)
            ->withCount('members')
            ->get();

        $this->assertCount(2, $teams);

        $sizes = $teams
            ->pluck('members_count')
            ->sort()
            ->values()
            ->all();

        $this->assertSame([2, 3], $sizes);

        $this->assertSame(
            5,
            TeamMember::whereIn('team_id', $teams->pluck('id'))
                ->count()
        );
    }

    public function test_randomize_rejects_more_teams_than_students(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createTeamActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.teams.randomize', $activity->id), [
                'team_count' => 2,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('team_count');

        $this->assertSame(
            0,
            Team::where('activity_id', $activity->id)->count()
        );
    }

    public function test_randomize_cannot_be_used_after_participation_exists(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createTeamActivity($class, $teacher);
        $team = $this->createTeam($activity);

        Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.teams.randomize', $activity->id), [
                'team_count' => 1,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('team_count');

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
        ]);
    }

    public function test_random_student_returns_an_active_class_student(): void
    {
        $teacher = $this->teacher();
        $student = $this->student('student@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createTeamActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.teams.random-student', $activity->id));

        $response->assertRedirect();
        $response->assertSessionHas('random_student');

        $randomStudent = session('random_student');

        $this->assertSame($student->id, $randomStudent['id']);
    }

    public function test_random_team_returns_a_team_from_the_activity(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createTeamActivity($class, $teacher);

        $student = $this->student('student@example.com');
        $this->enroll($class, $student);

        $team = $this->createTeam($activity);

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $student->id,
        ]);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.teams.random-team', $activity->id));

        $response->assertRedirect();

        $response->assertSessionHas('random_team', function ($randomTeam) use ($team) {
            return $randomTeam['id'] === $team->id
                && $randomTeam['name'] === $team->name;
        });
    }

    public function test_random_team_rejects_non_team_activity(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createTeamActivity($class, $teacher, [
            'mode' => 'individual',
        ]);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.teams.random-team', $activity->id));

        $response->assertRedirect();
        $response->assertSessionHasErrors('random');

        $this->assertFalse(
            session()->has('random_team')
        );
    }
}
