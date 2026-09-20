<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Crossword;
use App\Models\CrosswordAnswer;
use App\Models\CrosswordWord;
use App\Models\Enrollment;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\CrosswordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CrosswordTest extends TestCase
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
            'description' => 'Crossword tests',
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
            'title' => 'Crossword Activity',
            'description' => 'Crossword test activity',
            'type' => 'crossword',
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

    private function createCrossword(
        Activity $activity,
        array $words = [
            ['word' => 'LARAVEL', 'clue' => 'Framework PHP', 'score' => 10],
            ['word' => 'VALUE', 'clue' => 'Valor en inglés', 'score' => 20],
        ]
    ): Crossword {
        $crossword = Crossword::create([
            'activity_id' => $activity->id,
            'rows' => 0,
            'columns' => 0,
            'grid' => [],
        ]);

        return app(CrosswordService::class)->store($activity, [
            'words' => $words,
        ])['crossword'];
    }

    public function test_validate_answer_is_case_insensitive_and_trims_spaces(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher);
        $crossword = $this->createCrossword($activity);

        $word = $crossword->words()->where('word', 'laravel')->firstOrFail();

        $service = app(CrosswordService::class);

        $this->assertTrue($service->validateAnswer($word, ' LARAVEL '));
        $this->assertTrue($service->validateAnswer($word, 'LaRaVeL'));
        $this->assertFalse($service->validateAnswer($word, 'PHP'));
    }

    public function test_store_creates_grid_and_persists_placed_words(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher);

        $crossword = Crossword::create([
            'activity_id' => $activity->id,
            'rows' => 0,
            'columns' => 0,
            'grid' => [],
        ]);

        $result = app(CrosswordService::class)->store($activity, [
            'words' => [
                ['word' => 'LARAVEL', 'clue' => 'Framework PHP', 'score' => 10],
                ['word' => 'VALUE', 'clue' => 'Valor en inglés', 'score' => 20],
            ],
        ]);

        $crossword = $result['crossword']->fresh();

        $this->assertSame($crossword->id, $result['crossword']->id);
        $this->assertNotEmpty($crossword->grid);
        $this->assertGreaterThan(0, $crossword->rows);
        $this->assertGreaterThan(0, $crossword->columns);

        $this->assertDatabaseHas('crosswords', [
            'id' => $crossword->id,
            'activity_id' => $activity->id,
            'rows' => $crossword->rows,
            'columns' => $crossword->columns,
        ]);

        $this->assertCount(2, $crossword->words);
        $this->assertSame(['laravel', 'value'], $crossword->words()
            ->orderBy('id')
            ->pluck('word')
            ->all());
    }

    public function test_update_replaces_previous_crossword_words_and_grid(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher);

        $crossword = $this->createCrossword($activity, [
            ['word' => 'LARAVEL', 'clue' => 'Framework PHP', 'score' => 10],
            ['word' => 'VALUE', 'clue' => 'Valor en inglés', 'score' => 20],
        ]);

        $oldGrid = $crossword->grid;

        $result = app(CrosswordService::class)->update($crossword, [
            'words' => [
                ['word' => 'PHP', 'clue' => 'Lenguaje', 'score' => 30],
                ['word' => 'HYPER', 'clue' => 'Palabra de prueba', 'score' => 40],
            ],
        ]);

        $updated = $result['crossword']->fresh();

        $this->assertSame($crossword->id, $updated->id);
        $this->assertNotSame($oldGrid, $updated->grid);

        $this->assertDatabaseMissing('crossword_words', [
            'crossword_id' => $crossword->id,
            'word' => 'laravel',
        ]);

        $this->assertDatabaseMissing('crossword_words', [
            'crossword_id' => $crossword->id,
            'word' => 'value',
        ]);

        $this->assertDatabaseHas('crossword_words', [
            'crossword_id' => $crossword->id,
            'word' => 'php',
            'score' => 30,
        ]);

        $this->assertDatabaseHas('crossword_words', [
            'crossword_id' => $crossword->id,
            'word' => 'hyper',
            'score' => 40,
        ]);
    }

    public function test_teacher_can_open_crossword_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher, ['status' => 'draft']);

        Crossword::create([
            'activity_id' => $activity->id,
            'rows' => 0,
            'columns' => 0,
            'grid' => [],
        ]);

        $response = $this->actingAs($teacher)
            ->get(route('teacher.crossword.configure', $activity->id));

        $response->assertOk();
        $response->assertViewIs('teacher.crossword.configure');
        $response->assertViewHas('activity', fn ($value) => $value->id === $activity->id);
    }

    public function test_teacher_can_store_crossword_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher, ['status' => 'draft']);

        Crossword::create([
            'activity_id' => $activity->id,
            'rows' => 0,
            'columns' => 0,
            'grid' => [],
        ]);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.crossword.store', $activity->id), [
                'words' => [
                    ['word' => 'LARAVEL', 'clue' => 'Framework PHP', 'score' => 10],
                    ['word' => 'VALUE', 'clue' => 'Valor en inglés', 'score' => 20],
                ],
            ]);

        $response->assertRedirect(route('teacher.activities.show', $activity->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('crossword_words', 2);
        $this->assertDatabaseHas('crosswords', [
            'activity_id' => $activity->id,
        ]);
    }

    public function test_teacher_can_update_crossword_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher, ['status' => 'draft']);
        $this->createCrossword($activity);

        $response = $this->actingAs($teacher)
            ->put(route('teacher.crossword.update', $activity->id), [
                'words' => [
                    ['word' => 'PHP', 'clue' => 'Lenguaje', 'score' => 30],
                    ['word' => 'HYPER', 'clue' => 'Palabra de prueba', 'score' => 40],
                ],
            ]);

        $response->assertRedirect(route('teacher.activities.show', $activity->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('crossword_words', [
            'word' => 'laravel',
        ]);

        $this->assertDatabaseHas('crossword_words', [
            'word' => 'php',
            'score' => 30,
        ]);
    }

    public function test_student_can_open_crossword_play_with_active_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $crossword = $this->createCrossword($activity);
        $participation = $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->get(route('student.crossword.play', $activity->id));

        $response->assertOk();
        $response->assertViewIs('student.crossword.play');
        $response->assertViewHas('participation', fn ($value) => $value->id === $participation->id);
        $response->assertViewHas('crossword', fn ($value) => $value->id === $crossword->id);
        $response->assertViewHas('words');
        $response->assertViewHas('remainingSeconds', null);
    }

    public function test_student_cannot_open_crossword_without_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createCrossword($activity);

        $response = $this->actingAs($student)
            ->get(route('student.crossword.play', $activity->id));

        $response->assertNotFound();
    }

    public function test_correct_crossword_answer_creates_answer_and_updates_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'max_score' => 100,
        ]);
        $crossword = $this->createCrossword($activity, [
            ['word' => 'LARAVEL', 'clue' => 'Framework PHP', 'score' => 10],
            ['word' => 'VALUE', 'clue' => 'Valor en inglés', 'score' => 20],
        ]);
        $participation = $this->createParticipation($activity, $student);

        $word = $crossword->words()->where('word', 'laravel')->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(route('student.crossword.answer'), [
                'participation_id' => $participation->id,
                'crossword_word_id' => $word->id,
                'response' => ' LARAVEL ',
            ]);

        $response->assertOk();
        $response->assertJsonPath('is_correct', true);
        $response->assertJsonPath('score', 10);
        $response->assertJsonPath('correct_word', null);
        $response->assertJsonPath('completed', false);

        $this->assertDatabaseHas('crossword_answers', [
            'participation_id' => $participation->id,
            'crossword_word_id' => $word->id,
            'response' => 'LARAVEL',
            'is_correct' => 1,
            'score' => 10,
        ]);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 33,
        ]);
    }

    public function test_incorrect_crossword_answer_creates_zero_score_answer_and_returns_correct_word(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'max_score' => 100,
        ]);
        $crossword = $this->createCrossword($activity, [
            ['word' => 'LARAVEL', 'clue' => 'Framework PHP', 'score' => 10],
            ['word' => 'VALUE', 'clue' => 'Valor en inglés', 'score' => 20],
        ]);
        $participation = $this->createParticipation($activity, $student);

        $word = $crossword->words()->where('word', 'laravel')->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(route('student.crossword.answer'), [
                'participation_id' => $participation->id,
                'crossword_word_id' => $word->id,
                'response' => 'PHP',
            ]);

        $response->assertOk();
        $response->assertJsonPath('is_correct', false);
        $response->assertJsonPath('score', 0);
        $response->assertJsonPath('correct_word', 'laravel');
        $response->assertJsonPath('completed', false);

        $this->assertDatabaseHas('crossword_answers', [
            'participation_id' => $participation->id,
            'crossword_word_id' => $word->id,
            'is_correct' => 0,
            'score' => 0,
        ]);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 0,
        ]);
    }

    public function test_same_crossword_word_cannot_be_answered_twice(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'max_score' => 100,
        ]);
        $crossword = $this->createCrossword($activity);
        $participation = $this->createParticipation($activity, $student);
        $word = $crossword->words()->where('word', 'laravel')->firstOrFail();

        $payload = [
            'participation_id' => $participation->id,
            'crossword_word_id' => $word->id,
            'response' => 'LARAVEL',
        ];

        $this->actingAs($student);

        $first = $this->postJson(route('student.crossword.answer'), $payload);
        $first->assertOk();
        $first->assertJsonPath('is_correct', true);

        $second = $this->postJson(route('student.crossword.answer'), $payload);
        $second->assertStatus(409);
        $second->assertJsonPath('error', 'Esta palabra ya fue respondida.');

        $this->assertSame(
            1,
            CrosswordAnswer::where('participation_id', $participation->id)
                ->where('crossword_word_id', $word->id)
                ->count()
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 33,
        ]);
    }

    public function test_answering_last_crossword_word_finishes_participation_automatically(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'max_score' => 100,
        ]);

        $crossword = $this->createCrossword($activity, [
            ['word' => 'PHP', 'clue' => 'Lenguaje', 'score' => 10],
            ['word' => 'HYPER', 'clue' => 'Palabra de prueba', 'score' => 20],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $words = $crossword->words()->get();

        $first = $this->actingAs($student)->postJson(
            route('student.crossword.answer'),
            [
                'participation_id' => $participation->id,
                'crossword_word_id' => $words[0]->id,
                'response' => $words[0]->word,
            ]
        );

        $first->assertOk();
        $first->assertJsonPath('completed', false);

        $second = $this->postJson(
            route('student.crossword.answer'),
            [
                'participation_id' => $participation->id,
                'crossword_word_id' => $words[1]->id,
                'response' => $words[1]->word,
            ]
        );

        $second->assertOk();
        $second->assertJsonPath('completed', true);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'completed',
            'score' => 100,
        ]);

        $this->assertNotNull(
            Participation::find($participation->id)->completed_at
        );
    }

    public function test_answer_cannot_be_submitted_for_another_student_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $otherStudent = $this->student('other@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);
        $this->enroll($class, $otherStudent);

        $activity = $this->createActivity($class, $teacher);
        $crossword = $this->createCrossword($activity);
        $participation = $this->createParticipation($activity, $student);
        $word = $crossword->words()->firstOrFail();

        $response = $this->actingAs($otherStudent)
            ->postJson(route('student.crossword.answer'), [
                'participation_id' => $participation->id,
                'crossword_word_id' => $word->id,
                'response' => $word->word,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('crossword_answers', [
            'participation_id' => $participation->id,
        ]);
    }

    public function test_answer_cannot_use_word_from_another_activity(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $otherActivity = $this->createActivity($class, $teacher, [
            'title' => 'Other Crossword',
        ]);

        $crossword = $this->createCrossword($activity);
        $otherCrossword = $this->createCrossword($otherActivity);
        $participation = $this->createParticipation($activity, $student);
        $otherWord = $otherCrossword->words()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(route('student.crossword.answer'), [
                'participation_id' => $participation->id,
                'crossword_word_id' => $otherWord->id,
                'response' => $otherWord->word,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('crossword_answers', [
            'participation_id' => $participation->id,
        ]);
    }

    public function test_answer_requires_valid_payload(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $crossword = $this->createCrossword($activity);
        $participation = $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->postJson(route('student.crossword.answer'), [
                'participation_id' => $participation->id,
                'crossword_word_id' => $crossword->words()->first()->id,
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['response']);
    }

    public function test_expired_participation_redirects_from_crossword_play(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'time_limit' => 60,
        ]);
        $this->createCrossword($activity);

        $participation = $this->createParticipation($activity, $student);
        $participation->started_at = Carbon::now()->subSeconds(120);
        $participation->save();

        $response = $this->actingAs($student)
            ->get(route('student.crossword.play', $activity->id));

        $response->assertRedirect(
            route('student.participation.result', $activity->id)
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'expired',
        ]);
    }
}
