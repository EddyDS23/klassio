<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Unit;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingTest extends TestCase
{
    use RefreshDatabase;

    private GradingService $grading;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grading = new GradingService;
    }

    public function test_unit_score_is_the_average_of_its_activities(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);

        $unitOne = $this->createUnit($class, 1);
        $unitTwo = $this->createUnit($class, 2);

        $activityOne = $this->createActivity($class, $teacher, ['unit_id' => $unitOne->id]);
        $activityTwo = $this->createActivity($class, $teacher, ['unit_id' => $unitOne->id]);
        $activityThree = $this->createActivity($class, $teacher, ['unit_id' => $unitTwo->id]);

        $this->enroll($class, $student);

        $this->participate($activityOne, $student, score: 100);
        $this->participate($activityTwo, $student, score: 80);
        $this->participate($activityThree, $student, score: 80);

        $grading = $this->grading->forClass($class->refresh());
        $row = $grading['students'][0];

        $this->assertSame(90.0, $row['unitScores'][$unitOne->id]);
        $this->assertSame(80.0, $row['unitScores'][$unitTwo->id]);
        $this->assertSame(85.0, $row['final']);
    }

    public function test_activities_without_unit_form_their_own_group(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, ['unit_id' => null]);

        $this->enroll($class, $student);
        $this->participate($activity, $student, score: 60);

        $grading = $this->grading->forClass($class->refresh());

        $this->assertCount(1, $grading['units']);
        $this->assertSame(GradingService::WITHOUT_UNIT_KEY, $grading['units'][0]['key']);
        $this->assertSame('Sin unidad', $grading['units'][0]['name']);

        $row = $grading['students'][0];

        $this->assertSame(60.0, $row['unitScores'][GradingService::WITHOUT_UNIT_KEY]);
        $this->assertSame(60.0, $row['final']);
    }

    public function test_last_attempt_wins(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher, ['unit_id' => null]);

        $this->enroll($class, $student);

        $this->participate($activity, $student, score: 40, attempt: 1);
        $this->participate($activity, $student, score: 90, attempt: 2);
        $this->participate($activity, $student, score: 10, attempt: 3);

        $grading = $this->grading->forClass($class->refresh());

        // La nota es la ÚLTIMA jugada (10), no la mejor (90).
        $this->assertSame(10.0, $grading['students'][0]['final']);
    }

    public function test_abandoned_and_expired_count_with_the_latest_attempt(): void
    {
        $teacher = $this->teacher();
        $first = $this->student('grading-a@example.com');
        $second = $this->student('grading-b@example.com');
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, ['unit_id' => null]);

        $this->enroll($class, $first);
        $this->enroll($class, $second);

        // La última jugada abandona: acepta su puntaje parcial.
        $this->participate($activity, $first, score: 80);
        $this->participate($activity, $first, score: 30, status: 'abandoned');

        // Solo expiró: cuenta su puntaje.
        $this->participate($activity, $second, score: 55, status: 'expired');

        $grading = $this->grading->forClass($class->refresh());
        $scores = collect($grading['students'])->pluck('final', 'student.email');

        $this->assertSame(30.0, $scores->get('grading-a@example.com'));
        $this->assertSame(55.0, $scores->get('grading-b@example.com'));
    }

    public function test_started_attempt_does_not_replace_a_terminal_one(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);
        $activity = $this->createActivity($class, $teacher, ['unit_id' => null]);

        $this->enroll($class, $student);

        $this->participate($activity, $student, score: 70);
        $this->participate($activity, $student, score: 0, status: 'started');

        $grading = $this->grading->forClass($class->refresh());

        $this->assertSame(70.0, $grading['students'][0]['final']);
    }

    public function test_overdue_activity_without_play_counts_as_zero(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);

        $unit = $this->createUnit($class, 1);

        $played = $this->createActivity($class, $teacher, ['unit_id' => $unit->id, 'due_at' => now()->addDay()]);
        $overdue = $this->createActivity($class, $teacher, ['unit_id' => $unit->id, 'due_at' => now()->subDay()]);

        $this->enroll($class, $student);
        $this->participate($played, $student, score: 100);

        $grading = $this->grading->forClass($class->refresh());
        $row = $grading['students'][0];

        // (100 + 0 por la vencida sin jugada) / 2 = 50.
        $this->assertSame(50.0, $row['final']);
        $this->assertSame(0, $row['activityRaw'][$overdue->id]);
    }

    public function test_active_activity_without_play_is_skipped(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);

        $unit = $this->createUnit($class, 1);

        $played = $this->createActivity($class, $teacher, ['unit_id' => $unit->id, 'due_at' => now()->addDay()]);
        $pending = $this->createActivity($class, $teacher, ['unit_id' => $unit->id, 'due_at' => now()->addDays(5)]);

        $this->enroll($class, $student);
        $this->participate($played, $student, score: 80);

        $grading = $this->grading->forClass($class->refresh());
        $row = $grading['students'][0];

        // La actividad vigente sin jugada no pesa: la unidad es la única nota.
        $this->assertSame(80.0, $row['final']);
        $this->assertArrayNotHasKey($pending->id, $row['activityRaw']);
    }

    public function test_closed_activity_without_due_date_counts_as_zero(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);

        $closed = $this->createActivity($class, $teacher, ['unit_id' => null, 'status' => 'closed']);

        $this->enroll($class, $student);

        $grading = $this->grading->forClass($class->refresh());

        // Cerrada sin fecha: quien no jugó queda con 0.
        $this->assertSame(0.0, $grading['students'][0]['final']);
        $this->assertSame(0, $grading['students'][0]['activityRaw'][$closed->id]);
    }

    public function test_closed_activity_with_future_due_date_counts_as_zero(): void
    {
        $teacher = $this->teacher();
        $played = $this->student('grading-played@example.com');
        $absent = $this->student('grading-absent@example.com');
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, [
            'unit_id' => null,
            'status' => 'closed',
            'due_at' => now()->addYear(),
        ]);

        $this->enroll($class, $played);
        $this->enroll($class, $absent);

        $this->participate($activity, $played, score: 90);

        $grading = $this->grading->forClass($class->refresh());
        $scores = collect($grading['students'])->pluck('final', 'student.email');
        $absentRow = collect($grading['students'])
            ->first(fn (array $row) => $row['student']->email === 'grading-absent@example.com');

        // Cerrada a mano aunque la fecha futura: sin jugada es 0; el que jugó,
        // conserva su nota.
        $this->assertSame(90.0, $scores->get('grading-played@example.com'));
        $this->assertSame(0.0, $scores->get('grading-absent@example.com'));
        $this->assertSame(0, $absentRow['activityRaw'][$activity->id]);
    }

    public function test_activity_score_shows_raw_points_against_its_max_score(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, ['unit_id' => null, 'max_score' => 120]);

        $this->enroll($class, $student);
        $this->participate($activity, $student, score: 120);

        $grading = $this->grading->forClass($class->refresh());
        $row = $grading['students'][0];

        // Nota normalizada (0-100) para promedios vs puntaje crudo (0-max_score).
        $this->assertSame(100.0, $row['activityScores'][$activity->id]);
        $this->assertSame(120, $row['activityRaw'][$activity->id]);
        $this->assertSame(100.0, $row['final']);

        $this->actingAs($teacher)
            ->get(route('teacher.classes.grading', $class->id))
            ->assertOk()
            ->assertSee('120 / 120');
    }

    public function test_team_score_is_shared_by_team_members(): void
    {
        $teacher = $this->teacher();
        $solo = $this->student('solo@example.com');
        $studentOne = $this->student('team1@example.com');
        $studentTwo = $this->student('team2@example.com');
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, ['unit_id' => null, 'mode' => 'team']);

        $this->enroll($class, $solo);
        $this->enroll($class, $studentOne);
        $this->enroll($class, $studentTwo);

        $team = Team::create(['activity_id' => $activity->id, 'name' => 'Equipo Sol']);
        TeamMember::create(['team_id' => $team->id, 'student_id' => $studentOne->id]);
        TeamMember::create(['team_id' => $team->id, 'student_id' => $studentTwo->id]);

        // Una sola participation de equipo (un representante) con puntaje alto.
        $this->participate($activity, $studentOne, score: 90, teamId: $team->id);

        $grading = $this->grading->forClass($class->refresh());
        $scores = collect($grading['students'])->pluck('final', 'student.email');

        $this->assertSame(90.0, $scores->get('team1@example.com'));
        $this->assertSame(90.0, $scores->get('team2@example.com'));
        $this->assertNull($scores->get('solo@example.com'));
    }

    public function test_final_is_null_without_any_participation(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);

        $this->createActivity($class, $teacher, ['unit_id' => null]);
        $this->enroll($class, $student);

        $grading = $this->grading->forClass($class->refresh());

        $this->assertNull($grading['students'][0]['final']);
        $this->assertNull($grading['students'][0]['unitScores'][GradingService::WITHOUT_UNIT_KEY]);
    }

    public function test_draft_activities_are_not_graded(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);

        $draft = $this->createActivity($class, $teacher, ['unit_id' => null, 'status' => 'draft']);
        $published = $this->createActivity($class, $teacher, ['unit_id' => null]);

        $this->enroll($class, $student);
        $this->participate($draft, $student, score: 100);
        $this->participate($published, $student, score: 70);

        $grading = $this->grading->forClass($class->refresh());
        $row = $grading['students'][0];

        $this->assertCount(1, $grading['units'][0]['activities']);
        $this->assertSame(70.0, $row['activityScores'][$published->id]);
        $this->assertArrayNotHasKey($draft->id, $row['activityScores']);
    }

    public function test_teacher_can_view_class_grading_page(): void
    {
        $teacher = $this->teacher();
        $student = $this->student();
        $class = $this->createClass($teacher);

        $activity = $this->createActivity($class, $teacher, ['unit_id' => null]);

        $this->enroll($class, $student);
        $this->participate($activity, $student, score: 85);

        $this->actingAs($teacher)
            ->get(route('teacher.classes.grading', $class->id))
            ->assertOk()
            ->assertViewIs('teacher.classes.grading')
            ->assertSee($student->name)
            ->assertSee('85.0')
            ->assertSee('Calificación general')
            ->assertSee('Buscar alumno')
            ->assertSee('data-detail');
    }

    public function test_other_teacher_cannot_view_class_grading(): void
    {
        $owner = $this->teacher('owner@example.com');
        $other = $this->teacher('other@example.com');
        $class = $this->createClass($owner);

        $this->actingAs($other)
            ->get(route('teacher.classes.grading', $class->id))
            ->assertForbidden();
    }

    /**
     * Helpers ----------------------------------------------------------------
     */
    private function teacher(string $email = 'grading-teacher@example.com'): User
    {
        return User::factory()->create([
            'email' => $email,
            'role' => 'teacher',
            'status' => 'active',
        ]);
    }

    private function student(string $email = 'grading-student@example.com'): User
    {
        return User::factory()->create([
            'email' => $email,
            'role' => 'student',
            'status' => 'active',
        ]);
    }

    private function createClass(User $teacher): SchoolClass
    {
        return SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Clase de calificaciones',
            'description' => 'Clase para probar la calificación general',
            'code' => strtoupper(fake()->unique()->bothify('GRADE###')),
            'status' => 'active',
        ]);
    }

    private function createUnit(SchoolClass $class, int $number): Unit
    {
        return Unit::create([
            'class_id' => $class->id,
            'number' => $number,
            'name' => 'Unidad '.$number,
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
            'title' => 'Actividad de calificación',
            'description' => 'Actividad para probar calificaciones',
            'type' => 'kahoot',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => null,
            'attempts' => 3,
            'due_at' => null,
            'status' => 'published',
        ], $attributes));
    }

    private function enroll(SchoolClass $class, User $student): Enrollment
    {
        return Enrollment::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'active',
        ]);
    }

    private function participate(
        Activity $activity,
        User $student,
        ?int $score = 50,
        int $attempt = 1,
        ?int $teamId = null,
        string $status = 'completed'
    ): Participation {
        return Participation::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'team_id' => $teamId,
            'attempt' => $attempt,
            'status' => $status,
            'score' => $score,
            'completed_at' => $status === 'completed' ? now() : null,
            'elapsed_seconds' => 30,
        ]);
    }
}
