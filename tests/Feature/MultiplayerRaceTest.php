<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Crossword;
use App\Models\Enrollment;
use App\Models\GameSession;
use App\Models\Matching;
use App\Models\MatchingItem;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Word;
use App\Services\CrosswordService;
use App\Services\MatchingService;
use App\Services\WordsearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MultiplayerRaceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tipos de actividad que se juegan en carrera libre.
     *
     * @var list<string>
     */
    private const RACE_TYPES = ['matching', 'word_search', 'crossword'];

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
            'description' => 'Multiplayer race',
            'code' => strtoupper(fake()->unique()->bothify('CLASS###')),
            'status' => 'active',
        ]);
    }

    private function enroll(SchoolClass $class, User $student): Enrollment
    {
        return Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'active',
        ]);
    }

    private function createActivity(
        SchoolClass $class,
        User $teacher,
        string $type,
        ?int $timeLimit = null
    ): Activity {
        return Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Carrera '.$type,
            'description' => 'Multiplayer race activity',
            'type' => $type,
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => $timeLimit,
            'attempts' => 1,
            'due_at' => null,
            'status' => 'published',
        ]);
    }

    /**
     * Construye el contenido del juego según el tipo de actividad.
     */
    private function buildContent(Activity $activity, string $type): void
    {
        match ($type) {
            'matching' => app(MatchingService::class)->buildMatching($activity, [
                ['left' => 'HTTP', 'right' => 'Protocolo de transferencia', 'score' => 10],
                ['left' => 'PHP', 'right' => 'Lenguaje de programación', 'score' => 20],
            ]),
            'word_search' => app(WordsearchService::class)->buildWordsearch(
                $activity,
                10,
                10,
                [['word' => 'LARAVEL', 'score' => 10]]
            ),
            'crossword' => tap(
                Crossword::create([
                    'activity_id' => $activity->id,
                    'rows' => 0,
                    'columns' => 0,
                    'grid' => [],
                ]),
                fn () => app(CrosswordService::class)->store($activity, [
                    'words' => [
                        ['word' => 'LARAVEL', 'clue' => 'Framework PHP', 'score' => 10],
                        ['word' => 'VALUE', 'clue' => 'Valor en inglés', 'score' => 20],
                    ],
                ])
            ),
            default => null,
        };
    }

    /**
     * Escena completa de carrera libre: actividad publicada, dos
     * estudiantes inscritos, sala creada por el profesor, ambos unidos
     * por código y partida iniciada.
     */
    private function setupRace(
        string $type,
        ?int $timeLimit = null,
        int $maxPlayers = 2
    ): array {
        $teacher = $this->teacher();
        $s1 = $this->student('student1@example.com');
        $s2 = $this->student('student2@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $s1);
        $this->enroll($class, $s2);

        $activity = $this->createActivity($class, $teacher, $type, $timeLimit);
        $this->buildContent($activity, $type);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.store'), [
                'activity_id' => $activity->id,
                'max_players' => $maxPlayers,
            ]);

        $session = GameSession::where('activity_id', $activity->id)
            ->latest('id')
            ->firstOrFail();

        $this->actingAs($s1)
            ->post(route('student.game-sessions.join'), ['code' => $session->code]);

        $this->actingAs($s2)
            ->post(route('student.game-sessions.join'), ['code' => $session->code]);

        $this->actingAs($teacher)
            ->post(route('teacher.game-sessions.start', $session->id));

        $session->refresh();

        return compact('teacher', 's1', 's2', 'class', 'activity', 'session');
    }

    private function participationOf(GameSession $session, User $student): Participation
    {
        return Participation::where('game_session_id', $session->id)
            ->where('student_id', $student->id)
            ->firstOrFail();
    }

    private function state(GameSession $session, User $student): array
    {
        return $this->actingAs($student)
            ->getJson(route('student.game-sessions.state', $session->id))
            ->assertOk()
            ->json();
    }

    // -------------------------------------------------------------------------
    // Despacho y vistas
    // -------------------------------------------------------------------------

    public function test_play_redirects_matching_to_its_race_route(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupRace('matching');

        $this->actingAs($s1)
            ->get(route('student.game-sessions.play', $session->id))
            ->assertRedirect(route('student.matching.play-session', $session->id));
    }

    public function test_play_redirects_wordsearch_to_its_race_route(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupRace('word_search');

        $this->actingAs($s1)
            ->get(route('student.game-sessions.play', $session->id))
            ->assertRedirect(route('student.wordsearch.play-session', $session->id));
    }

    public function test_play_redirects_crossword_to_its_race_route(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupRace('crossword');

        $this->actingAs($s1)
            ->get(route('student.game-sessions.play', $session->id))
            ->assertRedirect(route('student.crossword.play-session', $session->id));
    }

    public function test_matching_race_page_is_available_for_participants(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupRace('matching');

        $this->actingAs($s1)
            ->get(route('student.matching.play-session', $session->id))
            ->assertOk()
            ->assertViewIs('student.matching.multiplayer')
            ->assertViewHas('session', fn ($value) => $value->id === $session->id);
    }

    public function test_wordsearch_race_page_is_available_for_participants(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupRace('word_search');

        $this->actingAs($s1)
            ->get(route('student.wordsearch.play-session', $session->id))
            ->assertOk()
            ->assertViewIs('student.wordsearch.multiplayer')
            ->assertViewHas('session', fn ($value) => $value->id === $session->id);
    }

    public function test_crossword_race_page_is_available_for_participants(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupRace('crossword');

        $this->actingAs($s1)
            ->get(route('student.crossword.play-session', $session->id))
            ->assertOk()
            ->assertViewIs('student.crossword.multiplayer')
            ->assertViewHas('session', fn ($value) => $value->id === $session->id);
    }

    public function test_non_participant_cannot_open_the_race(): void
    {
        ['session' => $session, 'class' => $class] = $this->setupRace('matching');

        $outsider = $this->student('outsider@example.com');
        $this->enroll($class, $outsider);

        $this->actingAs($outsider)
            ->get(route('student.matching.play-session', $session->id))
            ->assertForbidden();
    }

    public function test_teacher_cannot_spin_a_race_session(): void
    {
        ['teacher' => $teacher, 'session' => $session] = $this->setupRace('matching');

        $this->actingAs($teacher)
            ->postJson(route('teacher.game-sessions.spin', $session->id), ['seconds' => 30])
            ->assertStatus(422)
            ->assertJsonPath(
                'error',
                'Este juego se juega en carrera libre: no hay rondas que avanzar.'
            );
    }

    public function test_teacher_can_view_the_race_room(): void
    {
        ['teacher' => $teacher, 'session' => $session] = $this->setupRace('matching');

        $this->actingAs($teacher)
            ->get(route('teacher.game-sessions.show', $session->id))
            ->assertOk()
            ->assertViewIs('teacher.game_sessions.show');
    }

    // -------------------------------------------------------------------------
    // Respuestas de los jugadores actualizan la participación de la sala
    // -------------------------------------------------------------------------

    public function test_matching_answer_updates_the_session_participation(): void
    {
        ['s1' => $s1, 'activity' => $activity, 'session' => $session] = $this->setupRace('matching');

        $matching = $activity->matching;
        $item = $matching->items()->where('left_text', 'HTTP')->firstOrFail();

        $participation = $this->participationOf($session, $s1);

        $this->actingAs($s1)
            ->postJson(route('student.matching.answer'), [
                'matching_id' => $matching->id,
                'matching_item_id' => $item->id,
                'response' => $item->right_text,
            ])
            ->assertOk()
            ->assertJsonPath('correct', true);

        $participation->refresh();

        $this->assertSame($session->id, $participation->game_session_id);
        $this->assertGreaterThan(0, $participation->score);
    }

    public function test_wordsearch_answer_updates_the_session_participation(): void
    {
        ['s1' => $s1, 'activity' => $activity, 'session' => $session] = $this->setupRace('word_search');

        $wordsearch = $activity->wordsearch;
        $word = $wordsearch->words()->firstOrFail();

        [$startRow, $startColumn, $endRow, $endColumn] = $this->wordSelection($word);

        $participation = $this->participationOf($session, $s1);

        $this->actingAs($s1)
            ->postJson(route('student.wordsearch.answer'), [
                'wordsearch_id' => $wordsearch->id,
                'start_row' => $startRow,
                'start_column' => $startColumn,
                'end_row' => $endRow,
                'end_column' => $endColumn,
            ])
            ->assertOk()
            ->assertJsonPath('correct', true);

        $participation->refresh();

        $this->assertSame($session->id, $participation->game_session_id);
        $this->assertGreaterThan(0, $participation->score);
    }

    public function test_crossword_answer_updates_the_session_participation(): void
    {
        ['s1' => $s1, 'activity' => $activity, 'session' => $session] = $this->setupRace('crossword');

        $crossword = $activity->crossword;
        $word = $crossword->words()->firstOrFail();

        $participation = $this->participationOf($session, $s1);

        $this->actingAs($s1)
            ->postJson(route('student.crossword.answer'), [
                'participation_id' => $participation->id,
                'crossword_word_id' => $word->id,
                'response' => $word->word,
            ])
            ->assertOk()
            ->assertJsonPath('is_correct', true);

        $participation->refresh();

        $this->assertSame($session->id, $participation->game_session_id);
        $this->assertGreaterThan(0, $participation->score);
    }

    // -------------------------------------------------------------------------
    // Fin de la carrera
    // -------------------------------------------------------------------------

    public function test_race_finishes_when_all_players_complete(): void
    {
        ['s1' => $s1, 's2' => $s2, 'activity' => $activity, 'session' => $session] = $this->setupRace('matching');

        $matching = $activity->matching;

        foreach ($matching->items()->get() as $item) {
            $this->answerMatching($matching, $item, $s1)->assertOk();
            $this->answerMatching($matching, $item, $s2)->assertOk();
        }

        $this->assertSame(
            'completed',
            $this->participationOf($session, $s1)->status
        );

        $state = $this->state($session, $s1);

        $this->assertSame('finished', $state['status']);

        $session->refresh();

        $this->assertSame('finished', $session->status);
        $this->assertSame('draw', $session->result_type);
        $this->assertNull($session->winner_participation_id);
    }

    public function test_race_finishes_when_time_limit_expires_and_declares_winner(): void
    {
        ['s1' => $s1, 's2' => $s2, 'activity' => $activity, 'session' => $session] = $this->setupRace('matching', 60);

        $matching = $activity->matching;
        $item = $matching->items()->where('left_text', 'HTTP')->firstOrFail();

        $this->answerMatching($matching, $item, $s1)->assertOk();

        $this->travel(61)->seconds();

        $state = $this->state($session, $s1);

        $this->assertSame('finished', $state['status']);

        $session->refresh();

        $this->assertSame('finished', $session->status);
        $this->assertSame('winner', $session->result_type);
        $this->assertSame(
            $this->participationOf($session, $s1)->id,
            $session->winner_participation_id
        );

        $this->assertSame(0, $this->participationOf($session, $s2)->score);
        $this->assertGreaterThan(0, $this->participationOf($session, $s1)->score);
    }

    public function test_state_reports_players_and_remaining_time_for_a_race(): void
    {
        ['s1' => $s1, 'session' => $session] = $this->setupRace('matching', 90);

        $state = $this->state($session, $s1);

        $this->assertSame('playing', $state['status']);
        $this->assertCount(2, $state['players']);
        $this->assertSame(90, $state['time_remaining']);
    }

    // -------------------------------------------------------------------------
    // Utilidades
    // -------------------------------------------------------------------------

    private function answerMatching(
        Matching $matching,
        MatchingItem $item,
        User $student
    ): TestResponse {
        return $this->actingAs($student)
            ->postJson(route('student.matching.answer'), [
                'matching_id' => $matching->id,
                'matching_item_id' => $item->id,
                'response' => $item->right_text,
            ]);
    }

    /**
     * Calcula las coordenadas de inicio/fin de la selección que
     * representa una palabra ya colocada en la sopa de letras.
     *
     * @return array{0:int,1:int,2:int,3:int}
     */
    private function wordSelection(Word $word): array
    {
        $service = app(WordsearchService::class);
        [$dr, $dc] = $service->directionDelta($word->direction);
        $length = mb_strlen($word->word);

        $endRow = $word->row + $dr * ($length - 1);
        $endColumn = $word->column + $dc * ($length - 1);

        return [$word->row, $word->column, $endRow, $endColumn];
    }
}
