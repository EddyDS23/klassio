<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\Participation;
use App\Models\Roulette;
use App\Models\RouletteAnswer;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\ParticipationService;
use App\Services\RouletteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RouletteTest extends TestCase
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
            'description' => 'Roulette tests',
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
            'title' => 'Roulette Activity',
            'description' => 'Roulette test activity',
            'type' => 'roulette',
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

    private function createRoulette(
        Activity $activity,
        array $items = [
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
        ]
    ): Roulette {
        return app(RouletteService::class)->buildRoulette(
            $activity,
            $items
        );
    }

    public function test_build_roulette_creates_roulette_and_items(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $roulette = $this->createRoulette($activity, [
            [
                'question' => '¿Qué es HTTP?',
                'option_a' => 'Protocolo',
                'option_b' => 'Sistema',
                'option_c' => 'Lenguaje',
                'option_d' => 'Base',
                'correct_option' => 'a',
                'points' => 10,
            ],
            [
                'question' => '¿Qué es PHP?',
                'option_a' => 'Base',
                'option_b' => 'Lenguaje',
                'option_c' => 'Framework',
                'option_d' => 'Servidor',
                'correct_option' => 'b',
                'points' => 20,
            ],
        ]);

        $this->assertDatabaseHas('roulettes', [
            'id' => $roulette->id,
            'activity_id' => $activity->id,
        ]);

        $this->assertDatabaseHas('roulette_items', [
            'roulette_id' => $roulette->id,
            'question' => '¿Qué es HTTP?',
            'option_a' => 'Protocolo',
            'option_b' => 'Sistema',
            'option_c' => 'Lenguaje',
            'option_d' => 'Base',
            'correct_option' => 'a',
            'points' => 10,
        ]);

        $this->assertDatabaseHas('roulette_items', [
            'roulette_id' => $roulette->id,
            'question' => '¿Qué es PHP?',
            'option_b' => 'Lenguaje',
            'correct_option' => 'b',
            'points' => 20,
        ]);

        $this->assertSame(2, $roulette->items()->count());
    }

    public function test_build_roulette_normalizes_text_correct_option_and_minimum_points(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $roulette = $this->createRoulette($activity, [
            [
                'question' => '  ¿Qué es X?  ',
                'option_a' => '  Respuesta A  ',
                'option_b' => 'Respuesta B',
                'option_c' => 'Respuesta C',
                'option_d' => 'Respuesta D',
                'correct_option' => 'z',
                'points' => 0,
            ],
        ]);

        $item = $roulette->items()->firstOrFail();

        $this->assertSame('¿Qué es X?', $item->question);
        $this->assertSame('Respuesta A', $item->option_a);
        $this->assertSame('a', $item->correct_option);
        $this->assertSame(1, $item->points);
    }

    public function test_build_roulette_replaces_previous_items(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $service = app(RouletteService::class);

        $first = $service->buildRoulette($activity, [
            [
                'question' => 'Pregunta 1',
                'option_a' => 'A1',
                'option_b' => 'B1',
                'option_c' => 'C1',
                'option_d' => 'D1',
                'correct_option' => 'a',
                'points' => 10,
            ],
            [
                'question' => 'Pregunta 2',
                'option_a' => 'A2',
                'option_b' => 'B2',
                'option_c' => 'C2',
                'option_d' => 'D2',
                'correct_option' => 'b',
                'points' => 20,
            ],
        ]);

        $firstItemIds = $first->items()->pluck('id')->all();

        $second = $service->buildRoulette($activity, [
            [
                'question' => 'Pregunta 3',
                'option_a' => 'A3',
                'option_b' => 'B3',
                'option_c' => 'C3',
                'option_d' => 'D3',
                'correct_option' => 'c',
                'points' => 30,
            ],
        ]);

        $this->assertSame($first->id, $second->id);

        $this->assertCount(1, $second->items()->get());

        $this->assertDatabaseHas('roulette_items', [
            'roulette_id' => $second->id,
            'question' => 'Pregunta 3',
            'correct_option' => 'c',
            'points' => 30,
        ]);

        foreach ($firstItemIds as $itemId) {
            $this->assertDatabaseMissing('roulette_items', [
                'id' => $itemId,
            ]);
        }
    }

    public function test_roulette_earned_score_counts_only_correct_answers(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $roulette = $this->createRoulette($activity, [
            [
                'question' => 'P1',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'a',
                'points' => 10,
            ],
            [
                'question' => 'P2',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'b',
                'points' => 20,
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $first = $roulette->items()->orderBy('id')->first();
        $second = $roulette->items()->orderBy('id')->skip(1)->first();

        RouletteAnswer::create([
            'participation_id' => $participation->id,
            'roulette_item_id' => $first->id,
            'response' => $first->option_a,
            'is_correct' => true,
            'score' => 10,
            'answered_at' => now(),
        ]);

        RouletteAnswer::create([
            'participation_id' => $participation->id,
            'roulette_item_id' => $second->id,
            'response' => $second->option_a,
            'is_correct' => false,
            'score' => 0,
            'answered_at' => now(),
        ]);

        $service = app(RouletteService::class);

        $this->assertSame(30, $service->maxScore($roulette));
        $this->assertSame(10, $service->earnedScore($roulette, $participation));
    }

    public function test_teacher_can_open_roulette_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createDraftActivity($class, $teacher);
        $roulette = $this->createRoulette($activity);

        $response = $this->actingAs($teacher)
            ->get(route('teacher.roulette.configure', $activity->id));

        $response->assertOk();
        $response->assertViewIs('teacher.roulette.configure');

        $response->assertViewHas('activity', fn ($value) => $value->id === $activity->id);

        $response->assertViewHas('roulette', fn ($value) => $value->id === $roulette->id);

        $response->assertViewHas('editing', true);
    }

    public function test_other_teacher_cannot_update_roulette_configuration(): void
    {
        $teacher = $this->teacher('teacher@example.com');
        $otherTeacher = $this->teacher('other@example.com');

        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($otherTeacher)
            ->get(route('teacher.roulette.configure', $activity->id));

        $response->assertForbidden();
    }

    public function test_teacher_can_store_roulette_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(
                route('teacher.roulette.store', $activity->id),
                [
                    'items' => [
                        [
                            'question' => '¿Qué es HTTP?',
                            'option_a' => 'Protocolo',
                            'option_b' => 'Sistema',
                            'option_c' => 'Lenguaje',
                            'option_d' => 'Base',
                            'correct_option' => 'a',
                            'points' => 10,
                        ],
                        [
                            'question' => '¿Qué es PHP?',
                            'option_a' => 'Base',
                            'option_b' => 'Lenguaje',
                            'option_c' => 'Framework',
                            'option_d' => 'Servidor',
                            'correct_option' => 'b',
                            'points' => 20,
                        ],
                    ],
                ]
            );

        $response->assertRedirect(route('teacher.roulette.edit', $activity->id));

        $roulette = $activity->fresh()->roulette;

        $this->assertNotNull($roulette);

        $this->assertSame(2, $roulette->items()->count());

        $this->assertDatabaseHas('roulette_items', [
            'roulette_id' => $roulette->id,
            'question' => '¿Qué es HTTP?',
            'correct_option' => 'a',
            'points' => 10,
        ]);
    }

    public function test_teacher_can_update_existing_roulette_configuration(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);

        $activity = $this->createDraftActivity($class, $teacher);

        $roulette = $this->createRoulette($activity, [
            [
                'question' => 'Pregunta 1',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'a',
                'points' => 10,
            ],
        ]);

        $response = $this->actingAs($teacher)
            ->put(
                route('teacher.roulette.update', $activity->id),
                [
                    'items' => [
                        [
                            'question' => 'Pregunta 2',
                            'option_a' => 'A',
                            'option_b' => 'B',
                            'option_c' => 'C',
                            'option_d' => 'D',
                            'correct_option' => 'c',
                            'points' => 25,
                        ],
                        [
                            'question' => 'Pregunta 3',
                            'option_a' => 'A',
                            'option_b' => 'B',
                            'option_c' => 'C',
                            'option_d' => 'D',
                            'correct_option' => 'd',
                            'points' => 15,
                        ],
                    ],
                ]
            );

        $response->assertRedirect(route('teacher.roulette.edit', $activity->id));

        $this->assertSame($roulette->id, $activity->fresh()->roulette->id);

        $this->assertCount(2, $roulette->fresh()->items()->get());

        $this->assertDatabaseHas('roulette_items', [
            'roulette_id' => $roulette->id,
            'question' => 'Pregunta 2',
            'correct_option' => 'c',
            'points' => 25,
        ]);

        $this->assertDatabaseHas('roulette_items', [
            'roulette_id' => $roulette->id,
            'question' => 'Pregunta 3',
            'correct_option' => 'd',
            'points' => 15,
        ]);

        $this->assertDatabaseMissing('roulette_items', [
            'roulette_id' => $roulette->id,
            'question' => 'Pregunta 1',
        ]);
    }

    public function test_teacher_configure_requires_valid_items(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $response = $this->actingAs($teacher)
            ->post(
                route('teacher.roulette.store', $activity->id),
                ['items' => []]
            );

        $response->assertSessionHasErrors(['items']);
    }

    public function test_student_can_open_roulette_play_with_active_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $roulette = $this->createRoulette($activity);

        $participation = $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->get(route('student.roulette.play', $activity->id));

        $response->assertOk();
        $response->assertViewIs('student.roulette.play');

        $response->assertViewHas('participation', fn ($value) => $value->id === $participation->id);

        $response->assertViewHas('roulette', fn ($value) => $value->id === $roulette->id);

        $response->assertViewHas('totalItems', 2);

        $response->assertViewHas('answeredCount', 0);

        $response->assertViewHas('earnedPoints', 0);

        $response->assertViewHas('maxScore', 30);
    }

    public function test_student_cannot_open_roulette_without_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createRoulette($activity);

        $response = $this->actingAs($student)
            ->get(route('student.roulette.play', $activity->id));

        $response->assertNotFound();
    }

    public function test_student_cannot_open_roulette_play_when_not_configured(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->get(route('student.roulette.play', $activity->id));

        $response->assertNotFound();
    }

    public function test_spin_requires_active_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $this->createRoulette($activity);

        $response = $this->actingAs($student)
            ->postJson(
                route('student.roulette.spin'),
                ['activity_id' => $activity->id]
            );

        $response->assertNotFound();
    }

    public function test_spin_returns_pending_random_item_excluding_answered(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $roulette = $this->createRoulette($activity, [
            [
                'question' => 'P1',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'a',
                'points' => 10,
            ],
            [
                'question' => 'P2',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'b',
                'points' => 20,
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $answered = $roulette->items()->orderBy('id')->first();

        $this->createParticipationAnswers($participation, $answered->id);

        $response = $this->actingAs($student)
            ->postJson(
                route('student.roulette.spin'),
                ['activity_id' => $activity->id]
            );

        $response->assertOk();

        $response->assertJsonPath('completed', false);

        $response->assertJsonPath('answered', 1);

        $response->assertJsonPath('total', 2);

        $item = $response->json('item');

        $this->assertNotSame($answered->id, $item['id']);

        $response->assertJsonMissingPath('item.correct_option');
    }

    private function createParticipationAnswers(
        Participation $participation,
        int $itemId
    ): RouletteAnswer {
        return RouletteAnswer::create([
            'participation_id' => $participation->id,
            'roulette_item_id' => $itemId,
            'response' => 'a',
            'is_correct' => false,
            'score' => 0,
            'answered_at' => now(),
        ]);
    }

    public function test_spin_returns_completed_when_all_answered(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $roulette = $this->createRoulette($activity, [
            [
                'question' => 'P1',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'a',
                'points' => 10,
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $item = $roulette->items()->firstOrFail();

        $this->createParticipationAnswers($participation, $item->id);

        $response = $this->actingAs($student)
            ->postJson(
                route('student.roulette.spin'),
                ['activity_id' => $activity->id]
            );

        $response->assertOk();

        $response->assertJsonPath('completed', true);

        $response->assertJsonPath('item', null);
    }

    public function test_roulette_item_serialization_hides_correct_option(): void
    {
        $teacher = $this->teacher();
        $class = $this->createClass($teacher);
        $activity = $this->createDraftActivity($class, $teacher);

        $roulette = $this->createRoulette($activity, [
            [
                'question' => 'P1',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'a',
                'points' => 10,
            ],
        ]);

        $item = $roulette->items()->firstOrFail();

        $serialized = (new RouletteService(
            app(ParticipationService::class)
        ))->serializeItem($item);

        $this->assertArrayNotHasKey('correct_option', $serialized);

        $this->assertCount(4, $serialized['options']);

        $keys = collect($serialized['options'])
            ->pluck('key')
            ->sort()
            ->values()
            ->all();

        $this->assertSame(['a', 'b', 'c', 'd'], $keys);
    }

    public function test_correct_roulette_answer_creates_answer_and_updates_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, ['max_score' => 100]);
        $roulette = $this->createRoulette($activity, [
            [
                'question' => 'P1',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'a',
                'points' => 10,
            ],
            [
                'question' => 'P2',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'b',
                'points' => 20,
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $item = $roulette->items()->orderBy('id')->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.roulette.answer'),
                [
                    'roulette_id' => $roulette->id,
                    'roulette_item_id' => $item->id,
                    'response' => 'a',
                ]
            );

        $response->assertOk();

        $response->assertJsonPath('is_correct', true);
        $response->assertJsonPath('already_answered', false);
        $response->assertJsonPath('score', 10);
        $response->assertJsonPath('completed', false);
        $response->assertJsonPath('answered', 1);
        $response->assertJsonPath('total', 2);
        $response->assertJsonPath('error', null);

        $this->assertDatabaseHas('roulette_answers', [
            'participation_id' => $participation->id,
            'roulette_item_id' => $item->id,
            'response' => 'A',
            'is_correct' => true,
            'score' => 10,
        ]);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 33,
        ]);
    }

    public function test_incorrect_roulette_answer_creates_answer_without_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, ['max_score' => 100]);
        $roulette = $this->createRoulette($activity, [
            [
                'question' => 'P1',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'a',
                'points' => 10,
            ],
            [
                'question' => 'P2',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'b',
                'points' => 20,
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $item = $roulette->items()->orderBy('id')->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.roulette.answer'),
                [
                    'roulette_id' => $roulette->id,
                    'roulette_item_id' => $item->id,
                    'response' => 'c',
                ]
            );

        $response->assertOk();

        $response->assertJsonPath('is_correct', false);
        $response->assertJsonPath('score', 0);
        $response->assertJsonPath('completed', false);
        $response->assertJsonPath('correct_option', 'a');
        $response->assertJsonPath('correct_text', 'A');

        $this->assertDatabaseHas('roulette_answers', [
            'participation_id' => $participation->id,
            'roulette_item_id' => $item->id,
            'response' => 'C',
            'is_correct' => false,
            'score' => 0,
        ]);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 0,
        ]);
    }

    public function test_same_roulette_item_cannot_be_scored_twice(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, ['max_score' => 100]);
        $roulette = $this->createRoulette($activity, [
            [
                'question' => 'P1',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'a',
                'points' => 10,
            ],
            [
                'question' => 'P2',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'b',
                'points' => 20,
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $item = $roulette->items()->orderBy('id')->firstOrFail();

        $this->actingAs($student);

        $first = $this->postJson(
            route('student.roulette.answer'),
            [
                'roulette_id' => $roulette->id,
                'roulette_item_id' => $item->id,
                'response' => 'a',
            ]
        );

        $first->assertJsonPath('is_correct', true);
        $first->assertJsonPath('already_answered', false);
        $first->assertJsonPath('score', 10);

        $second = $this->postJson(
            route('student.roulette.answer'),
            [
                'roulette_id' => $roulette->id,
                'roulette_item_id' => $item->id,
                'response' => 'a',
            ]
        );

        $second->assertJsonPath('already_answered', true);
        $second->assertJsonPath('score', 0);

        $this->assertSame(1, RouletteAnswer::where(
            'participation_id',
            $participation->id
        )->where('roulette_item_id', $item->id)->count());

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'score' => 33,
        ]);
    }

    public function test_correct_answer_from_different_roulette_is_rejected(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activityA = $this->createActivity($class, $teacher, ['title' => 'Roulette A']);
        $activityB = $this->createActivity($class, $teacher, ['title' => 'Roulette B']);

        $rouletteA = $this->createRoulette($activityA);
        $rouletteB = $this->createRoulette($activityB);

        $participation = $this->createParticipation($activityA, $student);

        $itemFromB = $rouletteB->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.roulette.answer'),
                [
                    'roulette_id' => $rouletteA->id,
                    'roulette_item_id' => $itemFromB->id,
                    'response' => 'a',
                ]
            );

        $response->assertOk();

        $response->assertJsonPath('is_correct', false);
        $response->assertJsonPath('completed', false);
        $response->assertJsonPath('score', 0);
        $response->assertJsonPath('error', 'Ese ítem no pertenece a esta actividad.');

        $this->assertDatabaseCount('roulette_answers', 0);
    }

    public function test_roulette_answer_requires_valid_payload(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $roulette = $this->createRoulette($activity);

        $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->postJson(route('student.roulette.answer'), []);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'roulette_id',
            'roulette_item_id',
            'response',
        ]);
    }

    public function test_roulette_answer_rejects_nonexistent_roulette(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);

        $this->createParticipation($activity, $student);

        $response = $this->actingAs($student)
            ->postJson(
                route('student.roulette.answer'),
                [
                    'roulette_id' => 999999,
                    'roulette_item_id' => 1,
                    'response' => 'a',
                ]
            );

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['roulette_id']);
    }

    public function test_roulette_answer_requires_single_char_response(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $roulette = $this->createRoulette($activity);

        $this->createParticipation($activity, $student);

        $item = $roulette->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.roulette.answer'),
                [
                    'roulette_id' => $roulette->id,
                    'roulette_item_id' => $item->id,
                    'response' => '',
                ]
            );

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors(['response']);
    }

    public function test_answer_after_completed_participation_is_rejected(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher);
        $roulette = $this->createRoulette($activity);

        $participation = $this->createParticipation($activity, $student, [
            'status' => 'completed',
            'score' => 100,
            'completed_at' => now(),
        ]);

        $item = $roulette->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.roulette.answer'),
                [
                    'roulette_id' => $roulette->id,
                    'roulette_item_id' => $item->id,
                    'response' => 'a',
                ]
            );

        $response->assertNotFound();
    }

    public function test_final_roulette_answer_completes_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, ['max_score' => 100]);
        $roulette = $this->createRoulette($activity, [
            [
                'question' => 'P1',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'a',
                'points' => 10,
            ],
        ]);

        $participation = $this->createParticipation($activity, $student);

        $item = $roulette->items()->firstOrFail();

        $response = $this->actingAs($student)
            ->postJson(
                route('student.roulette.answer'),
                [
                    'roulette_id' => $roulette->id,
                    'roulette_item_id' => $item->id,
                    'response' => 'a',
                ]
            );

        $response->assertOk();

        $response->assertJsonPath('is_correct', true);
        $response->assertJsonPath('completed', true);
        $response->assertJsonPath('score', 10);

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'completed',
            'score' => 100,
        ]);

        $this->assertNotNull(Participation::find($participation->id)->completed_at);
    }

    public function test_get_result_includes_roulette_answers(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, ['max_score' => 100]);
        $roulette = $this->createRoulette($activity, [
            [
                'question' => 'P1',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'a',
                'points' => 10,
            ],
        ]);

        $participation = $this->createParticipation($activity, $student, [
            'status' => 'completed',
            'score' => 100,
            'completed_at' => now(),
            'elapsed_seconds' => 30,
        ]);

        $item = $roulette->items()->firstOrFail();

        RouletteAnswer::create([
            'participation_id' => $participation->id,
            'roulette_item_id' => $item->id,
            'response' => 'A',
            'is_correct' => true,
            'score' => 10,
            'answered_at' => now(),
        ]);

        $result = app(ParticipationService::class)->getResult($participation);

        $this->assertSame(1, $result['total']);
        $this->assertCount(1, $result['answers']);

        $this->assertSame('P1', $result['answers'][0]['question']);
        $this->assertSame('A', $result['answers'][0]['response']);
        $this->assertSame('A', $result['answers'][0]['correct']);
        $this->assertTrue($result['answers'][0]['is_correct']);
        $this->assertSame(10, $result['answers'][0]['score']);
    }

    public function test_expired_participation_redirects_from_roulette_play(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();

        $class = $this->createClass($teacher);
        $this->enroll($class, $student);

        $activity = $this->createActivity($class, $teacher, ['time_limit' => 60]);

        $this->createRoulette($activity);

        $participation = $this->createParticipation($activity, $student);

        $participation->started_at = Carbon::now()->subSeconds(120);
        $participation->save();

        $response = $this->actingAs($student)
            ->get(route('student.roulette.play', $activity->id));

        $response->assertRedirect(route('student.participation.result', $activity->id));

        $this->assertDatabaseHas('participations', [
            'id' => $participation->id,
            'status' => 'expired',
        ]);
    }
}
