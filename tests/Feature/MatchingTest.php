<?php
namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\Matching;
use App\Models\MatchingAnswer;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MatchingTest extends TestCase
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
            'description' => 'Matching tests',
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
            'title' => 'Matching Activity',
            'description' => 'Matching test activity',
            'type' => 'matching',
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

    private function createMatching(
        Activity $activity,
        array $items = [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo de transferencia',
                'score' => 10,
            ],
            [
                'left' => 'PHP',
                'right' => 'Lenguaje de programación',
                'score' => 20,
            ],
        ]
    ): Matching {
        return app(MatchingService::class)->buildMatching(
            $activity,
            $items
        );
    }

    public function test_build_matching_creates_matching_and_items(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $matching = $this->createMatching($activity, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo de transferencia',
                'score' => 10,
            ],
            [
                'left' => 'PHP',
                'right' => 'Lenguaje de programación',
                'score' => 20,
            ],
        ]);

        $this->assertDatabaseHas('matchings', [
            'id' => $matching->id,
            'activity_id' => $activity->id,
        ]);

        $this->assertDatabaseHas('matching_items', [
            'matching_id' => $matching->id,
            'left_text' => 'HTTP',
            'right_text' => 'Protocolo de transferencia',
            'score' => 10,
        ]);

        $this->assertDatabaseHas('matching_items', [
            'matching_id' => $matching->id,
            'left_text' => 'PHP',
            'right_text' => 'Lenguaje de programación',
            'score' => 20,
        ]);

        $this->assertSame(2, $matching->items()->count());
    }

    public function test_build_matching_normalizes_text_and_minimum_score(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $matching = $this->createMatching($activity, [
            [
                'left' => '  HTTP  ',
                'right' => '  Protocolo HTTP  ',
                'score' => 0,
            ],
        ]);

        $item = $matching->items()->firstOrFail();

        $this->assertSame('HTTP', $item->left_text);
        $this->assertSame('Protocolo HTTP', $item->right_text);
        $this->assertSame(1, $item->score);
    }

    public function test_build_matching_replaces_previous_items(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $service = app(MatchingService::class);

        $first = $service->buildMatching($activity, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo HTTP',
                'score' => 10,
            ],
            [
                'left' => 'PHP',
                'right' => 'Lenguaje PHP',
                'score' => 20,
            ],
        ]);

        $firstItemIds = $first->items()->pluck('id')->all();

        $second = $service->buildMatching($activity, [
            [
                'left' => 'Laravel',
                'right' => 'Framework PHP',
                'score' => 30,
            ],
        ]);

        $this->assertSame($first->id, $second->id);

        $this->assertCount(1, $second->items()->get());

        $this->assertDatabaseHas('matching_items', [
            'matching_id' => $second->id,
            'left_text' => 'Laravel',
            'right_text' => 'Framework PHP',
            'score' => 30,
        ]);

        foreach ($firstItemIds as $itemId) {
            $this->assertDatabaseMissing('matching_items', [
                'id' => $itemId,
            ]);
        }
    }

    public function test_matching_max_score_is_sum_of_item_scores(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $matching = $this->createMatching($activity, [
            [
                'left' => 'A',
                'right' => 'Respuesta A',
                'score' => 10,
            ],
            [
                'left' => 'B',
                'right' => 'Respuesta B',
                'score' => 20,
            ],
            [
                'left' => 'C',
                'right' => 'Respuesta C',
                'score' => 30,
            ],
        ]);

        $service = app(MatchingService::class);

        $this->assertSame(
            60,
            $service->maxScore($matching)
        );
    }

    public function test_teacher_can_open_matching_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createDraftActivity($class, $teacher);
        $matching = $this->createMatching($activity);

        $response = $this->actingAs($teacher)
            ->get(route(
                'teacher.matching.configure',
                $activity->id
            ));

        $response->assertOk();
        $response->assertViewIs('teacher.matching.configure');

        $response->assertViewHas(
            'activity',
            fn ($value) => $value->id === $activity->id
        );

        $response->assertViewHas(
            'matching',
            fn ($value) => $value->id === $matching->id
        );

        $response->assertViewHas('editing', true);
    }

    public function test_other_teacher_cannot_update_matching_configuration(): void
    {
        $teacher = $this->teacher('teacher@example.com');
        $otherTeacher = $this->teacher('other@example.com');

        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($otherTeacher)
            ->get(route(
                'teacher.matching.configure',
                $activity->id
            ));

        $response->assertForbidden();
    }

    public function test_teacher_can_store_matching_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(
                route('teacher.matching.store', $activity->id),
                [
                    'items' => [
                        [
                            'left' => 'HTTP',
                            'right' => 'Protocolo de transferencia',
                            'score' => 10,
                        ],
                        [
                            'left' => 'PHP',
                            'right' => 'Lenguaje de programación',
                            'score' => 20,
                        ],
                    ],
                ]
            );

        $response->assertRedirect(
            route('teacher.matching.edit', $activity->id)
        );

        $matching = $activity->fresh()->matching;

        $this->assertNotNull($matching);

        $this->assertSame(2, $matching->items()->count());

        $this->assertDatabaseHas('matching_items', [
            'matching_id' => $matching->id,
            'left_text' => 'HTTP',
            'right_text' => 'Protocolo de transferencia',
            'score' => 10,
        ]);
    }

    public function test_teacher_can_update_existing_matching_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createDraftActivity($class, $teacher);

        $matching = $this->createMatching($activity, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo HTTP',
                'score' => 10,
            ],
        ]);

        $response = $this->actingAs($teacher)
            ->put(
                route('teacher.matching.update', $activity->id),
                [
                    'items' => [
                        [
                            'left' => 'Laravel',
                            'right' => 'Framework PHP',
                            'score' => 25,
                        ],
                        [
                            'left' => 'MySQL',
                            'right' => 'Base de datos relacional',
                            'score' => 15,
                        ],
                    ],
                ]
            );

        $response->assertRedirect(
            route('teacher.matching.edit', $activity->id)
        );

        $this->assertSame(
            $matching->id,
            $activity->fresh()->matching->id
        );

        $this->assertCount(
            2,
            $matching->fresh()->items()->get()
        );

        $this->assertDatabaseHas('matching_items', [
            'matching_id' => $matching->id,
            'left_text' => 'Laravel',
            'right_text' => 'Framework PHP',
            'score' => 25,
        ]);

        $this->assertDatabaseHas('matching_items', [
            'matching_id' => $matching->id,
            'left_text' => 'MySQL',
            'right_text' => 'Base de datos relacional',
            'score' => 15,
        ]);

        $this->assertDatabaseMissing('matching_items', [
            'matching_id' => $matching->id,
            'left_text' => 'HTTP',
        ]);
    }

    public function test_student_can_open_matching_play_with_active_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $matching = $this->createMatching($activity);

        $participation = $this->createParticipation(
            $activity,
            $student
        );

        $response = $this->actingAs($student)
            ->get(route(
                'student.matching.play',
                $activity->id
            ));

        $response->assertOk();
        $response->assertViewIs('student.matching.play');

        $response->assertViewHas(
            'participation',
            fn ($value) => $value->id === $participation->id
        );

        $response->assertViewHas(
            'matching',
            fn ($value) => $value->id === $matching->id
        );

        $response->assertViewHas(
            'items',
            fn ($value) => $value->count() === 2
        );

        $response->assertViewHas(
            'correctIds',
            []
        );

        $response->assertViewHas(
            'earnedPoints',
            0
        );

        $response->assertViewHas(
            'maxScore',
            30
        );
    }

    public function test_student_cannot_open_matching_without_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createMatching($activity);

        $response = $this->actingAs($student)
            ->get(route(
                'student.matching.play',
                $activity->id
            ));

        $response->assertNotFound();
    }

    public function test_student_cannot_answer_matching_without_active_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $matching = $this->createMatching($activity);

        $item = $matching->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.matching.answer'),
                [
                    'matching_id' => $matching->id,
                    'matching_item_id' => $item->id,
                    'response' => $item->right_text,
                ]
            );

        $response->assertNotFound();

        $this->assertDatabaseCount(
            'matching_answers',
            0
        );
    }

    public function test_correct_matching_answer_creates_answer_and_updates_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity(
            $class,
            $teacher,
            [
                'max_score' => 100,
            ]
        );

        $matching = $this->createMatching($activity, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo HTTP',
                'score' => 10,
            ],
            [
                'left' => 'PHP',
                'right' => 'Lenguaje PHP',
                'score' => 20,
            ],
        ]);

        $participation = $this->createParticipation(
            $activity,
            $student
        );

        $item = $matching->items()
            ->where('left_text', 'HTTP')
            ->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.matching.answer'),
                [
                    'matching_id' => $matching->id,
                    'matching_item_id' => $item->id,
                    'response' => $item->right_text,
                ]
            );

        $response->assertOk();

        $response->assertJsonPath(
            'correct',
            true
        );

        $response->assertJsonPath(
            'already_answered',
            false
        );

        $response->assertJsonPath(
            'left',
            'HTTP'
        );

        $response->assertJsonPath(
            'right',
            'Protocolo HTTP'
        );

        $response->assertJsonPath(
            'score',
            10
        );

        $response->assertJsonPath(
            'completed',
            false
        );

        $response->assertJsonPath(
            'error',
            null
        );

        $this->assertDatabaseHas('matching_answers', [
            'participation_id' => $participation->id,
            'matching_item_id' => $item->id,
            'response' => 'Protocolo HTTP',
            'is_correct' => true,
            'score' => 10,
        ]);

        /*
         * syncScore() normaliza el score contra activity.max_score.
         *
         * Actividad:
         *   max_score = 100
         *
         * Matching:
         *   posible = 30
         *
         * Obtenido:
         *   10
         *
         * Resultado:
         *   (10 / 30) * 100 = 33
         */
        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 33,
        ]);
    }

    public function test_matching_answer_is_case_sensitive_but_ignores_outer_spaces(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $matching = $this->createMatching($activity, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo HTTP',
                'score' => 10,
            ],
        ]);

        $participation = $this->createParticipation(
            $activity,
            $student
        );

        $item = $matching->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.matching.answer'),
                [
                    'matching_id' => $matching->id,
                    'matching_item_id' => $item->id,
                    'response' => '   Protocolo HTTP   ',
                ]
            );

        $response->assertOk();
        $response->assertJsonPath('correct', true);
        $response->assertJsonPath('score', 10);

        $this->assertDatabaseHas('matching_answers', [
            'participation_id' => $participation->id,
            'matching_item_id' => $item->id,
            'response' => 'Protocolo HTTP',
            'is_correct' => true,
            'score' => 10,
        ]);
    }

    public function test_incorrect_matching_answer_creates_incorrect_answer_without_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $matching = $this->createMatching($activity, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo HTTP',
                'score' => 10,
            ],
        ]);

        $participation = $this->createParticipation(
            $activity,
            $student
        );

        $item = $matching->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.matching.answer'),
                [
                    'matching_id' => $matching->id,
                    'matching_item_id' => $item->id,
                    'response' => 'Respuesta incorrecta',
                ]
            );

        $response->assertOk();

        $response->assertJsonPath(
            'correct',
            false
        );

        $response->assertJsonPath(
            'already_answered',
            false
        );

        $response->assertJsonPath(
            'score',
            0
        );

        $response->assertJsonPath(
            'completed',
            false
        );

        $response->assertJsonPath(
            'error',
            'Esa pareja no es correcta.'
        );

        $this->assertDatabaseHas('matching_answers', [
            'participation_id' => $participation->id,
            'matching_item_id' => $item->id,
            'response' => 'Respuesta incorrecta',
            'is_correct' => false,
            'score' => 0,
        ]);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 0,
        ]);
    }

    public function test_same_matching_item_cannot_be_scored_twice(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $matching = $this->createMatching($activity, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo HTTP',
                'score' => 10,
            ],
            [
                'left' => 'PHP',
                'right' => 'Lenguaje PHP',
                'score' => 20,
            ],
        ]);

        $participation = $this->createParticipation(
            $activity,
            $student
        );

        $item = $matching->items()
            ->where('left_text', 'HTTP')
            ->firstOrFail();

        $this->actingAs($student);

        $first = $this->postJson(
            route('student.matching.answer'),
            [
                'matching_id' => $matching->id,
                'matching_item_id' => $item->id,
                'response' => $item->right_text,
            ]
        );

        $first->assertJsonPath(
            'correct',
            true
        );

        $first->assertJsonPath(
            'already_answered',
            false
        );

        $first->assertJsonPath(
            'score',
            10
        );

        $second = $this->postJson(
            route('student.matching.answer'),
            [
                'matching_id' => $matching->id,
                'matching_item_id' => $item->id,
                'response' => $item->right_text,
            ]
        );

        $second->assertJsonPath(
            'correct',
            true
        );

        $second->assertJsonPath(
            'already_answered',
            true
        );

        $second->assertJsonPath(
            'score',
            0
        );

        $this->assertSame(
            1,
            MatchingAnswer::where(
                'participation_id',
                $participation->id
            )
                ->where(
                    'matching_item_id',
                    $item->id
                )
                ->count()
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 33,
        ]);
    }

    public function test_correct_answer_from_different_matching_is_rejected(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activityA = $this->createActivity(
            $class,
            $teacher,
            [
                'title' => 'Matching A',
            ]
        );

        $activityB = $this->createActivity(
            $class,
            $teacher,
            [
                'title' => 'Matching B',
            ]
        );

        $matchingA = $this->createMatching($activityA, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo HTTP',
                'score' => 10,
            ],
        ]);

        $matchingB = $this->createMatching($activityB, [
            [
                'left' => 'PHP',
                'right' => 'Lenguaje PHP',
                'score' => 20,
            ],
        ]);

        $participation = $this->createParticipation(
            $activityA,
            $student
        );

        $itemFromB = $matchingB->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.matching.answer'),
                [
                    'matching_id' => $matchingA->id,
                    'matching_item_id' => $itemFromB->id,
                    'response' => $itemFromB->right_text,
                ]
            );

        $response->assertOk();

        $response->assertJsonPath(
            'correct',
            false
        );

        $response->assertJsonPath(
            'completed',
            false
        );

        $response->assertJsonPath(
            'score',
            0
        );

        $response->assertJsonPath(
            'error',
            'Ese ítem no pertenece a esta actividad.'
        );

        $this->assertDatabaseCount(
            'matching_answers',
            0
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 0,
        ]);
    }

    public function test_finding_last_matching_pair_completes_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity(
            $class,
            $teacher,
            [
                'max_score' => 100,
            ]
        );

        $matching = $this->createMatching($activity, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo HTTP',
                'score' => 10,
            ],
        ]);

        $participation = $this->createParticipation(
            $activity,
            $student
        );

        $item = $matching->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.matching.answer'),
                [
                    'matching_id' => $matching->id,
                    'matching_item_id' => $item->id,
                    'response' => $item->right_text,
                ]
            );

        $response->assertOk();

        $response->assertJsonPath(
            'correct',
            true
        );

        $response->assertJsonPath(
            'completed',
            true
        );

        $response->assertJsonPath(
            'score',
            10
        );

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

        $matching = $this->createMatching($activity, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo HTTP',
                'score' => 10,
            ],
            [
                'left' => 'PHP',
                'right' => 'Lenguaje PHP',
                'score' => 20,
            ],
        ]);

        $participation = $this->createParticipation(
            $activity,
            $student,
            [
                'status' => 'completed',
                'score' => 100,
                'completed_at' => now(),
            ]
        );

        $item = $matching->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.matching.answer'),
                [
                    'matching_id' => $matching->id,
                    'matching_item_id' => $item->id,
                    'response' => $item->right_text,
                ]
            );

        $response->assertNotFound();
    }

    public function test_matching_answer_requires_valid_payload(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $matching = $this->createMatching($activity);

        $this->createParticipation(
            $activity,
            $student
        );

        $response = $this->actingAs($student)
            ->postJson(
                route('student.matching.answer'),
                []
            );

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'matching_id',
            'matching_item_id',
            'response',
        ]);
    }

    public function test_matching_answer_rejects_nonexistent_matching(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createMatching($activity);

        $this->createParticipation(
            $activity,
            $student
        );

        $response = $this->actingAs($student)
            ->postJson(
                route('student.matching.answer'),
                [
                    'matching_id' => 999999,
                    'matching_item_id' => 1,
                    'response' => 'Respuesta',
                ]
            );

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'matching_id',
        ]);
    }

    public function test_matching_answer_requires_non_empty_response(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $matching = $this->createMatching($activity);

        $this->createParticipation(
            $activity,
            $student
        );

        $item = $matching->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.matching.answer'),
                [
                    'matching_id' => $matching->id,
                    'matching_item_id' => $item->id,
                    'response' => '',
                ]
            );

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'response',
        ]);
    }

    public function test_expired_participation_redirects_from_matching_play(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity(
            $class,
            $teacher,
            [
                'time_limit' => 60,
            ]
        );

        $this->createMatching($activity);

        $participation = $this->createParticipation(
            $activity,
            $student
        );

        $participation->started_at = Carbon::now()->subSeconds(120);
        $participation->save();

        $response = $this->actingAs($student)
            ->get(route(
                'student.matching.play',
                $activity->id
            ));

        /*
         * MatchingController detecta que getForPlay()
         * convirtió la participación en expired y redirige
         * al resultado.
         */
        $response->assertRedirect(
            route(
                'student.participation.result',
                $activity->id
            )
        );

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'expired',
        ]);
    }

    public function test_matching_service_returns_correct_item_ids_and_earned_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $matching = $this->createMatching($activity, [
            [
                'left' => 'HTTP',
                'right' => 'Protocolo HTTP',
                'score' => 10,
            ],
            [
                'left' => 'PHP',
                'right' => 'Lenguaje PHP',
                'score' => 20,
            ],
        ]);

        $participation = $this->createParticipation(
            $activity,
            $student
        );

        $first = $matching->items()
            ->where('left_text', 'HTTP')
            ->firstOrFail();

        $second = $matching->items()
            ->where('left_text', 'PHP')
            ->firstOrFail();

        MatchingAnswer::create([
            'participation_id' => $participation->id,
            'matching_item_id' => $first->id,
            'response' => $first->right_text,
            'is_correct' => true,
            'score' => 10,
            'answered_at' => now(),
        ]);

        MatchingAnswer::create([
            'participation_id' => $participation->id,
            'matching_item_id' => $second->id,
            'response' => $second->right_text,
            'is_correct' => false,
            'score' => 0,
            'answered_at' => now(),
        ]);

        $service = app(MatchingService::class);

        $correctIds = $service->correctItemIds(
            $matching,
            $participation
        );

        $this->assertSame(
            [$first->id],
            $correctIds
        );

        $this->assertSame(
            10,
            $service->earnedScore(
                $matching,
                $participation
            )
        );
    }

    public function test_matching_play_requires_existing_matching_configuration(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity(
            $class,
            $teacher
        );

        $this->createParticipation(
            $activity,
            $student
        );

        $response = $this->actingAs($student)
            ->get(route(
                'student.matching.play',
                $activity->id
            ));

        $response->assertNotFound();
    }
}

