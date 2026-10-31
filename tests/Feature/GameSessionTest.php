<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\GameSession;
use App\Models\GameSessionPresence;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\Team;
use App\Models\TeamMember;
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

    private function createRoom(User $teacher, Activity $activity, int $maxPlayers = 2): GameSession
    {
        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => $maxPlayers,
            ]);

        return GameSession::where('activity_id', $activity->id)->first();
    }

    private function createSessionRecord(
        User $teacher,
        Activity $activity,
        string $code,
        string $status
    ): GameSession {
        return GameSession::create([
            'activity_id' => $activity->id,
            'code' => $code,
            'status' => $status,
            'max_players' => 4,
            'created_by' => $teacher->id,
            'state' => [],
        ]);
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
        $session = $this->createRoom($teacher, $activity);

        return compact('teacher', 's1', 's2', 'class', 'activity', 'session');
    }

    public function test_teacher_dashboard_lists_only_the_teachers_active_game_sessions(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $waitingActivity = $this->createActivity($class, $teacher, ['title' => 'Sala en espera']);
        $playingActivity = $this->createActivity($class, $teacher, [
            'title' => 'Sala en juego',
            'type' => 'crossword',
        ]);
        $finishedActivity = $this->createActivity($class, $teacher, [
            'title' => 'Sala terminada',
            'type' => 'matching',
        ]);
        $cancelledActivity = $this->createActivity($class, $teacher, [
            'title' => 'Sala cancelada',
            'type' => 'kahoot',
        ]);

        $waitingSession = $this->createSessionRecord($teacher, $waitingActivity, 'KLS101', 'waiting');
        $playingSession = $this->createSessionRecord($teacher, $playingActivity, 'KLS102', 'playing');
        $this->createSessionRecord($teacher, $finishedActivity, 'KLS103', 'finished');
        $this->createSessionRecord($teacher, $cancelledActivity, 'KLS104', 'cancelled');

        $otherTeacher = $this->teacher('other-teacher@example.com');
        $otherClass = $this->createClass($otherTeacher);
        $otherActivity = $this->createActivity($otherClass, $otherTeacher, ['title' => 'Sala ajena']);
        $this->createSessionRecord($otherTeacher, $otherActivity, 'KLS105', 'waiting');

        $response = $this->actingAs($teacher)->get(route('teacher.dashboard'));

        $response->assertOk()
            ->assertViewIs('teacher.dashboard')
            ->assertViewHas('activeGameSessions', function ($sessions) use ($waitingSession, $playingSession): bool {
                return $sessions->count() === 2
                    && $sessions->contains('id', $waitingSession->id)
                    && $sessions->contains('id', $playingSession->id);
            })
            ->assertSee('Sala en espera')
            ->assertSee('Sala en juego')
            ->assertSee(route('teacher.game-sessions.show', $waitingSession->id), false)
            ->assertSee(route('teacher.game-sessions.show', $playingSession->id), false)
            ->assertDontSee('Sala terminada')
            ->assertDontSee('Sala cancelada')
            ->assertDontSee('Sala ajena');
    }

    public function test_teacher_can_close_a_waiting_room_that_no_student_joined(): void
    {
        ['teacher' => $teacher, 'activity' => $activity, 'session' => $session] = $this->setupRoom();

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.show', $session->id))
            ->assertOk()
            ->assertSee('Cerrar sala sin iniciar');

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.cancel', $session->id))
            ->assertRedirect(route('teacher.dashboard'))
            ->assertSessionHas('success', 'La sala se cerró y ya no aparece entre las partidas activas.');

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'status' => GameSession::STATUS_CANCELLED,
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertDontSee($activity->title);
    }

    public function test_teacher_cannot_close_a_waiting_room_after_a_student_joined(): void
    {
        ['teacher' => $teacher, 's1' => $student, 'activity' => $activity, 'session' => $session] = $this->setupRoom();

        $this->actingAs($student)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id));

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.cancel', $session->id))
            ->assertSessionHas('error', 'No se puede cerrar la sala porque ya tuvo participantes.');

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'status' => GameSession::STATUS_WAITING,
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.show', $session->id))
            ->assertOk()
            ->assertDontSee('Cerrar sala sin iniciar')
            ->assertSee($activity->title);
    }

    public function test_only_owner_teacher_can_close_a_waiting_room(): void
    {
        ['teacher' => $teacher, 'activity' => $activity, 'session' => $session] = $this->setupRoom();
        $otherTeacher = $this->teacher('other-teacher@example.com');

        $this->actingAs($otherTeacher)
            ->post(route('teacher.game-sessions.cancel', $session->id))
            ->assertForbidden();

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'activity_id' => $activity->id,
            'created_by' => $teacher->id,
            'status' => GameSession::STATUS_WAITING,
        ]);
    }

    // -------------------------------------------------------------------------
    // Creación
    // -------------------------------------------------------------------------

    public function test_teacher_can_create_room_without_joining(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 4,
            ]);

        $session = GameSession::where('activity_id', $activity->id)->first();

        $response->assertRedirect(route('teacher.game-sessions.show', $session->id));

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'activity_id' => $activity->id,
            'status' => 'waiting',
            'max_players' => 4,
            'created_by' => $teacher->id,
        ]);

        $this->assertMatchesRegularExpression('/^KLS\d{3}$/', $session->code);

        $this->assertDatabaseMissing('participations', [
            'game_session_id' => $session->id,
            'student_id' => $teacher->id,
        ]);
    }

    public function test_teacher_cannot_create_room_for_activity_of_another_teacher(): void
    {
        $owner = $this->teacher('owner@example.com');
        $other = $this->teacher('other@example.com');
        $class = $this->createClass($owner);
        $activity = $this->createActivity($class, $owner);

        $this->actingAs($other)
            ->post(route('teacher.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 4,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('game_sessions', [
            'activity_id' => $activity->id,
        ]);
    }

    public function test_student_cannot_create_room(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher);

        $this->actingAs($student)
            ->post(route('teacher.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 4,
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('game_sessions', [
            'activity_id' => $activity->id,
        ]);
    }

    public function test_generated_codes_are_unique(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher);

        $service = app(GameSessionService::class);

        $a = $service->create($activity, 4, $teacher->id);
        $b = $service->create($activity, 4, $teacher->id);

        $this->assertNotSame($a->code, $b->code);
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

    public function test_duplicate_join_redirects_to_the_room(): void
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
            ->assertRedirect(route('student.game-sessions.show', $session->id))
            ->assertSessionHas('success', 'Ya estás en esta sala.')
            ->assertSessionMissing('error');

        $this->assertSame(1, $session->participations()
            ->where('student_id', $s2->id)
            ->whereIn('status', ['waiting', 'started'])
            ->count());
    }

    public function test_full_room_rejects_new_player(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

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

    public function test_team_members_share_one_room_seat_and_one_participation(): void
    {
        $teacher = $this->teacher();
        $firstMember = $this->student('team-member-1@example.com');
        $secondMember = $this->student('team-member-2@example.com');
        $opponent = $this->student('opponent@example.com');
        $overflow = $this->student('overflow@example.com');
        $class = $this->createClass($teacher);

        foreach ([$firstMember, $secondMember, $opponent, $overflow] as $student) {
            $this->enroll($class, $student);
        }

        $activity = $this->createActivity($class, $teacher, ['mode' => 'team']);
        $firstTeam = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Uno']);
        $opponentTeam = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Dos']);
        $overflowTeam = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Tres']);
        TeamMember::create(['team_id' => $firstTeam->id, 'student_id' => $firstMember->id]);
        TeamMember::create(['team_id' => $firstTeam->id, 'student_id' => $secondMember->id]);
        TeamMember::create(['team_id' => $opponentTeam->id, 'student_id' => $opponent->id]);
        TeamMember::create(['team_id' => $overflowTeam->id, 'student_id' => $overflow->id]);
        $session = $this->createRoom($teacher, $activity, 2);

        foreach ([$firstMember, $secondMember, $opponent] as $student) {
            $this->actingAs($student)
                ->post(route('student.game-sessions.join'), ['code' => $session->code])
                ->assertRedirect(route('student.game-sessions.show', $session->id));
        }

        $this->assertSame(2, $session->participations()->whereIn('status', ['waiting', 'started'])->count());
        $this->assertSame(1, $session->participations()->where('team_id', $firstTeam->id)->count());
        $this->assertSame(2, GameSessionPresence::query()
            ->where('game_session_id', $session->id)
            ->where('participation_id', $session->participations()->where('team_id', $firstTeam->id)->value('id'))
            ->where('status', GameSessionPresence::STATUS_ACTIVE)
            ->count());

        $this->actingAs($overflow)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertSessionHas('error', 'La sala alcanzó su máximo de jugadores.');

        $this->actingAs($secondMember)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id))
            ->assertSessionHas('success', 'Ya estás en esta sala.')
            ->assertSessionMissing('error');
    }

    public function test_teammate_can_join_a_started_team_participation_and_rejoin_after_disconnect(): void
    {
        $teacher = $this->teacher();
        $firstMember = $this->student('started-team-member-1@example.com');
        $secondMember = $this->student('started-team-member-2@example.com');
        $opponent = $this->student('started-opponent@example.com');
        $class = $this->createClass($teacher);

        foreach ([$firstMember, $secondMember, $opponent] as $student) {
            $this->enroll($class, $student);
        }

        $activity = $this->createActivity($class, $teacher, ['mode' => 'team']);
        $firstTeam = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Uno']);
        $opponentTeam = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Dos']);
        TeamMember::create(['team_id' => $firstTeam->id, 'student_id' => $firstMember->id]);
        TeamMember::create(['team_id' => $firstTeam->id, 'student_id' => $secondMember->id]);
        TeamMember::create(['team_id' => $opponentTeam->id, 'student_id' => $opponent->id]);
        $session = $this->createRoom($teacher, $activity, 2);

        foreach ([$firstMember, $opponent] as $student) {
            $this->actingAs($student)
                ->post(route('student.game-sessions.join'), ['code' => $session->code]);
        }

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id))
            ->assertRedirect();

        $teamParticipation = $session->participations()->where('team_id', $firstTeam->id)->firstOrFail();
        $this->actingAs($secondMember)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id));

        $presence = GameSessionPresence::query()
            ->where('game_session_id', $session->id)
            ->where('student_id', $secondMember->id)
            ->firstOrFail();

        $this->assertSame(1, $session->participations()->where('team_id', $firstTeam->id)->count());
        $this->assertSame($teamParticipation->id, $presence->participation_id);

        $this->actingAs($firstMember)
            ->post(route('student.game-sessions.leave', $session->id))
            ->assertRedirect(route('student.activities.show', $activity->id));

        $this->assertSame('started', $teamParticipation->fresh()->status);
        $this->assertSame(GameSession::STATUS_PLAYING, $session->fresh()->status);

        $this->actingAs($firstMember)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id));

        $this->assertSame(1, $session->participations()->where('team_id', $firstTeam->id)->count());
        $this->assertSame(
            GameSessionPresence::STATUS_ACTIVE,
            $presence->fresh()->status
        );

        $this->actingAs($firstMember)
            ->post(route('student.game-sessions.leave', $session->id));
        $this->actingAs($secondMember)
            ->post(route('student.game-sessions.leave', $session->id));

        $this->assertSame(GameSession::STATUS_FINISHED, $session->fresh()->status);
        $this->assertSame('abandoned', $teamParticipation->fresh()->status);
    }

    public function test_individual_player_who_closes_the_window_rejoins_by_code_without_wait(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), ['code' => $session->code]);

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), ['code' => $session->code]);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id));

        // La participación de s1 sigue viva (started con fecha reciente),
        // igual que cuando cierra la pestaña y regresa de inmediato.
        $participation = Participation::where('game_session_id', $session->id)
            ->where('student_id', $s1->id)
            ->firstOrFail();
        $this->assertSame('started', $participation->status);

        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id))
            ->assertSessionHas('success', 'Ya estás en esta sala.')
            ->assertSessionMissing('error');

        $this->assertSame('started', $participation->fresh()->status);
        $this->assertSame(GameSession::STATUS_PLAYING, $session->fresh()->status);
    }

    public function test_team_member_who_closes_the_window_rejoins_by_code_without_wait(): void
    {
        $teacher = $this->teacher();
        $firstMember = $this->student('quick-return-member-1@example.com');
        $secondMember = $this->student('quick-return-member-2@example.com');
        $opponent = $this->student('quick-return-opponent@example.com');
        $class = $this->createClass($teacher);

        foreach ([$firstMember, $secondMember, $opponent] as $student) {
            $this->enroll($class, $student);
        }

        $activity = $this->createActivity($class, $teacher, ['mode' => 'team']);
        $firstTeam = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Uno']);
        $opponentTeam = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Dos']);
        TeamMember::create(['team_id' => $firstTeam->id, 'student_id' => $firstMember->id]);
        TeamMember::create(['team_id' => $firstTeam->id, 'student_id' => $secondMember->id]);
        TeamMember::create(['team_id' => $opponentTeam->id, 'student_id' => $opponent->id]);
        $session = $this->createRoom($teacher, $activity, 2);

        foreach ([$firstMember, $opponent] as $student) {
            $this->actingAs($student)
                ->post(route('student.game-sessions.join'), ['code' => $session->code]);
        }

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id))
            ->assertRedirect()
            ->assertSessionMissing('error');

        $this->actingAs($secondMember)
            ->post(route('student.game-sessions.join'), ['code' => $session->code]);

        $presence = GameSessionPresence::query()
            ->where('game_session_id', $session->id)
            ->where('student_id', $secondMember->id)
            ->firstOrFail();
        $this->assertSame(GameSessionPresence::STATUS_ACTIVE, $presence->status);

        // La presencia sigue viva: regresa enseguida tras cerrar la ventana.
        $this->actingAs($secondMember)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id))
            ->assertSessionHas('success', 'Ya estás en esta sala.')
            ->assertSessionMissing('error');

        $this->assertSame(GameSessionPresence::STATUS_ACTIVE, $presence->fresh()->status);
        $this->assertSame(GameSession::STATUS_PLAYING, $session->fresh()->status);
    }

    public function test_team_with_a_single_member_can_rejoin_after_leaving_a_started_match(): void
    {
        $teacher = $this->teacher();
        $member = $this->student('solo-team-member@example.com');
        $memberB = $this->student('solo-team-opponent-b@example.com');
        $memberC = $this->student('solo-team-opponent-c@example.com');
        $class = $this->createClass($teacher);

        foreach ([$member, $memberB, $memberC] as $student) {
            $this->enroll($class, $student);
        }

        $activity = $this->createActivity($class, $teacher, ['mode' => 'team']);
        $team = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Uno']);
        $teamB = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Dos']);
        $teamC = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Tres']);
        TeamMember::create(['team_id' => $team->id, 'student_id' => $member->id]);
        TeamMember::create(['team_id' => $teamB->id, 'student_id' => $memberB->id]);
        TeamMember::create(['team_id' => $teamC->id, 'student_id' => $memberC->id]);
        $session = $this->createRoom($teacher, $activity, 3);

        foreach ([$member, $memberB, $memberC] as $student) {
            $this->actingAs($student)
                ->post(route('student.game-sessions.join'), ['code' => $session->code]);
        }

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id))
            ->assertRedirect()
            ->assertSessionMissing('error');

        // El único integrante del equipo se sale con la partida en curso.
        $this->actingAs($member)
            ->post(route('student.game-sessions.leave', $session->id))
            ->assertRedirect();

        $teamParticipation = Participation::where('game_session_id', $session->id)
            ->where('team_id', $team->id)
            ->firstOrFail();
        $this->assertSame('abandoned', $teamParticipation->status);
        $this->assertSame(GameSession::STATUS_PLAYING, $session->fresh()->status);

        // Reingresa con el código: la sala vuelve a aceptar a su equipo.
        $this->actingAs($member)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id))
            ->assertSessionMissing('error');

        $rejoined = $teamParticipation->fresh();
        $this->assertSame('started', $rejoined->status);
        $this->assertNotNull($rejoined->started_at);
        $this->assertSame(0, $rejoined->score);

        $this->assertSame(
            GameSessionPresence::STATUS_ACTIVE,
            GameSessionPresence::query()
                ->where('game_session_id', $session->id)
                ->where('student_id', $member->id)
                ->whereNull('left_at')
                ->latest('id')
                ->value('status')
        );
        $this->assertSame(GameSession::STATUS_PLAYING, $session->fresh()->status);
    }

    public function test_invalid_code_is_rejected(): void
    {
        $this->actingAs($this->student('joiner@example.com'))
            ->post(route('student.game-sessions.join'), [
                'code' => 'KLS000',
            ])
            ->assertSessionHas('error', 'No existe una sala con ese código.');
    }

    public function test_join_page_is_available_for_students(): void
    {
        $this->actingAs($this->student())
            ->get(route('student.game-sessions.join-page'))
            ->assertOk()
            ->assertViewIs('student.game_sessions.join');
    }

    // -------------------------------------------------------------------------
    // Inicio
    // -------------------------------------------------------------------------

    public function test_cannot_start_with_less_than_two_players(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher);
        $session = $this->createRoom($teacher, $activity);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id))
            ->assertSessionHas('error', 'Se necesitan al menos 2 jugadores para iniciar.');

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'status' => 'waiting',
        ]);
    }

    public function test_teacher_starts_game_with_two_players(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id))
            ->assertRedirect(route('teacher.game-sessions.show', $session->id));

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

    public function test_non_owner_teacher_cannot_start(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $other = $this->teacher('other@example.com');

        $this->actingAs($other)
            ->post(route('teacher.game-sessions.start', $session->id))
            ->assertForbidden();

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'status' => 'waiting',
        ]);
    }

    public function test_student_cannot_start(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s1)
            ->post(route('teacher.game-sessions.start', $session->id))
            ->assertRedirect();

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'status' => 'waiting',
        ]);
    }

    public function test_room_is_cancelled_when_last_player_leaves_in_waiting(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

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

    public function test_owner_teacher_can_view_room(): void
    {
        ['teacher' => $teacher, 'session' => $session] = $this->setupRoom();

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.show', $session->id))
            ->assertOk()
            ->assertViewIs('teacher.game_sessions.show');
    }

    public function test_state_endpoint_returns_shared_state(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

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

    public function test_teacher_can_read_state_of_own_room(): void
    {
        ['teacher' => $teacher, 'session' => $session] = $this->setupRoom();

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.state', $session->id))
            ->assertOk()
            ->assertJsonPath('session_id', $session->id);
    }

    public function test_leave_marks_participation_abandoned(): void
    {
        ['s2' => $s2, 'session' => $session] = $this->setupRoom();

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

    public function test_non_participant_cannot_leave_someone_elses_room(): void
    {
        ['s1' => $participant, 's2' => $otherStudent, 'session' => $session] = $this->setupRoom();
        $this->actingAs($participant)
            ->post(route('student.game-sessions.join'), ['code' => $session->code]);

        $this->actingAs($otherStudent)
            ->post(route('student.game-sessions.leave', $session->id))
            ->assertForbidden();

        $this->assertDatabaseHas('participations', [
            'game_session_id' => $session->id,
            'student_id' => $participant->id,
            'status' => 'waiting',
        ]);
        $this->assertDatabaseMissing('participations', [
            'game_session_id' => $session->id,
            'student_id' => $otherStudent->id,
        ]);
    }

    public function test_cannot_join_playing_session(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id));

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

    public function test_stale_team_member_presence_allows_rejoin_after_disconnect(): void
    {
        $teacher = $this->teacher();
        $firstMember = $this->student('stale-presence-member-1@example.com');
        $secondMember = $this->student('stale-presence-member-2@example.com');
        $opponent = $this->student('stale-presence-opponent@example.com');
        $class = $this->createClass($teacher);

        foreach ([$firstMember, $secondMember, $opponent] as $student) {
            $this->enroll($class, $student);
        }

        $activity = $this->createActivity($class, $teacher, ['mode' => 'team']);
        $firstTeam = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Uno']);
        $opponentTeam = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Dos']);
        TeamMember::create(['team_id' => $firstTeam->id, 'student_id' => $firstMember->id]);
        TeamMember::create(['team_id' => $firstTeam->id, 'student_id' => $secondMember->id]);
        TeamMember::create(['team_id' => $opponentTeam->id, 'student_id' => $opponent->id]);
        $session = $this->createRoom($teacher, $activity, 2);

        foreach ([$firstMember, $opponent] as $student) {
            $this->actingAs($student)
                ->post(route('student.game-sessions.join'), ['code' => $session->code]);
        }

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id))
            ->assertRedirect();

        $teamParticipation = $session->participations()->where('team_id', $firstTeam->id)->firstOrFail();

        $this->actingAs($secondMember)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id));

        $presence = GameSessionPresence::query()
            ->where('game_session_id', $session->id)
            ->where('student_id', $firstMember->id)
            ->firstOrFail();

        // Simula una desconexión abrupta: la presencia sigue activa pero con
        // una fecha de entrada antigua.
        $presence->update([
            'joined_at' => now()->subHours(2),
        ]);

        // Simula una partida larga: la presencia del equipo rival también es
        // antigua aunque el jugador sigue jugando activamente; el reingreso
        // no debe barrerlo de la sala.
        $opponentPresence = GameSessionPresence::query()
            ->where('game_session_id', $session->id)
            ->where('student_id', $opponent->id)
            ->firstOrFail();

        $opponentPresence->update([
            'joined_at' => now()->subHours(2),
        ]);

        // El reingreso se resuelve con la expiración lazy de la propia
        // presencia: no se espera al job y no se expulsa a nadie más.
        $this->actingAs($firstMember)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id));

        $this->assertSame(GameSessionPresence::STATUS_ACTIVE, $presence->fresh()->status);
        $this->assertSame($teamParticipation->id, $presence->fresh()->participation_id);

        $this->assertSame(
            GameSessionPresence::STATUS_ACTIVE,
            $opponentPresence->fresh()->status
        );

        // Tras el reingreso el participante debe poder consultar el estado
        // sin recibir un error de autorización.
        $this->actingAs($firstMember)
            ->get(route('student.game-sessions.state', $session->id))
            ->assertOk();
    }

    public function test_stale_presence_in_waiting_session_allows_rejoin_after_disconnect(): void
    {
        $teacher = $this->teacher();
        $firstMember = $this->student('stale-waiting-member-1@example.com');
        $secondMember = $this->student('stale-waiting-member-2@example.com');
        $class = $this->createClass($teacher);

        foreach ([$firstMember, $secondMember] as $student) {
            $this->enroll($class, $student);
        }

        $activity = $this->createActivity($class, $teacher, ['mode' => 'team']);
        $team = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Uno']);
        TeamMember::create(['team_id' => $team->id, 'student_id' => $firstMember->id]);
        TeamMember::create(['team_id' => $team->id, 'student_id' => $secondMember->id]);
        $session = $this->createRoom($teacher, $activity, 2);

        foreach ([$firstMember, $secondMember] as $student) {
            $this->actingAs($student)
                ->post(route('student.game-sessions.join'), ['code' => $session->code]);
        }

        $presence = GameSessionPresence::query()
            ->where('game_session_id', $session->id)
            ->where('student_id', $firstMember->id)
            ->firstOrFail();

        $presence->update([
            'joined_at' => now()->subHours(2),
        ]);

        $this->actingAs($firstMember)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id));

        $this->assertSame(GameSessionPresence::STATUS_ACTIVE, $presence->fresh()->status);
    }

    public function test_individual_participant_can_rejoin_after_abrupt_disconnect(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupRoom();

        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), ['code' => $session->code]);

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), ['code' => $session->code]);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id));

        $session->refresh();

        $participation = Participation::where('game_session_id', $session->id)
            ->where('student_id', $s1->id)
            ->firstOrFail();

        // Simula una desconexión abrupta: la participación sigue "started"
        // pero con una fecha de inicio muy antigua.
        $participation->started_at = now()->subMinutes(5);
        $participation->save();

        // El reingreso se resuelve como started: no se espera al job, no
        // toca al resto de jugadores y puede volver a responder.
        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), ['code' => $session->code])
            ->assertRedirect(route('student.game-sessions.show', $session->id));

        $rejoined = Participation::where('game_session_id', $session->id)
            ->where('student_id', $s1->id)
            ->firstOrFail();

        $this->assertSame('started', $rejoined->status);
        $this->assertNotNull($rejoined->started_at);
        $this->assertTrue($rejoined->started_at?->gt(now()->subSeconds(GameSessionService::PRESENCE_TIMEOUT_SECONDS)));

        $otherParticipation = Participation::where('game_session_id', $session->id)
            ->where('student_id', $s2->id)
            ->firstOrFail();

        $this->assertSame('started', $otherParticipation->fresh()->status);
        $this->assertSame(GameSession::STATUS_PLAYING, $session->fresh()->status);
    }
}
