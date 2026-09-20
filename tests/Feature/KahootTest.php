<?php
namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\KahootAnswer;
use App\Models\Option;
use App\Models\Participation;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\KahootService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class KahootTest extends TestCase
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
            'description' => 'Kahoot tests',
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
            'title' => 'Kahoot Activity',
            'description' => 'Kahoot test activity',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 1,
            'due_at' => null,
            'status' => 'published',
        ], $attributes));
    }

    private function createDraftActivity(
        SchoolClass $class,
        User $teacher,
        array $attributes = []
    ): Activity {
        return $this->createActivity(
            $class,
            $teacher,
            array_merge([
                'status' => 'draft',
            ], $attributes)
        );
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

    private function createKahoot(
        Activity $activity,
        array $questions = []
    ) {
        $kahoot = $activity->kahoot()->create();

        if ($questions === []) {
            $questions = [
                [
                    'question' => '¿Qué lenguaje usa Laravel?',
                    'time_limit' => 30,
                    'score' => 10,
                    'options' => [
                        ['text' => 'PHP', 'is_correct' => '1'],
                        ['text' => 'Python', 'is_correct' => '0'],
                        ['text' => 'Java', 'is_correct' => '0'],
                        ['text' => 'C#', 'is_correct' => '0'],
                    ],
                ],
            ];
        }

        app(KahootService::class)->store($kahoot, [
            'questions' => $questions,
        ]);

        return $kahoot->fresh();
    }

    private function questionsPayload(array $questions): array
    {
        return [
            'questions' => $questions,
        ];
    }

    public function test_store_kahoot_creates_questions_and_options(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $kahoot = $activity->kahoot()->create();

        app(KahootService::class)->store($kahoot, $this->questionsPayload([
            [
                'question' => '¿Qué lenguaje usa Laravel?',
                'time_limit' => 30,
                'score' => 10,
                'options' => [
                    ['text' => 'PHP', 'is_correct' => '1'],
                    ['text' => 'Python', 'is_correct' => '0'],
                    ['text' => 'Java', 'is_correct' => '0'],
                ],
            ],
            [
                'question' => '¿Qué herramienta administra dependencias PHP?',
                'time_limit' => 20,
                'score' => 20,
                'options' => [
                    ['text' => 'Composer', 'is_correct' => '1'],
                    ['text' => 'NPM', 'is_correct' => '0'],
                ],
            ],
        ]));

        $this->assertDatabaseHas('kahoots', [
            'id' => $kahoot->id,
            'activity_id' => $activity->id,
        ]);

        $this->assertDatabaseHas('questions', [
            'kahoot_id' => $kahoot->id,
            'question' => '¿Qué lenguaje usa Laravel?',
            'position' => 1,
            'time_limit' => 30,
            'score' => 10,
        ]);

        $this->assertDatabaseHas('questions', [
            'kahoot_id' => $kahoot->id,
            'question' => '¿Qué herramienta administra dependencias PHP?',
            'position' => 2,
            'time_limit' => 20,
            'score' => 20,
        ]);

        $this->assertSame(2, $kahoot->questions()->count());
        $this->assertSame(5, Option::whereIn(
            'question_id',
            $kahoot->questions()->pluck('id')
        )->count());
    }

    public function test_store_kahoot_assigns_question_and_option_positions(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $kahoot = $activity->kahoot()->create();

        app(KahootService::class)->store($kahoot, $this->questionsPayload([
            [
                'question' => 'Pregunta 1',
                'time_limit' => 10,
                'score' => 5,
                'options' => [
                    ['text' => 'A', 'is_correct' => '1'],
                    ['text' => 'B', 'is_correct' => '0'],
                ],
            ],
            [
                'question' => 'Pregunta 2',
                'time_limit' => 15,
                'score' => 10,
                'options' => [
                    ['text' => 'C', 'is_correct' => '1'],
                    ['text' => 'D', 'is_correct' => '0'],
                    ['text' => 'E', 'is_correct' => '0'],
                ],
            ],
        ]));

        $questions = $kahoot->questions()
            ->orderBy('position')
            ->get();

        $this->assertSame(1, $questions[0]->position);
        $this->assertSame(2, $questions[1]->position);

        $this->assertSame(
            [1, 2],
            $questions[0]->options()
                ->orderBy('position')
                ->pluck('position')
                ->all()
        );

        $this->assertSame(
            [1, 2, 3],
            $questions[1]->options()
                ->orderBy('position')
                ->pluck('position')
                ->all()
        );
    }

    public function test_update_kahoot_replaces_previous_questions_and_options(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $kahoot = $this->createKahoot($activity, [
            [
                'question' => 'Pregunta antigua',
                'time_limit' => 20,
                'score' => 10,
                'options' => [
                    ['text' => 'Correcta antigua', 'is_correct' => '1'],
                    ['text' => 'Incorrecta antigua', 'is_correct' => '0'],
                ],
            ],
        ]);

        $oldQuestion = $kahoot->questions()->firstOrFail();

        app(KahootService::class)->update($kahoot, $this->questionsPayload([
            [
                'question' => 'Pregunta nueva',
                'time_limit' => 30,
                'score' => 25,
                'options' => [
                    ['text' => 'Correcta nueva', 'is_correct' => '1'],
                    ['text' => 'Incorrecta nueva', 'is_correct' => '0'],
                    ['text' => 'Otra incorrecta', 'is_correct' => '0'],
                ],
            ],
        ]));

        $this->assertDatabaseMissing('questions', [
            'id' => $oldQuestion->id,
        ]);

        $this->assertDatabaseHas('questions', [
            'kahoot_id' => $kahoot->id,
            'question' => 'Pregunta nueva',
            'score' => 25,
        ]);

        $newQuestion = $kahoot->fresh()->questions()->firstOrFail();

        $this->assertSame(3, $newQuestion->options()->count());
        $this->assertDatabaseMissing('options', [
            'question_id' => $newQuestion->id,
            'text' => 'Correcta antigua',
        ]);
    }

    public function test_teacher_can_open_kahoot_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

         $activity->kahoot()->create();

        $response = $this->actingAs($teacher)
            ->get(route('teacher.kahoot.configure', $activity->id));

        $response->assertOk();
        $response->assertViewIs('teacher.kahoot.configure');
        $response->assertViewHas('activity', fn ($value) =>
            $value->id === $activity->id
        );
    }

    public function test_other_teacher_cannot_open_kahoot_configuration(): void
    {
        $teacher = $this->teacher();
        $otherTeacher = $this->teacher('other@example.com');

        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($otherTeacher)
            ->get(route('teacher.kahoot.configure', $activity->id));

        $response->assertForbidden();
    }

    public function test_teacher_can_store_kahoot_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

         $activity->kahoot()->create();

        $response = $this->actingAs($teacher)
            ->post(route('teacher.kahoot.store', $activity->id), [
                'questions' => [
                    [
                        'question' => '¿Qué es Laravel?',
                        'time_limit' => 30,
                        'score' => 10,
                        'options' => [
                            ['text' => 'Framework PHP', 'is_correct' => '1'],
                            ['text' => 'Base de datos', 'is_correct' => '0'],
                        ],
                    ],
                ],
            ]);

        $response->assertRedirect(
            route('teacher.activities.show', $activity->id)
        );

        $this->assertDatabaseHas('questions', [
            'kahoot_id' => $activity->kahoot->id,
            'question' => '¿Qué es Laravel?',
        ]);
    }

    public function test_teacher_can_open_kahoot_edit_after_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $this->createKahoot($activity);

        $response = $this->actingAs($teacher)
            ->get(route('teacher.kahoot.edit', $activity->id));

        $response->assertOk();
        $response->assertViewIs('teacher.kahoot.configure');
        $response->assertViewHas('questions');
    }

    public function test_configure_redirects_to_edit_when_kahoot_already_has_questions(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $this->createKahoot($activity);

        $response = $this->actingAs($teacher)
            ->get(route('teacher.kahoot.configure', $activity->id));

        $response->assertRedirect(
            route('teacher.kahoot.edit', $activity->id)
        );
    }

    public function test_teacher_can_update_kahoot_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $this->createKahoot($activity);

        $response = $this->actingAs($teacher)
            ->put(route('teacher.kahoot.update', $activity->id), [
                'questions' => [
                    [
                        'question' => 'Pregunta actualizada',
                        'time_limit' => 45,
                        'score' => 25,
                        'options' => [
                            ['text' => 'Correcta', 'is_correct' => '1'],
                            ['text' => 'Incorrecta', 'is_correct' => '0'],
                        ],
                    ],
                ],
            ]);

        $response->assertRedirect(
            route('teacher.activities.show', $activity->id)
        );

        $this->assertDatabaseHas('questions', [
            'kahoot_id' => $activity->kahoot->id,
            'question' => 'Pregunta actualizada',
            'time_limit' => 45,
            'score' => 25,
        ]);
    }

    public function test_teacher_cannot_store_kahoot_twice(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $this->createKahoot($activity);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.kahoot.store', $activity->id), [
                'questions' => [
                    [
                        'question' => 'Segunda configuración',
                        'time_limit' => 20,
                        'score' => 10,
                        'options' => [
                            ['text' => 'A', 'is_correct' => '1'],
                            ['text' => 'B', 'is_correct' => '0'],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(409);

        $this->assertDatabaseMissing('questions', [
            'kahoot_id' => $activity->kahoot->id,
            'question' => 'Segunda configuración',
        ]);
    }

    public function test_kahoot_requires_exactly_one_correct_option_per_question(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.kahoot.store', $activity->id), [
                'questions' => [
                    [
                        'question' => 'Pregunta inválida',
                        'time_limit' => 20,
                        'score' => 10,
                        'options' => [
                            ['text' => 'A', 'is_correct' => '0'],
                            ['text' => 'B', 'is_correct' => '0'],
                        ],
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('questions.0.options');

        $this->assertDatabaseCount('questions', 0);
    }

    public function test_kahoot_rejects_more_than_one_correct_option(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.kahoot.store', $activity->id), [
                'questions' => [
                    [
                        'question' => 'Pregunta inválida',
                        'time_limit' => 20,
                        'score' => 10,
                        'options' => [
                            ['text' => 'A', 'is_correct' => '1'],
                            ['text' => 'B', 'is_correct' => '1'],
                            ['text' => 'C', 'is_correct' => '0'],
                        ],
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('questions.0.options');

        $this->assertDatabaseCount('questions', 0);
    }

    public function test_kahoot_validates_question_time_limit(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.kahoot.store', $activity->id), [
                'questions' => [
                    [
                        'question' => 'Pregunta',
                        'time_limit' => 4,
                        'score' => 10,
                        'options' => [
                            ['text' => 'A', 'is_correct' => '1'],
                            ['text' => 'B', 'is_correct' => '0'],
                        ],
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('questions.0.time_limit');
    }

    public function test_kahoot_validates_score(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.kahoot.store', $activity->id), [
                'questions' => [
                    [
                        'question' => 'Pregunta',
                        'time_limit' => 20,
                        'score' => 0,
                        'options' => [
                            ['text' => 'A', 'is_correct' => '1'],
                            ['text' => 'B', 'is_correct' => '0'],
                        ],
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('questions.0.score');
    }

    public function test_kahoot_requires_at_least_two_options(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.kahoot.store', $activity->id), [
                'questions' => [
                    [
                        'question' => 'Pregunta',
                        'time_limit' => 20,
                        'score' => 10,
                        'options' => [
                            ['text' => 'Única opción', 'is_correct' => '1'],
                        ],
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('questions.0.options');
    }

    public function test_student_can_open_kahoot_play_with_active_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createKahoot($activity);
        $participation = $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->get(route('student.kahoot.play', $activity->id));

        $response->assertOk();
        $response->assertViewIs('student.kahoot.play');
        $response->assertViewHas(
            'participation',
            fn ($value) => $value->id === $participation->id
        );
        $response->assertViewHas('answeredIds', []);
        $response->assertViewHas('remainingSeconds', null);
    }

    public function test_student_cannot_open_kahoot_without_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createKahoot($activity);

        $response = $this->actingAs($student)
            ->get(route('student.kahoot.play', $activity->id));

        $response->assertNotFound();
    }

    public function test_unpublished_kahoot_cannot_be_played(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createDraftActivity($class, $teacher);
        $this->createKahoot($activity);
        $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->get(route('student.kahoot.play', $activity->id));

        $response->assertForbidden();
    }

    public function test_kahoot_play_does_not_expose_correct_option(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $kahoot = $this->createKahoot($activity);
        $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->get(route('student.kahoot.play', $activity->id));

        $response->assertOk();

        $response->assertViewHas('questions', function ($questions) {
            return isset($questions[0])
                && isset($questions[0]['options'])
                && ! array_key_exists('is_correct', $questions[0]['options'][0]);
        });
    }

    public function test_get_questions_for_play_returns_questions_in_position_order(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $kahoot = $this->createKahoot($activity, [
            [
                'question' => 'Primera',
                'time_limit' => 20,
                'score' => 10,
                'options' => [
                    ['text' => 'A', 'is_correct' => '1'],
                    ['text' => 'B', 'is_correct' => '0'],
                ],
            ],
            [
                'question' => 'Segunda',
                'time_limit' => 30,
                'score' => 20,
                'options' => [
                    ['text' => 'C', 'is_correct' => '1'],
                    ['text' => 'D', 'is_correct' => '0'],
                ],
            ],
        ]);

        $result = app(KahootService::class)->getQuestionsForPlay($kahoot);

        $this->assertCount(2, $result);
        $this->assertSame('Primera', $result[0]['question']);
        $this->assertSame(1, $result[0]['position']);
        $this->assertSame('Segunda', $result[1]['question']);
        $this->assertSame(2, $result[1]['position']);

        foreach ($result as $question) {
            foreach ($question['options'] as $option) {
                $this->assertArrayNotHasKey('is_correct', $option);
            }
        }
    }

    public function test_validate_answer_returns_correct_option_and_score(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $kahoot = $this->createKahoot($activity);
        $question = $kahoot->questions()->firstOrFail();
        $correct = $question->options()
            ->where('is_correct', true)
            ->firstOrFail();

        $result = app(KahootService::class)
            ->validateAnswer($question, $correct->id);

        $this->assertTrue($result['is_correct']);
        $this->assertSame(10, $result['score']);
        $this->assertSame($correct->id, $result['correct_option_id']);
    }

    public function test_validate_answer_returns_zero_for_incorrect_option(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $kahoot = $this->createKahoot($activity);
        $question = $kahoot->questions()->firstOrFail();
        $incorrect = $question->options()
            ->where('is_correct', false)
            ->firstOrFail();

        $result = app(KahootService::class)
            ->validateAnswer($question, $incorrect->id);

        $this->assertFalse($result['is_correct']);
        $this->assertSame(0, $result['score']);
        $this->assertSame(
            $question->options()->where('is_correct', true)->firstOrFail()->id,
            $result['correct_option_id']
        );
    }

    public function test_validate_answer_rejects_option_from_another_question(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $kahoot = $this->createKahoot($activity, [
            [
                'question' => 'Pregunta 1',
                'time_limit' => 20,
                'score' => 10,
                'options' => [
                    ['text' => 'A', 'is_correct' => '1'],
                    ['text' => 'B', 'is_correct' => '0'],
                ],
            ],
            [
                'question' => 'Pregunta 2',
                'time_limit' => 20,
                'score' => 20,
                'options' => [
                    ['text' => 'C', 'is_correct' => '1'],
                    ['text' => 'D', 'is_correct' => '0'],
                ],
            ],
        ]);

        $questions = $kahoot->questions()->orderBy('position')->get();

        $optionFromSecondQuestion = $questions[1]
            ->options()
            ->firstOrFail();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        app(KahootService::class)->validateAnswer(
            $questions[0],
            $optionFromSecondQuestion->id
        );
    }

    public function test_correct_kahoot_answer_creates_answer_and_updates_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $kahoot = $this->createKahoot($activity, [
            [
                'question' => 'Pregunta 1',
                'time_limit' => 20,
                'score' => 10,
                'options' => [
                    ['text' => 'Correcta', 'is_correct' => '1'],
                    ['text' => 'Incorrecta', 'is_correct' => '0'],
                ],
            ],
            [
                'question' => 'Pregunta 2',
                'time_limit' => 20,
                'score' => 20,
                'options' => [
                    ['text' => 'Correcta 2', 'is_correct' => '1'],
                    ['text' => 'Incorrecta 2', 'is_correct' => '0'],
                ],
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $question = $kahoot->questions()->orderBy('position')->firstOrFail();
        $correct = $question->options()->where('is_correct', true)->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), [
                'participation_id' => $participation->id,
                'question_id' => $question->id,
                'option_id' => $correct->id,
            ]);

        $response->assertOk();
        $response->assertJsonPath('is_correct', true);
        $response->assertJsonPath('score', 10);
        $response->assertJsonPath('correct_option_id', $correct->id);
        $response->assertJsonPath('completed', false);

        $this->assertDatabaseHas('kahoot_answers', [
            'participation_id' => $participation->id,
            'question_id' => $question->id,
            'option_id' => $correct->id,
            'is_correct' => true,
            'score' => 10,
        ]);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 33,
            'status' => 'started',
        ]);
    }

    public function test_incorrect_kahoot_answer_creates_incorrect_answer_without_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $kahoot = $this->createKahoot($activity);

        $participation = $this->createParticipation($activity, $student);

        $question = $kahoot->questions()->firstOrFail();
        $incorrect = $question->options()
            ->where('is_correct', false)
            ->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), [
                'participation_id' => $participation->id,
                'question_id' => $question->id,
                'option_id' => $incorrect->id,
            ]);

        $response->assertOk();
        $response->assertJsonPath('is_correct', false);
        $response->assertJsonPath('score', 0);
        $response->assertJsonPath('correct_option_id', $question->options()->where('is_correct', true)->firstOrFail()->id);

        $this->assertDatabaseHas('kahoot_answers', [
            'participation_id' => $participation->id,
            'question_id' => $question->id,
            'option_id' => $incorrect->id,
            'is_correct' => false,
            'score' => 0,
        ]);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 0,
        ]);
    }

    public function test_same_kahoot_question_cannot_be_answered_twice(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $kahoot = $this->createKahoot($activity, [
            [
                'question' => 'Pregunta 1',
                'time_limit' => 20,
                'score' => 10,
                'options' => [
                    ['text' => 'A', 'is_correct' => '1'],
                    ['text' => 'B', 'is_correct' => '0'],
                ],
            ],
            [
                'question' => 'Pregunta 2',
                'time_limit' => 20,
                'score' => 20,
                'options' => [
                    ['text' => 'C', 'is_correct' => '1'],
                    ['text' => 'D', 'is_correct' => '0'],
                ],
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);
        $question = $kahoot->questions()->firstOrFail();
        $correct = $question->options()->where('is_correct', true)->firstOrFail();

        $payload = [
            'participation_id' => $participation->id,
            'question_id' => $question->id,
            'option_id' => $correct->id,
        ];

        $first = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), $payload);

        $first->assertOk();

        $second = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), $payload);

        $second->assertStatus(409);
        $second->assertJsonPath(
            'error',
            'Esta pregunta ya fue respondida.'
        );

        $this->assertSame(
            1,
            KahootAnswer::where('participation_id', $participation->id)
                ->where('question_id', $question->id)
                ->count()
        );
    }

    public function test_answering_question_from_another_kahoot_is_rejected(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $firstKahoot = $this->createKahoot($activity);

        $otherActivity = $this->createActivity(
            $class,
            $teacher,
            ['title' => 'Other Kahoot']
        );

        $secondKahoot = $this->createKahoot($otherActivity);

        $participation = $this->createParticipation($activity, $student);

        $questionFromOtherKahoot = $secondKahoot->questions()->firstOrFail();
        $optionFromOtherKahoot = $questionFromOtherKahoot
            ->options()
            ->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), [
                'participation_id' => $participation->id,
                'question_id' => $questionFromOtherKahoot->id,
                'option_id' => $optionFromOtherKahoot->id,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('kahoot_answers', [
            'participation_id' => $participation->id,
            'question_id' => $questionFromOtherKahoot->id,
        ]);
    }

    public function test_student_cannot_answer_another_students_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $otherStudent = $this->student('other-student@example.com');

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);
        $this->enroll($class, $otherStudent);

        $activity = $this->createActivity($class, $teacher);
        $kahoot = $this->createKahoot($activity);

        $participation = $this->createParticipation($activity, $student);

        $question = $kahoot->questions()->firstOrFail();
        $correct = $question->options()->where('is_correct', true)->firstOrFail();

        $response = $this->actingAs($otherStudent)
            ->postJson(route('student.kahoot.answer'), [
                'participation_id' => $participation->id,
                'question_id' => $question->id,
                'option_id' => $correct->id,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('kahoot_answers', [
            'participation_id' => $participation->id,
            'question_id' => $question->id,
        ]);
    }

    public function test_last_kahoot_answer_completes_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $kahoot = $this->createKahoot($activity, [
            [
                'question' => 'Pregunta 1',
                'time_limit' => 20,
                'score' => 10,
                'options' => [
                    ['text' => 'Correcta', 'is_correct' => '1'],
                    ['text' => 'Incorrecta', 'is_correct' => '0'],
                ],
            ],
            [
                'question' => 'Pregunta 2',
                'time_limit' => 20,
                'score' => 20,
                'options' => [
                    ['text' => 'Correcta 2', 'is_correct' => '1'],
                    ['text' => 'Incorrecta 2', 'is_correct' => '0'],
                ],
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $questions = $kahoot->questions()->orderBy('position')->get();

        $firstCorrect = $questions[0]
            ->options()
            ->where('is_correct', true)
            ->firstOrFail();

        $secondCorrect = $questions[1]
            ->options()
            ->where('is_correct', true)
            ->firstOrFail();

        $this->actingAs($student)->postJson(
            route('student.kahoot.answer'),
            [
                'participation_id' => $participation->id,
                'question_id' => $questions[0]->id,
                'option_id' => $firstCorrect->id,
            ]
        )->assertJsonPath('completed', false);

        $response = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), [
                'participation_id' => $participation->id,
                'question_id' => $questions[1]->id,
                'option_id' => $secondCorrect->id,
            ]);

        $response->assertOk();
        $response->assertJsonPath('is_correct', true);
        $response->assertJsonPath('score', 20);
        $response->assertJsonPath('completed', true);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'completed',
            'score' => 100,
        ]);

        $this->assertNotNull(
            Participation::find($participation->id)->completed_at
        );
    }

    public function test_answer_after_completed_participation_is_rejected(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $kahoot = $this->createKahoot($activity, [
            [
                'question' => 'Pregunta 1',
                'time_limit' => 20,
                'score' => 10,
                'options' => [
                    ['text' => 'Correcta', 'is_correct' => '1'],
                    ['text' => 'Incorrecta', 'is_correct' => '0'],
                ],
            ],
        ]);

        $participation = $this->createParticipation(
            $activity,
            $student,
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );

        $question = $kahoot->questions()->firstOrFail();
        $correct = $question->options()->where('is_correct', true)->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), [
                'participation_id' => $participation->id,
                'question_id' => $question->id,
                'option_id' => $correct->id,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('kahoot_answers', [
            'participation_id' => $participation->id,
            'question_id' => $question->id,
        ]);
    }

    public function test_kahoot_answer_requires_valid_payload(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createKahoot($activity);
        $participation = $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'participation_id',
            'question_id',
            'option_id',
        ]);
    }

    public function test_kahoot_answer_rejects_nonexistent_ids(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createKahoot($activity);

        $response = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), [
                'participation_id' => 999999,
                'question_id' => 999999,
                'option_id' => 999999,
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'participation_id',
            'question_id',
            'option_id',
        ]);
    }

    public function test_kahoot_play_requires_existing_kahoot(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->get(route('student.kahoot.play', $activity->id));

        $response->assertNotFound();
    }

    public function test_kahoot_play_requires_questions(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $activity->kahoot()->create();
        $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->get(route('student.kahoot.play', $activity->id));

        $response->assertNotFound();
    }

    public function test_expired_participation_redirects_from_kahoot_play(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'time_limit' => 60,
        ]);

        $this->createKahoot($activity);

        $participation = $this->createParticipation($activity, $student);

        $participation->started_at = Carbon::now()->subSeconds(120);
        $participation->save();

        $response = $this->actingAs($student)
            ->get(route('student.kahoot.play', $activity->id));

        $response->assertRedirect(
            route('student.participation.result', $activity->id)
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'expired',
        ]);
    }

    public function test_kahoot_answer_uses_activity_max_score_for_normalized_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, [
            'max_score' => 200,
        ]);

        $kahoot = $this->createKahoot($activity, [
            [
                'question' => 'Pregunta 1',
                'time_limit' => 20,
                'score' => 10,
                'options' => [
                    ['text' => 'Correcta', 'is_correct' => '1'],
                    ['text' => 'Incorrecta', 'is_correct' => '0'],
                ],
            ],
            [
                'question' => 'Pregunta 2',
                'time_limit' => 20,
                'score' => 30,
                'options' => [
                    ['text' => 'Correcta 2', 'is_correct' => '1'],
                    ['text' => 'Incorrecta 2', 'is_correct' => '0'],
                ],
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $question = $kahoot->questions()->firstOrFail();
        $correct = $question->options()->where('is_correct', true)->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), [
                'participation_id' => $participation->id,
                'question_id' => $question->id,
                'option_id' => $correct->id,
            ]);

        $response->assertOk();

        // 10 / 40 * 200 = 50
        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 50,
        ]);
    }

    public function test_kahoot_answer_does_not_trust_frontend_correctness(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $kahoot = $this->createKahoot($activity);
        $participation = $this->createParticipation($activity, $student);

        $question = $kahoot->questions()->firstOrFail();
        $incorrect = $question->options()
            ->where('is_correct', false)
            ->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(route('student.kahoot.answer'), [
                'participation_id' => $participation->id,
                'question_id' => $question->id,
                'option_id' => $incorrect->id,
                'is_correct' => true,
                'score' => 1000,
            ]);

        $response->assertOk();
        $response->assertJsonPath('is_correct', false);
        $response->assertJsonPath('score', 0);

        $this->assertDatabaseHas('kahoot_answers', [
            'participation_id' => $participation->id,
            'question_id' => $question->id,
            'option_id' => $incorrect->id,
            'is_correct' => false,
            'score' => 0,
        ]);
    }

    public function test_kahoot_service_returns_correct_answers_for_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $kahoot = $this->createKahoot($activity, [
            [
                'question' => 'Pregunta',
                'time_limit' => 20,
                'score' => 10,
                'options' => [
                    ['text' => 'Correcta', 'is_correct' => '1'],
                    ['text' => 'Incorrecta', 'is_correct' => '0'],
                ],
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $question = $kahoot->questions()->firstOrFail();
        $correct = $question->options()->where('is_correct', true)->firstOrFail();

        $this->actingAs($student)->postJson(
            route('student.kahoot.answer'),
            [
                'participation_id' => $participation->id,
                'question_id' => $question->id,
                'option_id' => $correct->id,
            ]
        )->assertOk();

        $result = app(\App\Services\ParticipationService::class)
            ->getResult($participation->fresh());

        $this->assertCount(1, $result['answers']);
        $this->assertSame('Pregunta', $result['answers'][0]['question']);
        $this->assertSame('Correcta', $result['answers'][0]['response']);
        $this->assertTrue($result['answers'][0]['is_correct']);
        $this->assertSame(10, $result['answers'][0]['score']);
    }
}
