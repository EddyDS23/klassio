<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\GameSession;
use App\Models\Participation;
use App\Models\Roulette;
use App\Models\RouletteAnswer;
use App\Models\RouletteItem;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\GameSessionService;
use App\Services\RouletteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MultiplayerRouletteTest extends TestCase
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
            'description' => 'Multiplayer roulette',
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

    private function createActivity(SchoolClass $class, User $teacher): Activity
    {
        return Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Rueda del saber',
            'description' => 'Multiplayer roulette activity',
            'type' => 'roulette',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
            'due_at' => null,
            'status' => 'published',
        ]);
    }

    private function createRoulette(Activity $activity): Roulette
    {
        return app(RouletteService::class)->buildRoulette($activity, [
            [
                'question' => '¿Qué es HTTP?',
                'option_a' => 'Protocolo de transferencia',
                'option_b' => 'Sistema operativo',
                'option_c' => 'Lenguaje de programación',
                'option_d' => 'Base de datos',
                'correct_option' => 'a',
                'points' => 10,
            ],
            [
                'question' => '¿Qué es PHP?',
                'option_a' => 'Base de datos',
                'option_b' => 'Lenguaje de programación',
                'option_c' => 'Framework',
                'option_d' => 'Servidor web',
                'correct_option' => 'b',
                'points' => 20,
            ],
        ]);
    }

    /**
     * Crea la escena completa: actividad ruleta publicada, dos
     * estudiantes inscritos, sala creada por el profesor, ambos unidos
     * por código y partida iniciada por el profesor.
     */
    private function setupPlayingGame()
    {
        $teacher = $this->teacher();
        $s1 = $this->student('student1@example.com');
        $s2 = $this->student('student2@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $s1);
        $this->enroll($class, $s2);

        $activity = $this->createActivity($class, $teacher);
        $roulette = $this->createRoulette($activity);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 2,
            ]);

        $session = GameSession::where('activity_id', $activity->id)->first();

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

        $session->refresh();

        return compact('teacher', 's1', 's2', 'activity', 'roulette', 'session');
    }

    private function participationOf(GameSession $session, User $student): Participation
    {
        return Participation::where('game_session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();
    }

    /**
     * El profesor gira la ruleta (abre una ronda).
     */
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

    /**
     * Un jugador solicita la pregunta de la ronda actual (solo lectura).
     */
    private function fetchRound(GameSession $session, User $student): TestResponse
    {
        return $this->actingAs($student)
            ->post(route('student.roulette.spin-session'), [
                'session_id' => $session->id,
            ]);
    }

    private function answer(
        GameSession $session,
        User $student,
        int $itemId,
        string $response
    ): TestResponse {
        return $this->actingAs($student)
            ->post(route('student.roulette.answer-session'), [
                'session_id' => $session->id,
                'roulette_item_id' => $itemId,
                'response' => $response,
            ]);
    }

    private function state(GameSession $session, User $student): array
    {
        return $this->actingAs($student)
            ->getJson(route('student.game-sessions.state', $session->id))
            ->assertOk()
            ->json();
    }

    private function wrongOption(RouletteItem $item): string
    {
        return collect(['a', 'b', 'c', 'd'])
            ->first(fn (string $key) => $key !== $item->correct_option);
    }

    // -------------------------------------------------------------------------
    // Rondas simultáneas controladas por el profesor
    // -------------------------------------------------------------------------

    public function test_play_session_page_is_available_for_participants(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupPlayingGame();

        $this->actingAs($s1)
            ->get(route('student.game-sessions.play', $session->id))
            ->assertRedirect(route('student.roulette.play-session', $session->id));

        $this->actingAs($s1)
            ->get(route('student.roulette.play-session', $session->id))
            ->assertOk()
            ->assertViewIs('student.roulette.multiplayer')
            ->assertViewHas('session', fn ($value) => $value->id === $session->id);
    }

    public function test_teacher_spin_opens_a_round_and_both_players_see_the_same_item(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $spin = $this->teacherSpin($session, $teacher)->assertOk()->json();

        $this->assertFalse($spin['finished']);
        $this->assertNotNull($spin['roulette_item_id']);

        $r1 = $this->fetchRound($session, $s1)->assertOk()->json();
        $r2 = $this->fetchRound($session, $s2)->assertOk()->json();

        $this->assertSame($spin['roulette_item_id'], $r1['item']['id']);
        $this->assertSame($r1['item']['id'], $r2['item']['id']);
        $this->assertArrayNotHasKey('correct_option', $r1['item']);
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

    public function test_students_can_only_read_the_round_not_spin(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 'session' => $session] = $this->setupPlayingGame();

        $this->fetchRound($session, $s1)
            ->assertOk()
            ->assertJsonPath('waiting', true);

        $session->refresh();
        $this->assertNull($session->state['roulette_item_id'] ?? null);

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

        // No se puede girar mientras la ronda sigue abierta.
        $this->teacherSpin($session, $teacher, 45)
            ->assertStatus(422)
            ->assertJsonPath('error', 'Espera a que termine la ronda actual.');

        // Al agotarse el tiempo se cierra y el tiempo se limita al mínimo.
        $this->travel(46)->seconds();

        $clamped = $this->teacherSpin($session, $teacher, 1)->assertOk()->json();
        $this->assertSame(GameSessionService::MIN_ROUND_SECONDS, $clamped['round_seconds']);
    }

    public function test_round_closes_when_all_players_answered_and_waits_for_teacher(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $round1 = $this->teacherSpin($session, $teacher)->assertOk()->json()['roulette_item_id'];
        $item1 = RouletteItem::find($round1);

        $this->answer($session, $s1, $round1, $item1->correct_option)
            ->assertOk()
            ->assertJsonPath('waiting_for_others', true);

        $session->refresh();
        $this->assertSame($round1, $session->state['roulette_item_id']);

        $this->answer($session, $s2, $round1, $item1->correct_option)->assertOk();

        $session->refresh();
        $this->assertNull($session->state['roulette_item_id'] ?? null);
        $this->assertSame('playing', $session->status);

        $round2 = $this->teacherSpin($session, $teacher)->assertOk()->json()['roulette_item_id'];
        $this->assertNotSame($round1, $round2);
    }

    public function test_players_who_do_not_answer_in_time_fail(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $round = $this->teacherSpin($session, $teacher, 5)->assertOk()->json()['roulette_item_id'];
        $item = RouletteItem::find($round);

        $this->answer($session, $s1, $round, $item->correct_option)->assertOk();

        $this->travel(6)->seconds();

        $state = $this->state($session, $s2);

        $this->assertNull($state['roulette_item_id']);

        $miss = RouletteAnswer::where(
            'participation_id',
            $this->participationOf($session, $s2)->id
        )
            ->where('roulette_item_id', $round)
            ->first();

        $this->assertNotNull($miss);
        $this->assertFalse($miss->is_correct);
        $this->assertSame(0, $miss->score);
        $this->assertSame(GameSessionService::MISSED_RESPONSE, $miss->response);

        // El jugador que sí respondió conserva su acierto.
        $hit = RouletteAnswer::where(
            'participation_id',
            $this->participationOf($session, $s1)->id
        )
            ->where('roulette_item_id', $round)
            ->first();

        $this->assertTrue($hit->is_correct);
    }

    public function test_answering_after_timeout_is_rejected(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $round = $this->teacherSpin($session, $teacher, 5)->assertOk()->json()['roulette_item_id'];
        $item = RouletteItem::find($round);

        $this->travel(6)->seconds();

        $this->answer($session, $s1, $round, $item->correct_option)
            ->assertStatus(422);
    }

    public function test_answering_non_current_item_is_rejected(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 'session' => $session, 'roulette' => $roulette] = $this->setupPlayingGame();

        $round = $this->teacherSpin($session, $teacher)->assertOk()->json()['roulette_item_id'];

        $other = RouletteItem::where('roulette_id', $roulette->id)
            ->where('id', '!=', $round)
            ->first();

        $this->answer($session, $s1, $other->id, 'a')
            ->assertStatus(422)
            ->assertJsonPath('error', 'Responde la pregunta actual de la ruleta.');
    }

    public function test_player_cannot_answer_the_same_round_twice(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 'session' => $session] = $this->setupPlayingGame();

        $round = $this->teacherSpin($session, $teacher)->assertOk()->json()['roulette_item_id'];
        $item = RouletteItem::find($round);

        $this->answer($session, $s1, $round, $item->correct_option)->assertOk();

        $this->answer($session, $s1, $round, $item->correct_option)
            ->assertStatus(422)
            ->assertJsonPath('error', 'Ya respondiste esta pregunta.');
    }

    public function test_state_reports_whether_the_player_answered_the_round(): void
    {
        ['teacher' => $teacher, 's1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $round = $this->teacherSpin($session, $teacher)->assertOk()->json()['roulette_item_id'];
        $item = RouletteItem::find($round);

        $this->assertFalse($this->state($session, $s1)['answered_current']);

        $this->answer($session, $s1, $round, $item->correct_option)->assertOk();

        $this->assertTrue($this->state($session, $s1)['answered_current']);
        $this->assertFalse($this->state($session, $s2)['answered_current']);
        $this->assertSame($round, $this->state($session, $s1)['roulette_item_id']);
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
     * Juega todas las rondas: el profesor gira; s1 acierta y s2 falla
     * hasta que la partida termina.
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

            $itemId = $spin['roulette_item_id'] ?? null;

            if ($itemId === null) {
                continue;
            }

            $item = RouletteItem::find($itemId);

            $this->answer($session, $s1, $itemId, $item->correct_option)->assertOk();
            $this->answer($session, $s2, $itemId, $this->wrongOption($item))->assertOk();
        }
    }
}
