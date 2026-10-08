<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\GameSession;
use App\Models\Participation;
use App\Models\Roulette;
use App\Models\RouletteItem;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\RouletteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
     * Crea la escena completa: actividad ruleta publicada,
     * dos estudiantes inscritos, sala con ambos y partida iniciada.
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

        $this->actingAs($s1)
            ->post(route('student.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => 2,
            ]);

        $session = GameSession::where('activity_id', $activity->id)->first();

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), [
                'code' => $session->code,
            ]);

        $this->actingAs($s1)
            ->post(route('student.game-sessions.start', $session->id));

        return compact('teacher', 's1', 's2', 'activity', 'roulette', 'session');
    }

    private function participationOf(GameSession $session, User $student): Participation
    {
        return Participation::where('game_session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();
    }

    private function spin(GameSession $session, User $student): TestResponse
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

    // -------------------------------------------------------------------------
    // Partida
    // -------------------------------------------------------------------------

    public function test_play_session_page_is_available_for_participants(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupPlayingGame();

        $response = $this->actingAs($s1)
            ->get(route('student.game-sessions.play', $session->id))
            ->assertRedirect(route('student.roulette.play-session', $session->id));

        $this->actingAs($s1)
            ->get(route('student.roulette.play-session', $session->id))
            ->assertOk()
            ->assertViewIs('student.roulette.multiplayer')
            ->assertViewHas('session', fn ($value) => $value->id === $session->id);
    }

    public function test_only_turn_holder_can_spin(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $session->refresh();

        $this->assertSame(
            $this->participationOf($session, $s1)->id,
            $session->current_turn_participation_id
        );

        $this->spin($session, $s2)
            ->assertStatus(403)
            ->assertJsonPath('error', 'No es tu turno.');
    }

    public function test_turn_holder_can_spin_and_get_item_without_answer(): void
    {
        ['s1' => $s1, 'session' => $session, 'roulette' => $roulette] = $this->setupPlayingGame();

        $response = $this->spin($session, $s1)->assertOk();

        $data = $response->json();

        $this->assertFalse($data['completed']);
        $this->assertNull($data['item']['correct_option'] ?? null);
        $this->assertArrayHasKey('id', $data['item']);

        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'state' => json_encode(['roulette_item_id' => $data['item']['id']]),
        ]);
    }

    public function test_answer_without_spin_is_rejected(): void
    {
        ['s1' => $s1, 'session' => $session, 'roulette' => $roulette] = $this->setupPlayingGame();

        $item = RouletteItem::where('roulette_id', $roulette->id)->first();

        $this->answer($session, $s1, $item->id, 'a')
            ->assertStatus(422)
            ->assertJsonPath('error', 'Primero gira la ruleta.');
    }

    public function test_spin_while_question_pending_is_rejected(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupPlayingGame();

        $this->spin($session, $s1)->assertOk();

        $this->spin($session, $s1)
            ->assertStatus(422)
            ->assertJsonPath('error', 'Primero responde la pregunta actual.');
    }

    public function test_answering_non_pending_item_is_rejected(): void
    {
        ['s1' => $s1, 'session' => $session, 'roulette' => $roulette] = $this->setupPlayingGame();

        $spin = $this->spin($session, $s1)->assertOk();
        $pendingItem = RouletteItem::find($spin->json('item.id'));

        $otherItem = RouletteItem::where('roulette_id', $roulette->id)
            ->where('id', '!=', $pendingItem->id)
            ->first();

        $this->answer($session, $s1, $otherItem->id, 'a')
            ->assertStatus(422)
            ->assertJsonPath('error', 'Responde la pregunta actual de la ruleta.');
    }

    public function test_full_round_robin_finishes_and_declares_winner(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session, 'roulette' => $roulette] = $this->setupPlayingGame();

        // Turno 1: s1 responde correcto
        $spin1 = $this->spin($session, $s1)->assertOk();
        $item1 = RouletteItem::find($spin1->json('item.id'));

        $r1 = $this->answer($session, $s1, $item1->id, $item1->correct_option)
            ->assertOk()
            ->json();

        $this->assertTrue($r1['is_correct']);
        $this->assertFalse($r1['finished']);

        // Turno 2: s2 responde correcto
        $session->refresh();
        $this->assertSame(
            $this->participationOf($session, $s2)->id,
            $session->current_turn_participation_id
        );

        $spin2 = $this->spin($session, $s2)->assertOk();
        $item2 = RouletteItem::find($spin2->json('item.id'));

        $this->answer($session, $s2, $item2->id, $item2->correct_option)
            ->assertOk()
            ->assertJsonPath('is_correct', true)
            ->assertJsonPath('finished', false);

        // Turno 3: s1 responde su última pregunta -> partida termina
        $session->refresh();
        $this->assertSame(
            $this->participationOf($session, $s1)->id,
            $session->current_turn_participation_id
        );

        $spin3 = $this->spin($session, $s1)->assertOk();
        $item3 = RouletteItem::find($spin3->json('item.id'));

        $this->assertNotNull($item3);
        $this->assertTrue($item3->id !== $item1->id);

        $this->answer($session, $s1, $item3->id, $item3->correct_option)
            ->assertOk()
            ->assertJsonPath('completed', true)
            ->assertJsonPath('finished', true);

        $session->refresh();

        $this->assertSame('finished', $session->status);
        $this->assertNotNull($session->finished_at);
        $this->assertSame('winner', $session->result_type);

        $p1 = $this->participationOf($session, $s1);
        $p2 = $this->participationOf($session, $s2);

        $this->assertSame('completed', $p1->status);
        $this->assertSame('completed', $p2->status);

        // s1 respondió ambas correcto (100 pts); s2 solo una (33 o 67 pts).
        $this->assertSame($p1->id, $session->winner_participation_id);
    }

    public function test_answer_can_only_target_random_pending_item(): void
    {
        ['s1' => $s1, 'session' => $session, 'roulette' => $roulette] = $this->setupPlayingGame();

        $spin1 = $this->spin($session, $s1)->assertOk();
        $item1 = RouletteItem::find($spin1->json('item.id'));

        $this->answer($session, $s1, $item1->id, $item1->correct_option)->assertOk();

        // Al responder se limpia la pregunta en vuelo
        $this->assertDatabaseHas('game_sessions', [
            'id' => $session->id,
            'state' => json_encode([]),
        ]);
    }

    public function test_result_page_shows_ranking(): void
    {
        ['s1' => $s1, 's2' => $s2, 'session' => $session] = $this->setupPlayingGame();

        $this->playUntilFinished($session, $s1, $s2);

        $session->refresh();
        $this->assertSame('finished', $session->status);

        $this->actingAs($s1)
            ->get(route('student.game-sessions.result', $session->id))
            ->assertOk()
            ->assertViewIs('student.game_sessions.result')
            ->assertViewHas('players');
    }

    /**
     * Juega rondas girando y respondiendo el ítem que devuelve el servidor
     * hasta que la partida termina.
     */
    private function playUntilFinished(GameSession $session, User $s1, User $s2): void
    {
        $player = $s1;

        while (true) {
            $session->refresh();

            if ($session->status === 'finished') {
                return;
            }

            $current = $session->current_turn_participation_id === $this->participationOf($session, $s1)->id
                ? $s1
                : $s2;

            $item = RouletteItem::find($this->spin($session, $current)->assertOk()->json('item.id'));

            $this->answer($session, $current, $item->id, $item->correct_option)->assertOk();

            $player = $current === $s1 ? $s2 : $s1;
        }
    }
}
