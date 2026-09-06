<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Matching;
use App\Models\MatchingAnswer;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    private MatchingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(MatchingService::class);
    }

    private function makeActivity(): Activity
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $class = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Clase demo',
            'code' => 'MT' . strtoupper(uniqid()),
        ]);

        return Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Unir conceptos demo',
            'type' => 'matching',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => 300,
            'status' => 'draft',
        ]);
    }

    private function buildDefaultMatching(Activity $activity): Matching
    {
        return $this->service->buildMatching($activity, [
            ['left' => 'HTTP', 'right' => 'Protocolo de transferencia', 'score' => 10],
            ['left' => 'HTML', 'right' => 'Lenguaje de marcado', 'score' => 5],
        ]);
    }

    private function makeParticipation(Matching $matching): Participation
    {
        return Participation::create([
            'activity_id' => $matching->activity_id,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);
    }

    #[Test]
    public function genera_los_pares_y_normaliza_los_textos(): void
    {
        $matching = $this->buildDefaultMatching($this->makeActivity());

        $this->assertDatabaseCount('matching_items', 2);
        $this->assertSame(['HTTP', 'HTML'], $matching->items()->orderBy('id')->pluck('left_text')->all());
        $this->assertSame('Protocolo de transferencia', $matching->items()->first()->right_text);
        $this->assertSame(10, $matching->items()->first()->score);
    }

    #[Test]
    public function regenerar_la_actividad_reemplaza_los_pares_anteriores(): void
    {
        $activity = $this->makeActivity();
        $this->buildDefaultMatching($activity);

        $this->service->buildMatching($activity, [
            ['left' => 'CSS', 'right' => 'Hoja de estilos', 'score' => 10],
        ]);

        $this->assertDatabaseCount('matchings', 1);
        $this->assertSame('CSS', $activity->matching->items()->first()->left_text);
    }

    #[Test]
    public function indica_el_puntaje_maximo_y_el_acumulado(): void
    {
        $activity = $this->makeActivity();
        $matching = $this->buildDefaultMatching($activity);
        $participation = $this->makeParticipation($matching);

        $this->assertSame(15, $this->service->maxScore($matching));
        $this->assertSame(0, $this->service->earnedScore($matching, $participation));

        $item = $matching->items()->first();
        $this->service->checkAnswer($matching, $participation, $item->id, $item->right_text);

        $this->assertSame(10, $this->service->earnedScore($matching, $participation));
        $this->assertSame([$item->id], $this->service->correctItemIds($matching, $participation));
    }

    #[Test]
    public function detecta_una_pareja_correcta(): void
    {
        $matching = $this->buildDefaultMatching($this->makeActivity());
        $participation = $this->makeParticipation($matching);
        $item = $matching->items()->first();

        $result = $this->service->checkAnswer($matching, $participation, $item->id, $item->right_text);

        $this->assertTrue($result['correct']);
        $this->assertFalse($result['already_answered']);
        $this->assertSame(10, $result['score']);

        $this->assertDatabaseCount('matching_answers', 1);
        $this->assertDatabaseHas('matching_answers', [
            'matching_item_id' => $item->id,
            'is_correct' => true,
            'score' => 10,
        ]);
    }

    #[Test]
    public function no_puntua_dos_veces_la_misma_pareja(): void
    {
        $matching = $this->buildDefaultMatching($this->makeActivity());
        $participation = $this->makeParticipation($matching);
        $item = $matching->items()->first();

        $this->service->checkAnswer($matching, $participation, $item->id, $item->right_text);
        $result = $this->service->checkAnswer($matching, $participation, $item->id, $item->right_text);

        $this->assertTrue($result['correct']);
        $this->assertTrue($result['already_answered']);
        $this->assertSame(0, $result['score']);
        $this->assertDatabaseCount('matching_answers', 1);
    }

    #[Test]
    public function marca_incorrecta_una_pareja_errada_y_permite_reintentar(): void
    {
        $matching = $this->buildDefaultMatching($this->makeActivity());
        $participation = $this->makeParticipation($matching);
        $item = $matching->items()->first();
        $wrongRight = $matching->items()->skip(1)->first()->right_text;

        $result = $this->service->checkAnswer($matching, $participation, $item->id, $wrongRight);

        $this->assertFalse($result['correct']);
        $this->assertSame(0, $result['score']);

        $this->assertDatabaseHas('matching_answers', [
            'matching_item_id' => $item->id,
            'is_correct' => false,
            'score' => 0,
        ]);

        $retry = $this->service->checkAnswer($matching, $participation, $item->id, $item->right_text);
        $this->assertTrue($retry['correct']);
        $this->assertSame(10, $retry['score']);
    }

    #[Test]
    public function rechaza_un_item_que_no_pertenece_a_la_actividad(): void
    {
        $matching = $this->buildDefaultMatching($this->makeActivity());
        $participation = $this->makeParticipation($matching);
        $other = $this->buildDefaultMatching($this->makeActivity());

        $result = $this->service->checkAnswer($matching, $participation, $other->items()->first()->id, 'x');

        $this->assertFalse($result['correct']);
        $this->assertNotNull($result['error']);
        $this->assertDatabaseCount('matching_answers', 0);
    }

    #[Test]
    public function normaliza_los_textos_de_las_parejas(): void
    {
        $activity = $this->makeActivity();

        $this->service->buildMatching($activity, [
            ['left' => '  CSS  ', 'right' => ' Hoja de estilos ', 'score' => 1],
        ]);

        $item = $activity->matching->items()->first();
        $this->assertSame('CSS', $item->left_text);
        $this->assertSame('Hoja de estilos', $item->right_text);

        $matching = $this->service->checkAnswer($activity->matching, $this->makeParticipation($activity->matching), $item->id, '  Hoja de estilos  ');
        $this->assertTrue($matching['correct']);
    }
}