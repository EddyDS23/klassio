<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\Participation;
use App\Models\User;
use App\Models\WordsearchAnswer;
use App\Models\SchoolClass;
use App\Services\WordsearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class WordsearchTest extends TestCase
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
            'name' => 'Test Class',
            'description' => 'Wordsearch tests',
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
            'title' => 'Wordsearch Activity',
            'description' => 'Wordsearch test activity',
            'type' => 'word_search',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
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
            'status' => 'started',
            'score' => 0,
        ], $attributes));
    }

    private function createWordsearch(
        Activity $activity,
        array $words = [['word' => 'LARAVEL', 'score' => 10]]
    ) {
        return app(WordsearchService::class)->buildWordsearch(
            $activity,
            10,
            10,
            $words
        );
    }

    public function test_generate_creates_grid_and_places_words(): void
    {
        $service = app(WordsearchService::class);

        $result = $service->generate(10, 10, [
            ['word' => 'LARAVEL', 'score' => 10],
            ['word' => 'PHP', 'score' => 20],
        ]);

        $this->assertCount(10, $result['grid']);
        $this->assertCount(10, $result['grid'][0]);
        $this->assertCount(2, $result['placements']);
        $this->assertSame([], $result['failed']);

        foreach ($result['grid'] as $row) {
            foreach ($row as $cell) {
                $this->assertIsString($cell);
                $this->assertSame(1, mb_strlen($cell));
            }
        }
    }

    public function test_generate_rejects_invalid_grid_dimensions(): void
    {
        $service = app(WordsearchService::class);

        $this->expectException(\InvalidArgumentException::class);

        $service->generate(1, 10, [
            ['word' => 'PHP', 'score' => 10],
        ]);
    }

    public function test_generate_reports_word_that_is_too_long(): void
    {
        $service = app(WordsearchService::class);

        $result = $service->generate(5, 5, [
            ['word' => 'LARAVEL', 'score' => 10],
        ]);

        $this->assertSame([], $result['placements']);
        $this->assertCount(1, $result['failed']);
        $this->assertSame('LARAVEL', $result['failed'][0]['word']);
        $this->assertSame('too_long', $result['failed'][0]['reason']);
    }

    public function test_build_wordsearch_persists_grid_and_words(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher);

        $wordsearch = $this->createWordsearch($activity, [
            ['word' => 'LARAVEL', 'score' => 10],
            ['word' => 'PHP', 'score' => 20],
        ]);

        $this->assertDatabaseHas('worksearches', [
            'id' => $wordsearch->id,
            'activity_id' => $activity->id,
            'rows' => 10,
            'columns' => 10,
        ]);

        $this->assertCount(2, $wordsearch->words()->get());
        $this->assertSame(2, $wordsearch->words()->count());
        $this->assertSame(100, count($wordsearch->grid, COUNT_RECURSIVE) - count($wordsearch->grid));
    }

    public function test_build_wordsearch_replaces_previous_words(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher);

        $service = app(WordsearchService::class);

        $first = $service->buildWordsearch(
            $activity,
            10,
            10,
            [['word' => 'PHP', 'score' => 10]]
        );

        $second = $service->buildWordsearch(
            $activity,
            10,
            10,
            [['word' => 'LARAVEL', 'score' => 20]]
        );

        $this->assertSame($first->id, $second->id);
        $this->assertDatabaseMissing('words', [
            'wordsearch_id' => $second->id,
            'word' => 'PHP',
        ]);
        $this->assertDatabaseHas('words', [
            'wordsearch_id' => $second->id,
            'word' => 'LARAVEL',
            'score' => 20,
        ]);
    }

    public function test_student_can_open_wordsearch_play_with_active_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createWordsearch($activity);
        $participation = $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->get(route('student.wordsearch.play', $activity->id));

        $response->assertOk();
        $response->assertViewIs('student.wordsearch.play');
        $response->assertViewHas('participation', fn ($value) => $value->id === $participation->id);
        $response->assertViewHas('foundIds', []);
        $response->assertViewHas('foundCount', 0);
        $response->assertViewHas('earnedPoints', 0);
    }

    public function test_student_cannot_open_wordsearch_without_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createWordsearch($activity);

        $response = $this->actingAs($student)
            ->get(route('student.wordsearch.play', $activity->id));

        $response->assertNotFound();
    }

    public function test_correct_wordsearch_answer_creates_answer_and_updates_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $wordsearch = $this->createWordsearch($activity, [
            ['word' => 'PHP', 'score' => 20],
            ['word' => 'LARAVEL', 'score' => 30],
        ]);
        $participation = $this->createParticipation($activity, $student);

        $word = $wordsearch->words()->where('word', 'PHP')->firstOrFail();
        $length = mb_strlen($word->word);

        $service = app(WordsearchService::class);
        [$dr, $dc] = $service->directionDelta($word->direction);
        $endRow = $word->row + $dr * ($length - 1);
        $endColumn = $word->column + $dc * ($length - 1);

        $response = $this->actingAs($student)
            ->postJson(route('student.wordsearch.answer'), [
                'wordsearch_id' => $wordsearch->id,
                'start_row' => $word->row,
                'start_column' => $word->column,
                'end_row' => $endRow,
                'end_column' => $endColumn,
            ]);

        $response->assertOk();
        $response->assertJsonPath('correct', true);
        $response->assertJsonPath('already_found', false);
        $response->assertJsonPath('word', 'PHP');
        $response->assertJsonPath('score', 20);
        $response->assertJsonPath('total', 1);
        $response->assertJsonPath('total_words', 2);
        $response->assertJsonPath('finished', false);

        $this->assertDatabaseHas('wordsearch_answers', [
            'participation_id' => $participation->id,
            'word_id' => $word->id,
            'score' => 20,
        ]);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 40,
        ]);
    }

    public function test_incorrect_wordsearch_answer_does_not_create_answer(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $wordsearch = $this->createWordsearch($activity);
        $participation = $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->postJson(route('student.wordsearch.answer'), [
                'wordsearch_id' => $wordsearch->id,
                'start_row' => 0,
                'start_column' => 0,
                'end_row' => 1,
                'end_column' => 2,
            ]);

        $response->assertOk();
        $response->assertJsonPath('correct', false);
        $response->assertJsonPath('already_found', false);
        $response->assertJsonPath('score', 0);

        $this->assertSame(
            0,
            WordsearchAnswer::where('participation_id', $participation->id)->count()
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 0,
        ]);
    }

    public function test_same_word_cannot_be_scored_twice(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $wordsearch = $this->createWordsearch($activity, [
            ['word' => 'PHP', 'score' => 20],
            ['word' => 'LARAVEL', 'score' => 30],
        ]);
        $participation = $this->createParticipation($activity, $student);

        $word = $wordsearch->words()->where('word', 'PHP')->firstOrFail();
        $service = app(WordsearchService::class);
        $length = mb_strlen($word->word);
        [$dr, $dc] = $service->directionDelta($word->direction);

        $payload = [
            'wordsearch_id' => $wordsearch->id,
            'start_row' => $word->row,
            'start_column' => $word->column,
            'end_row' => $word->row + $dr * ($length - 1),
            'end_column' => $word->column + $dc * ($length - 1),
        ];

        $this->actingAs($student);

        $first = $this->postJson(route('student.wordsearch.answer'), $payload);
        $first->assertJsonPath('correct', true);
        $first->assertJsonPath('already_found', false);

        $second = $this->postJson(route('student.wordsearch.answer'), $payload);
        $second->assertJsonPath('correct', true);
        $second->assertJsonPath('already_found', true);
        $second->assertJsonPath('score', 0);

        $this->assertSame(
            1,
            WordsearchAnswer::where('participation_id', $participation->id)
                ->where('word_id', $word->id)
                ->count()
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 40,
        ]);
    }

    public function test_finding_last_word_finishes_participation_automatically(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $wordsearch = $this->createWordsearch($activity, [
            ['word' => 'PHP', 'score' => 10],
        ]);
        $participation = $this->createParticipation($activity, $student);

        $word = $wordsearch->words()->firstOrFail();
        $service = app(WordsearchService::class);
        $length = mb_strlen($word->word);
        [$dr, $dc] = $service->directionDelta($word->direction);

        $response = $this->actingAs($student)
            ->postJson(route('student.wordsearch.answer'), [
                'wordsearch_id' => $wordsearch->id,
                'start_row' => $word->row,
                'start_column' => $word->column,
                'end_row' => $word->row + $dr * ($length - 1),
                'end_column' => $word->column + $dc * ($length - 1),
            ]);

        $response->assertOk();
        $response->assertJsonPath('correct', true);
        $response->assertJsonPath('finished', true);
        $response->assertJsonPath('total', 1);
        $response->assertJsonPath('total_words', 1);
        $response->assertJsonPath('redirect', route(
            'student.participation.result',
            $activity->id
        ));

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'completed',
            'score' => 100,
        ]);

        $this->assertNotNull(
            Participation::find($participation->id)->completed_at
        );
    }

    public function test_wordsearch_rejects_non_linear_selection(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $wordsearch = $this->createWordsearch($activity);
        $participation = $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->postJson(route('student.wordsearch.answer'), [
                'wordsearch_id' => $wordsearch->id,
                'start_row' => 0,
                'start_column' => 0,
                'end_row' => 1,
                'end_column' => 2,
            ]);

        $response->assertOk();
        $response->assertJsonPath('correct', false);
        $response->assertJsonPath(
            'error',
            'Debes seleccionar celdas en línea recta (horizontal, vertical o diagonal).'
        );

        $this->assertDatabaseMissing('wordsearch_answers', [
            'participation_id' => $participation->id,
        ]);
    }

    public function test_expired_participation_redirects_from_wordsearch_play(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'time_limit' => 60,
        ]);
        $this->createWordsearch($activity);

        $participation = $this->createParticipation($activity, $student);
        $participation->started_at = Carbon::now()->subSeconds(120);
        $participation->save();

        $response = $this->actingAs($student)
            ->get(route('student.wordsearch.play', $activity->id));

        $response->assertRedirect(
            route('student.participation.result', $activity->id)
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'expired',
        ]);
    }

    public function test_wordsearch_answer_requires_valid_payload(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $wordsearch = $this->createWordsearch($activity);

        $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->postJson(route('student.wordsearch.answer'), [
                'wordsearch_id' => $wordsearch->id,
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'start_row',
            'start_column',
            'end_row',
            'end_column',
        ]);
    }
}
