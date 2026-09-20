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

class ParticipationTest extends TestCase
{
    use RefreshDatabase;

    private function teacher(): User
    {
        return User::create([
            'name' => 'Teacher',
            'email' => 'teacher@example.com',
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
            'description' => 'Participation tests',
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
            'title' => 'Test Activity',
            'description' => 'Participation test activity',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
            'due_at' => null,
            'status' => 'published',
        ], $attributes));
    }

    /*
    |--------------------------------------------------------------------------
    | START
    |--------------------------------------------------------------------------
    */

    public function test_enrolled_student_can_start_published_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.start', $activity->id)
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('participations', [
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);
    }

    public function test_student_cannot_start_draft_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'status' => 'draft',
        ]);

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.start', $activity->id)
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('participations', [
            'activity_id' => $activity->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_student_cannot_start_closed_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'status' => 'closed',
        ]);

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.start', $activity->id)
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('participations', [
            'activity_id' => $activity->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_student_cannot_start_activity_after_due_date(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'due_at' => Carbon::now()->subMinute(),
        ]);

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.start', $activity->id)
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('participations', [
            'activity_id' => $activity->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_unenrolled_student_cannot_start_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher);

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.start', $activity->id
        ));

        $response->assertRedirect();

        $response->assertSessionHas(
            'error',
            'No estás inscrito en la clase de esta actividad.'
        );

        $this->assertDatabaseMissing('participations', [
            'activity_id' => $activity->id,
            'student_id' => $student->id,
        ]);
    }

    public function test_student_cannot_start_second_active_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $this->actingAs($student);

        $this->post(
            route('student.participation.start', $activity->id)
        )->assertRedirect();

        $response = $this->post(
            route('student.participation.start', $activity->id)
        );

        $response->assertRedirect();

        $response->assertSessionHas(
            'error',
            'Ya tienes un intento activo en esta actividad.'
        );

        $this->assertSame(
            1,
            Participation::where('activity_id', $activity->id)
                ->where('student_id', $student->id)
                ->where('status', 'started')
                ->count()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FINISH
    |--------------------------------------------------------------------------
    */

    public function test_student_can_finish_own_active_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.finish', $activity->id)
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'completed',
        ]);
    }

    public function test_student_cannot_finish_another_students_participation(): void
    {
        $teacher = $this->teacher();
        $owner = $this->student('owner@example.com');
        $attacker = $this->student('attacker@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $owner);
        $this->enroll($class, $attacker);

        $activity = $this->createActivity($class, $teacher);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $owner->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $this->actingAs($attacker);

        $response = $this->post(
            route('student.participation.finish', $activity->id)
        );

        $response->assertRedirect();

        $response->assertSessionHas(
            'error',
            'No tienes una participación activa en esta actividad.'
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'started',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ABANDON
    |--------------------------------------------------------------------------
    */

    public function test_student_can_abandon_own_active_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.abandon', $activity->id)
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'abandoned',
        ]);
    }

    public function test_student_cannot_abandon_another_students_participation(): void
    {
        $teacher = $this->teacher();
        $owner = $this->student('owner@example.com');
        $attacker = $this->student('attacker@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $owner);
        $this->enroll($class, $attacker);

        $activity = $this->createActivity($class, $teacher);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $owner->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $this->actingAs($attacker);

        $response = $this->post(
            route('student.participation.abandon', $activity->id)
        );

        $response->assertRedirect();

        $response->assertSessionHas(
            'error',
            'No tienes una participación activa en esta actividad.'
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'started',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TEAM MODE
    |--------------------------------------------------------------------------
    */

    public function test_team_member_can_start_team_participation(): void
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

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.start', $activity->id)
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('participations', [
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'started',
        ]);
    }

    public function test_student_without_team_cannot_start_team_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
        ]);

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.start', $activity->id)
        );

        $response->assertRedirect();

        $response->assertSessionHas(
            'error',
            'No perteneces a ningún equipo en esta actividad.'
        );

        $this->assertDatabaseMissing('participations', [
            'activity_id' => $activity->id,
            'team_id' => null,
            'status' => 'started',
        ]);
    }

    public function test_team_member_can_finish_team_participation(): void
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

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.finish', $activity->id)
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'completed',
        ]);
    }

    public function test_non_member_cannot_finish_team_participation(): void
    {
        $teacher = $this->teacher();
        $member = $this->student('member@example.com');
        $outsider = $this->student('outsider@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $member);
        $this->enroll($class, $outsider);

        $activity = $this->createActivity($class, $teacher, [
            'mode' => 'team',
        ]);

        $team = Team::create([
            'activity_id' => $activity->id,
            'name' => 'Team Alpha',
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $member->id,
        ]);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $this->actingAs($outsider);

        $response = $this->post(
            route('student.participation.finish', $activity->id)
        );

        $response->assertRedirect();

        $response->assertSessionHas(
            'error',
            'No tienes participación activa en esta actividad.'
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'started',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPIRE
    |--------------------------------------------------------------------------
    */

    public function test_expire_route_cannot_expire_participation_before_time_limit(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'time_limit' => 300,
        ]);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.expire', $activity->id)
        );

        $response->assertStatus(409);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'started',
        ]);
    }

    public function test_expired_participation_is_expired_automatically(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'time_limit' => 60,
        ]);

        $participation = Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $participation->started_at = Carbon::now()->subSeconds(120);
        $participation->save();

        $this->actingAs($student);

        $response = $this->post(
            route('student.participation.expire', $activity->id)
        );

        /*
         * getActive() detecta que la participación ya expiró,
         * la marca como expired y lanza una excepción que el
         * controlador convierte en una redirección al resultado.
         */
        $response->assertRedirect();

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'expired',
        ]);
    }
}