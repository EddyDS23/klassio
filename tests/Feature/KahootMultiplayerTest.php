<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\GameSession;
use App\Models\Kahoot;
use App\Models\KahootAnswer;
use App\Models\Option;
use App\Models\Participation;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\GameSessionService;
use App\Services\KahootService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class KahootMultiplayerTest extends TestCase
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
            'description' => 'Multiplayer kahoot',
            'code' => strtoupper(fake()->unique()->bothify('CLASS###')),
            'status' => 'active',
        ]);
    }

    private function enroll(SchoolClass $class, User $student): void
    {
        Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'active',
        ]);
    }

    private function createActivity(SchoolClass $class, User $teacher): Activity
    {
        return Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Kahoot del saber',
            'description' => 'Multiplayer kahoot activity',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
            'due_at' => null,
            'status' => 'published',
        ]);
    }

    private function createKahoot(Activity $activity): Kahoot
    {
        $kahoot = Kahoot::create(['activity_id' => $activity->id]);

        app(KahootService::class)->store($kahoot, [
            'questions' => [
                [
                    'question' => '¿Qué es HTTP?',
                    'time_limit' => 30,
                    'score' => 10,
                    'options' => [
                        ['text' => 'Protocolo de transferencia', 'is_correct' => '1'],
                        ['text' => 'Sistema operativo', 'is_correct' => '0'],
                        ['text' => 'Lenguaje de programación', 'is_correct' => '0'],
                    ],
                ],
                [
                    'question' => '¿Qué es PHP?',
                    'time_limit' => 30,
                    'score' => 20,
                    'options' => [
                        ['text' => 'Base de datos', 'is_correct' => '0'],
                        ['text' => 'Lenguaje de programación', 'is_correct' => '1'],
                        ['text' => 'Servidor web', 'is_correct' => '0'],
                    ],
                ],
            ],
        ]);

        return $kahoot->fresh();
    }

    private function setupPlayingGame()
    {
        $teacher = $this->teacher();
        $s1 = $this->student('student1@example.com');
        $s2 = $this->student('student2@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $s1);
        $this->enroll($class, $s2);

        $activity = $this->createActivity($class, $teacher);
        $kahoot = $this->createKahoot($activity);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 2,
            ]);

        $session = GameSession::where('activity_id', $activity->id)->first();

        $this->actingAs($s1)->post(route('student.game-sessions.join'), ['code' => $session->code]);
        $this->actingAs($s2)->post(route('student.game-sessions.join'), ['code' => $session->code]);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id));

        $session->refresh();

        return compact('teacher', 's1', 's2', 'activity', 'kahoot', 'session');
    }

    private function participationOf(GameSession $session, User $student): Participation
    {
        return Participation::where('game_session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();
    }

    private function teacherSpin(
        GameSession $session,
        User $teacher,
        int $seconds = 30
    ): TestResponse {
        return $this->actingAs($teacher)
            ->postJson(route('teacher.game-sessions.spin', $session->id), [
                'seconds' => $seconds,
            ]);
    }

    private function fetchRound(GameSession $session, User $student): TestResponse
    {
        return $this->actingAs($student)
            ->post(route('student.kahoot.question-session'), [
                'session_id' => $session->id,
            ]);
    }

    private function answer(
        GameSession $session,
        User $student,
        int $questionId,
        int $optionId
    ): TestResponse {
        return $this->actingAs($student)
            ->post(route('student.kahoot.answer-session'), [
                'session_id' => $session->id,
                'question_id' => $questionId,
                'option_id' => $optionId,
            ]);
    }

    private function state(GameSession $session, User $student): array
    {
        return $this->actingAs($student)
            ->getJson(route('student.game-sessions.state', $session->id))
            ->assertOk()
            ->json();
    }

    private function correctOptionId(int $questionId): int
    {
        return (int) Option::where('question_id', $questionId)
            ->where('is_correct', true)
            ->value('id');
    }

    private function wrongOptionId(int $questionId): int
    {
        return (int) Option::where('question_id', $questionId)
            ->where('is_correct', false)
            ->value('id');
    }

    // -------------------------------------------------------------------------
    // Rondas controladas por el profesor
    // -------------------------------------------------------------------------

    public function test_play_session_page_is_available_for_participants(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupPlayingGame();

        $this->actingAs($s1)
            ->get(route('student.game-sessions.play', $session->id))
            ->assertRedirect(route('student.kahoot.play-session', $session->id));

        $this->actingAs($s1)
            ->get(route('student.kahoot.play-session', $session->id))
            ->assertOk()
            ->assertViewIs('student.kahoot.multiplayer')
            ->assertViewHas('session', fn ($value) => $value->id === $session->id);
    }

    public function test_teacher_spin_opens_a_round_and_both_players_see_the_same_question(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $spin = $this->teacherSpin($session, $teacher)->assertOk()->json();

        $this->assertFalse($spin['finished']);
        $this->assertNotNull($spin['question_id']);

        $r1 = $this->fetchRound($session, $s1)->assertOk()->json();
        $r2 = $this->fetchRound($session, $s2)->assertOk()->json();

        $this->assertSame($spin['question_id'], $r1['item']['id']);
        $this->assertSame($r1['item']['id'], $r2['item']['id']);
        $this->assertArrayNotHasKey('is_correct', $r1['item']['options'][0]);
    }

    public function test_only_the_owning_teacher_can_spin(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 'session' => $session] = $this->setupPlayingGame();

        $other = $this->teacher('other@example.com');

        $this->assertTrue(Gate::forUser($teacher)->allows('spin', $session));
        $this->assertFalse(Gate::forUser($other)->allows('spin', $session));
        $this->assertFalse(Gate::forUser($s1)->allows('spin', $session));

        $this->actingAs($other)
            ->postJson(route('teacher.game-sessions.spin', $session->id), ['seconds' => 30])
            ->assertStatus(403);
    }

    public function test_students_can_only_read_the_round_not_advance(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 'session' => $session] = $this->setupPlayingGame();

        $this->fetchRound($session, $s1)
            ->assertOk()
            ->assertJsonPath('waiting', true);

        $session->refresh();
        $this->assertNull($session->state['kahoot_question_id'] ?? null);

        $this->teacherSpin($session, $teacher)->assertOk();

        $this->fetchRound($session, $s1)
            ->assertOk()
            ->assertJsonMissingPath('waiting');
    }

    public function test_teacher_configured_seconds_are_respected_and_clamped(): void
    {
        ['teacher' => $teacher, 'session' => $session] = $this->setupPlayingGame();

        $spin = $this->teacherSpin($session, $teacher, 45)->assertOk()->json();
        $this->assertSame(45, $spin['round_seconds']);

        $session->refresh();
        $this->assertSame(45, $session->state['round_seconds']);

        $remaining = app(GameSessionService::class)->roundTimeRemaining($session);
        $this->assertGreaterThanOrEqual(44, $remaining);
        $this->assertLessThanOrEqual(45, $remaining);

        $this->teacherSpin($session, $teacher, 45)
            ->assertStatus(422)
            ->assertJsonPath('error', 'Espera a que termine la pregunta actual.');

        $this->travel(46)->seconds();

        $clamped = $this->teacherSpin($session, $teacher, 1)->assertOk()->json();
        $this->assertSame(GameSessionService::MIN_ROUND_SECONDS, $clamped['round_seconds']);
    }

    public function test_round_closes_when_all_players_answered_and_waits_for_teacher(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $round1 = $this->teacherSpin($session, $teacher)->assertOk()->json()['question_id'];

        $this->answer($session, $s1, $round1, $this->correctOptionId($round1))
            ->assertOk()
            ->assertJsonPath('waiting_for_others', true);

        $session->refresh();
        $this->assertSame($round1, $session->state['kahoot_question_id']);

        $this->answer($session, $s2, $round1, $this->correctOptionId($round1))->assertOk();

        $session->refresh();
        $this->assertNull($session->state['kahoot_question_id'] ?? null);
        $this->assertSame('playing', $session->status);

        $round2 = $this->teacherSpin($session, $teacher)->assertOk()->json()['question_id'];
        $this->assertNotSame($round1, $round2);
    }

    public function test_players_who_do_not_answer_in_time_fail(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $round = $this->teacherSpin($session, $teacher, 5)->assertOk()->json()['question_id'];

        $this->answer($session, $s1, $round, $this->correctOptionId($round))->assertOk();

        $this->travel(6)->seconds();

        $state = $this->state($session, $s2);
        $this->assertNull($state['question_id']);

        $miss = KahootAnswer::where('participation_id', $this->participationOf($session, $s2)->id)
            ->where('question_id', $round)
            ->first();

        $this->assertNotNull($miss);
        $this->assertNull($miss->option_id);
        $this->assertFalse($miss->is_correct);
        $this->assertSame(0, $miss->score);

        $hit = KahootAnswer::where('participation_id', $this->participationOf($session, $s1)->id)
            ->where('question_id', $round)
            ->first();

        $this->assertTrue($hit->is_correct);
    }

    public function test_answering_after_timeout_is_rejected(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 'session' => $session] = $this->setupPlayingGame();

        $round = $this->teacherSpin($session, $teacher, 5)->assertOk()->json()['question_id'];

        $this->travel(6)->seconds();

        $this->answer($session, $s1, $round, $this->correctOptionId($round))
            ->assertStatus(422);
    }

    public function test_answering_non_current_question_is_rejected(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 'session' => $session, 'kahoot' => $kahoot] = $this->setupPlayingGame();

        $round = $this->teacherSpin($session, $teacher)->assertOk()->json()['question_id'];

        $other = Question::where('kahoot_id', $kahoot->id)
            ->where('id', '!=', $round)
            ->first();

        $this->answer($session, $s1, $other->id, $this->correctOptionId($other->id))
            ->assertStatus(422)
            ->assertJsonPath('error', 'Responde la pregunta actual.');
    }

    public function test_player_cannot_answer_the_same_round_twice(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 'session' => $session] = $this->setupPlayingGame();

        $round = $this->teacherSpin($session, $teacher)->assertOk()->json()['question_id'];

        $this->answer($session, $s1, $round, $this->correctOptionId($round))->assertOk();

        $this->answer($session, $s1, $round, $this->correctOptionId($round))
            ->assertStatus(422)
            ->assertJsonPath('error', 'Ya respondiste esta pregunta.');
    }

    public function test_state_reports_whether_the_player_answered_the_round(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $round = $this->teacherSpin($session, $teacher)->assertOk()->json()['question_id'];

        $this->assertFalse($this->state($session, $s1)['answered_current']);

        $this->answer($session, $s1, $round, $this->correctOptionId($round))->assertOk();

        $this->assertTrue($this->state($session, $s1)['answered_current']);
        $this->assertFalse($this->state($session, $s2)['answered_current']);
        $this->assertSame($round, $this->state($session, $s1)['question_id']);
    }

    public function test_game_finishes_after_all_rounds_and_declares_winner(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $this->playUntilFinished($session, $teacher, $s1, $s2);

        $session->refresh();

        $this->assertSame('finished', $session->status);
        $this->assertSame('winner', $session->result_type);
        $this->assertNotNull($session->finished_at);
        $this->assertSame(
            $this->participationOf($session, $s1)->id,
            $session->winner_participation_id
        );
    }

    public function test_result_page_shows_ranking(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $this->playUntilFinished($session, $teacher, $s1, $s2);

        $session->refresh();
        $this->assertSame('finished', $session->status);

        $this->actingAs($s1)
            ->get(route('student.game-sessions.result', $session->id))
            ->assertOk()
            ->assertViewIs('student.game_sessions.result')
            ->assertViewHas('players');
    }

    /**
     * Juega todas las rondas: el profesor avanza; s1 acierta y s2 falla.
     */
    private function playUntilFinished(
        GameSession $session,
        User $teacher,
        User $s1,
        User $s2
    ): void {
        $guard = 0;

        while ($guard++ < 50) {
            $session->refresh();

            if ($session->status === 'finished') {
                return;
            }

            $spin = $this->teacherSpin($session, $teacher)->assertOk()->json();

            if (! empty($spin['finished'])) {
                return;
            }

            $questionId = $spin['question_id'] ?? null;

            if ($questionId === null) {
                continue;
            }

            $this->answer($session, $s1, $questionId, $this->correctOptionId($questionId))->assertOk();
            $this->answer($session, $s2, $questionId, $this->wrongOptionId($questionId))->assertOk();
        }
    }
}
