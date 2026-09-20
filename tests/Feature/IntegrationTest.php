<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\Kahoot;
use App\Models\Question;
use App\Models\Option;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function teacher(string $email = 'teacher@example.com'): User
    {
        return User::create([
            'name' => 'Teacher',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'status' => 'active',
        ]);
    }

    private function student(string $email = 'student@example.com'): User
    {
        return User::create([
            'name' => 'Student',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'student',
            'status' => 'active',
        ]);
    }

    private function createClass(User $teacher): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Integration Class',
            'description' => 'Integration test class',
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
            'title' => 'Integration Activity',
            'description' => 'Integration test activity',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 3,
            'due_at' => null,
            'status' => 'published',
        ], $attributes));
    }

    private function createKahoot(Activity $activity): void
    {
        $kahoot = Kahoot::create([
            'activity_id' => $activity->id,
        ]);

        $question = Question::create([
            'kahoot_id' => $kahoot->id,
            'question' => 'What is 2 + 2?',
            'position' => 1,
            'time_limit' => 30,
            'score' => 100,
        ]);

        Option::create([
            'question_id' => $question->id,
            'text' => '3',
            'is_correct' => false,
            'position' => 1,
        ]);

        Option::create([
            'question_id' => $question->id,
            'text' => '4',
            'is_correct' => true,
            'position' => 2,
        ]);
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

    // -------------------------------------------------------------------------
    // INDIVIDUAL FLOW
    // Activity -> Participation -> Play -> Finish -> Result
    // -------------------------------------------------------------------------

    public function test_student_can_complete_an_individual_activity_flow(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'type' => 'kahoot',
            'mode' => 'individual',
            'attempts' => 2,
        ]);

        $this->createKahoot($activity);

        // 1. Iniciar actividad
        $response = $this->actingAs($student)->post(
            route('student.participation.start', $activity->id)
        );

        $response->assertRedirect(
            route('student.kahoot.play', $activity->id)
        );

        $this->assertDatabaseHas('participations', [
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        $participation = Participation::where('activity_id', $activity->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        // 2. La pantalla del juego debe resolver la participación
        $response = $this->actingAs($student)->get(
            route('student.kahoot.play', $activity->id)
        );

        $response->assertStatus(200);

        // 3. Finalizar actividad
        $response = $this->actingAs($student)->post(
            route('student.participation.finish', $activity->id)
        );

        $response->assertRedirect(
            route('student.participation.result', $activity->id)
        );

        $participation->refresh();

        $this->assertSame('completed', $participation->status);
        $this->assertNotNull($participation->completed_at);
        $this->assertNotNull($participation->elapsed_seconds);

        // 4. Consultar resultado
        $response = $this->actingAs($student)->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(200);
        $response->assertViewIs('student.participation.result');

        $response->assertViewHas('activity', function ($result) use ($activity) {
            return $result->id === $activity->id;
        });

        $response->assertViewHas('participation', function ($result) use ($participation) {
            return $result->id === $participation->id
                && $result->status === 'completed';
        });
    }

    // -------------------------------------------------------------------------
    // INDIVIDUAL -> TEACHER RESULTS
    // Student completes -> teacher sees participation
    // -------------------------------------------------------------------------

    public function test_completed_individual_participation_appears_in_teacher_results(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $this->actingAs($student)->post(
            route('student.participation.start', $activity->id)
        );

        $this->actingAs($student)->post(
            route('student.participation.finish', $activity->id)
        );

        $participation = Participation::where('activity_id', $activity->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.results', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('results', function ($results) use (
            $student,
            $participation
        ) {
            $result = $results->firstWhere(
                'student.id',
                $student->id
            );

            return $result !== null
                && $result['participation']->id === $participation->id
                && $result['status'] === 'completed';
        });
    }

    // -------------------------------------------------------------------------
    // INDIVIDUAL -> RANKING
    // Completed participation -> ranking
    // -------------------------------------------------------------------------

    public function test_completed_individual_participation_appears_in_ranking(): void
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
            'status' => 'completed',
            'score' => 90,
            'started_at' => now()->subSeconds(60),
            'completed_at' => now(),
            'elapsed_seconds' => 60,
        ]);

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use (
            $student,
            $participation
        ) {
            return $ranking->count() === 1
                && $ranking[0]['student']->id === $student->id
                && $ranking[0]['participation']->id === $participation->id
                && $ranking[0]['score'] === 90;
        });
    }

    // -------------------------------------------------------------------------
    // TEAM FLOW
    // Team -> Participation -> Finish -> Team Result
    // -------------------------------------------------------------------------

    public function test_team_can_complete_an_activity_flow(): void
    {
        $teacher = $this->teacher();

        $studentOne = $this->student('one@example.com');
        $studentTwo = $this->student('two@example.com');

        $class = $this->createClass($teacher);

        $this->enroll($class, $studentOne);
        $this->enroll($class, $studentTwo);

        $activity = $this->createActivity($class, $teacher, [
            'type' => 'kahoot',
            'mode' => 'team',
            'attempts' => 2,
        ]);

        $this->createKahoot($activity);

        $team = $this->createTeam($activity);

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $studentOne->id,
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'student_id' => $studentTwo->id,
        ]);

        // Student 1 inicia la actividad
        $response = $this->actingAs($studentOne)->post(
            route('student.participation.start', $activity->id)
        );

        $response->assertRedirect(
            route('student.kahoot.play', $activity->id)
        );

        $this->assertDatabaseHas('participations', [
            'activity_id' => $activity->id,
            'student_id' => null,
            'team_id' => $team->id,
            'attempt' => 1,
            'status' => 'started',
        ]);

        // La Participation pertenece al equipo, no a un estudiante
        $participation = Participation::where('activity_id', $activity->id)
            ->where('team_id', $team->id)
            ->firstOrFail();

        // Student 2 también debe poder resolver la misma participación
        $response = $this->actingAs($studentTwo)->get(
            route('student.kahoot.play', $activity->id)
        );

        $response->assertStatus(200);

        // Student 2 finaliza en nombre del equipo
        $response = $this->actingAs($studentTwo)->post(
            route('student.participation.finish', $activity->id)
        );

        $response->assertRedirect(
            route('student.participation.result', $activity->id)
        );

        $participation->refresh();

        $this->assertSame('completed', $participation->status);
        $this->assertNull($participation->student_id);
        $this->assertSame($team->id, $participation->team_id);

        // Cualquier miembro del equipo puede ver el resultado
        $response = $this->actingAs($studentOne)->get(
            route('student.participation.result', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('participation', function ($result) use ($participation) {
            return $result->id === $participation->id
                && $result->team_id !== null
                && $result->student_id === null;
        });
    }

    // -------------------------------------------------------------------------
    // TEAM -> TEACHER RESULTS
    // -------------------------------------------------------------------------

    public function test_team_participation_appears_for_all_team_members_in_teacher_results(): void
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

        $team = $this->createTeam($activity);

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
            'score' => 85,
            'started_at' => now()->subSeconds(90),
            'completed_at' => now(),
            'elapsed_seconds' => 90,
        ]);

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.results', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('results', function ($results) use (
            $studentOne,
            $studentTwo,
            $participation
        ) {
            $one = $results->firstWhere(
                'student.id',
                $studentOne->id
            );

            $two = $results->firstWhere(
                'student.id',
                $studentTwo->id
            );

            return $one !== null
                && $two !== null
                && $one['participation']->id === $participation->id
                && $two['participation']->id === $participation->id
                && $one['score'] === 85
                && $two['score'] === 85;
        });
    }

    // -------------------------------------------------------------------------
    // TEAM -> RANKING
    // -------------------------------------------------------------------------

    public function test_completed_team_participation_appears_in_team_ranking(): void
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

        $team = $this->createTeam($activity);

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
            'score' => 95,
            'started_at' => now()->subSeconds(70),
            'completed_at' => now(),
            'elapsed_seconds' => 70,
        ]);

        $response = $this->actingAs($teacher)->get(
            route('teacher.activities.ranking', $activity->id)
        );

        $response->assertStatus(200);

        $response->assertViewHas('ranking', function ($ranking) use (
            $team,
            $participation
        ) {
            return $ranking->count() === 1
                && $ranking[0]['team']->id === $team->id
                && $ranking[0]['participation']->id === $participation->id
                && $ranking[0]['score'] === 95;
        });
    }

    // -------------------------------------------------------------------------
    // CROSS-MODULE ISOLATION
    // Activity A must not leak into Activity B
    // -------------------------------------------------------------------------

    public function test_participation_from_another_activity_never_appears_in_result(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activityA = $this->createActivity($class, $teacher, [
            'title' => 'Activity A',
        ]);

        $activityB = $this->createActivity($class, $teacher, [
            'title' => 'Activity B',
        ]);

        $this->actingAs($student)->post(
            route('student.participation.start', $activityA->id)
        );

        $this->actingAs($student)->post(
            route('student.participation.finish', $activityA->id)
        );

        $response = $this->actingAs($student)->get(
            route('student.participation.result', $activityB->id)
        );

        $response->assertStatus(404);
    }

    // -------------------------------------------------------------------------
    // CROSS-ROLE INTEGRATION
    // Student flow must not expose teacher-only endpoints
    // -------------------------------------------------------------------------

    public function test_student_cannot_jump_from_student_flow_to_teacher_results(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $response = $this->actingAs($student)->get(
            route('teacher.activities.results', $activity->id)
        );

        $response->assertRedirect();
    }

    public function test_student_can_answer_kahoot_and_it_completes_automatically(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);

        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'type' => 'kahoot',
            'mode' => 'individual',
            'attempts' => 1,
            'max_score' => 100,
        ]);

        $this->createKahoot($activity);

        /*
     * Iniciar participación.
     */
        $response = $this->actingAs($student)->post(
            route('student.participation.start', $activity->id)
        );

        $response->assertRedirect();

        $participation = Participation::query()
            ->where('activity_id', $activity->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $question = Question::query()
            ->whereHas('kahoot', function ($query) use ($activity) {
                $query->where('activity_id', $activity->id);
            })
            ->firstOrFail();

        $correctOption = Option::query()
            ->where('question_id', $question->id)
            ->where('is_correct', true)
            ->firstOrFail();

        /*
     * Entrar al juego.
     */
        $response = $this->actingAs($student)->get(
            route('student.kahoot.play', $activity->id)
        );

        $response->assertStatus(200);

        /*
     * Responder la pregunta correctamente.
     */
        $response = $this->actingAs($student)->postJson(
            route('student.kahoot.answer'),
            [
                'participation_id' => $participation->id,
                'question_id' => $question->id,
                'option_id' => $correctOption->id,
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'is_correct' => true,
                'completed' => true,
            ]);

        /*
     * La respuesta debe existir.
     */
        $this->assertDatabaseHas('kahoot_answers', [
            'participation_id' => $participation->id,
            'question_id' => $question->id,
            'option_id' => $correctOption->id,
        ]);

        /*
     * La participación debe haberse
     * completado automáticamente.
     */
        $participation->refresh();

        $this->assertSame('completed', $participation->status);

        $this->assertNotNull($participation->completed_at);

        /*
     * El score debe haberse sincronizado.
     */
        $this->assertGreaterThan(0, $participation->score);
    }
}
