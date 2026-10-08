<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\GameSession;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\GameSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameSessionTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

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
            'description' => 'Multiplayer tests',
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
            'title' => 'Multiplayer Activity',
            'description' => 'Test activity',
            'type' => 'roulette',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
            'due_at' => null,
            'status' => 'published',
        ], $attributes));
    }

    private function setupRoom()
    {
        $teacher = $this->teacher();
        $s1 = $this->student('student1@example.com');
        $s2 = $this->student('student2@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $s1);
        $this->enroll($class, $s2);

        $activity = $this->createActivity($class, $teacher);

        $this->actingAs($s1)
            ->post(route('student.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 2,
            ]);

        $session = GameSession::where('activity_id', $activity->id)->first();

        return compact('teacher', 's1', 's2', 'class', 'activity', 'session');
    }

    // -------------------------------------------------------------------------
    // Creación
    // -------------------------------------------------------------------------

    public function test_student_can_create_a_room_and_becomes_host(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);
        $this->enroll($class, $student);
        $activity = $this->createActivity($class, $teacher);

        $response = $this->actingAs($student)
            ->post(route('student.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 4,
            ]);

        $session = GameSession::where('activity_id', $activity->id)->first();

        $response->assertRedirect(route('student.game-sessions.show', $session->id));

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'activity_id' => $activity->id,
            'status' => 'waiting',
            'max_players' => 4,
            'created_by' => $student->id,
        ]);

        $this->assertMatchesRegularExpression('/^KLS\d{3}$/', $session->code);

        $this->assertDatabaseHas('participations', [
            'game_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'waiting',
        ]);
    }

    public function test_generated_codes_are_unique(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);
        $this->enroll($class, $student);
        $activity = $this->createActivity($class, $teacher);

        $service = app(GameSessionService::class);

        $a = $service->create($activity, 4, $student->id);
        $b = $service->create($activity, 4, $student->id);

        $this->assertNotSame($a->code, $b->code);
    }

    public function test_non_student_cannot_create_room(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher);

        $this->actingAs($teacher)
            ->post(route('student.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 4,
            ])
            ->assertRedirect();
    }

    // -------------------------------------------------------------------------
    // Unirse
    // -------------------------------------------------------------------------

    public function test_student_can_join_by_code(): void
    {
        ['s2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ])
            ->assertRedirect(route('student.game-sessions.show', $session->id));

        $this->assertDatabaseHas('participations', [
            'game_session_id' => $session->id,
            'student_id' => $s2->id,
            'status' => 'waiting',
        ]);
    }

    public function test_duplicate_join_is_rejected(): void
    {
        ['s2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ])
            ->assertSessionHas('error');
    }

    public function test_full_room_rejects_new_player(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $s3 = $this->student('student3@example.com');
        $this->enroll($session->activity->schoolClass, $s3);

        $this->actingAs($s3)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ])
            ->assertSessionHas('error', 'La sala alcanzó su máximo de jugadores.');
    }

    public function test_invalid_code_is_rejected(): void
    {
        $this->actingAs($this->student('joiner@example.com'))
            ->post(route('student.game-sessions.join'), [
                'code' => 'KLS000',
            ])
            ->assertSessionHas('error', 'No existe una sala con ese código.');
    }

    // -------------------------------------------------------------------------
    // Inicio
    // -------------------------------------------------------------------------

    public function test_cannot_start_with_less_than_two_players(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);
        $this->enroll($class, $student);
        $activity = $this->createActivity($class, $teacher);

        $this->actingAs($student)
            ->post(route('student.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 4,
            ]);

        $session = GameSession::where('activity_id', $activity->id)->first();

        $this->actingAs($student)
            ->post(route('student.game-sessions.start', $session->id))
            ->assertSessionHas('error', 'Se necesitan al menos 2 jugadores para iniciar.');

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'status' => 'waiting',
        ]);
    }

    public function test_host_starts_game_with_two_players(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($s1)
            ->post(route('student.game-sessions.start', $session->id))
            ->assertRedirect(route('student.game-sessions.play', $session->id));

        $session->refresh();

        $this->assertSame('playing', $session->status);
        $this->assertNotNull($session->started_at);
        $this->assertNotNull($session->current_turn_participation_id);

        $this->assertSame(
            2,
            Participation::where('game_session_id', $session->id)
                ->where('status', 'started')
                ->count()
        );
    }

    public function test_non_host_cannot_start(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($s2)
            ->post(route('student.game-sessions.start', $session->id))
            ->assertForbidden();
    }

    public function test_join_is_required_to_start(): void
    {
        $teacher = $this->teacher();
        $s1 = $this->student('student1@example.com');
        $s2 = $this->student('student2@example.com');
        $class = $this->createClass($teacher);
        $this->enroll($class, $s1);
        $this->enroll($class, $s2);
        $activity = $this->createActivity($class, $teacher);

        $this->actingAs($s1)
            ->post(route('student.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 4,
            ]);

        $session = GameSession::where('activity_id', $activity->id)->first();

        // host abandona antes de que entre el segundo jugador
        $this->actingAs($s1)
            ->post(route('student.game-sessions.leave', $session->id));

        $session->refresh();

        $this->assertSame('cancelled', $session->status);
    }

    // -------------------------------------------------------------------------
    // Acceso / estado
    // -------------------------------------------------------------------------

    public function test_non_participant_cannot_view_room(): void
    {
        ['session' => $session] = $this->setupRoom();

        $outsider = $this->student('outsider@example.com');

        $this->actingAs($outsider)
            ->get(route('student.game-sessions.show', $session->id))
            ->assertForbidden();
    }

    public function test_state_endpoint_returns_shared_state(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $response = $this->actingAs($s1)
            ->get(route('student.game-sessions.state', $session->id))
            ->assertOk();

        $response->assertJsonPath('session_id', $session->id);
        $response->assertJsonPath('status', 'waiting');
        $response->assertJsonPath('activity.id', $session->activity_id);
        $response->assertJsonCount(2, 'players');
    }

    public function test_leave_marks_participation_abandoned(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($s2)
            ->post(route('student.game-sessions.leave', $session->id))
            ->assertRedirect(route('student.activities.show', $session->activity_id));

        $this->assertDatabaseHas('participations', [
            'game_session_id' => $session->id,
            'student_id' => $s2->id,
            'status' => 'abandoned',
        ]);
    }

    public function test_cannot_join_playing_session(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($s1)
            ->post(route('student.game-sessions.start', $session->id));

        $s3 = $this->student('student3@example.com');
        $this->enroll($session->activity->schoolClass, $s3);

        $this->actingAs($s3)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ])
            ->assertSessionHas('error', 'Esta sala ya no acepta jugadores.');
    }

    public function test_game_session_model_belongs_to_activity(): void
    {
        ['activity' => $activity, 'session' => $session] = $this->setupRoom();

        $this->assertInstanceOf(Activity::class, $session->activity);
        $this->assertSame($activity->id, $session->activity->id);
        $this->assertSame($session->id, $activity->gameSessions()->first()->id);
        $this->assertTrue($activity->gameSessions->contains($session->id));
    }
}
