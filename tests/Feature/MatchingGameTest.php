<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Matching;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MatchingGameTest extends TestCase
{
    use RefreshDatabase;

    private MatchingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(MatchingService::class);

        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);
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

    private function payload(): array
    {
        return [
            'activity_id' => $this->makeActivity()->id,
            'items' => [
                ['left' => 'HTTP', 'right' => 'Protocolo de transferencia', 'score' => 10],
                ['left' => 'HTML', 'right' => 'Lenguaje de marcado', 'score' => 5],
            ],
        ];
    }

    private function buildDefaultMatching(Activity $activity): Matching
    {
        return $this->service->buildMatching($activity, [
            ['left' => 'HTTP', 'right' => 'Protocolo de transferencia', 'score' => 10],
            ['left' => 'HTML', 'right' => 'Lenguaje de marcado', 'score' => 5],
        ]);
    }

    #[Test]
    public function el_maestro_genera_la_actividad_desde_el_formulario(): void
    {
        $activity = $this->makeActivity();

        $response = $this->post(route('teacher.matchings.store'), [
            'activity_id' => $activity->id,
            'items' => [
                ['left' => 'HTTP', 'right' => 'Protocolo de transferencia', 'score' => 10],
                ['left' => 'HTML', 'right' => 'Lenguaje de marcado', 'score' => 5],
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('matchings', ['activity_id' => $activity->id]);
        $this->assertSame(2, $activity->matching->items()->count());
    }

    #[Test]
    public function la_actividad_no_se_genera_sin_parejas(): void
    {
        $activity = $this->makeActivity();

        $response = $this->post(route('teacher.matchings.store'), [
            'activity_id' => $activity->id,
            'items' => [],
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('matchings', 0);
    }

    #[Test]
    public function regenerar_la_actividad_reemplaza_las_parejas_anteriores(): void
    {
        $activity = $this->makeActivity();
        $this->buildDefaultMatching($activity);

        $this->post(route('teacher.matchings.store'), [
            'activity_id' => $activity->id,
            'items' => [['left' => 'CSS', 'right' => 'Hoja de estilos', 'score' => 10]],
        ]);

        $this->assertDatabaseCount('matchings', 1);
        $this->assertSame('CSS', $activity->matching->items()->firstOrFail()->left_text);
    }

    #[Test]
    public function el_estudiante_responde_una_pareja_correcta(): void
    {
        $activity = $this->makeActivity();
        $matching = $this->buildDefaultMatching($activity);
        $item = $matching->items()->first();

        $this->get(route('matchings.play', $matching))
            ->assertOk()
            ->assertSee('HTTP')
            ->assertSee('HTML');

        $response = $this->postJson(route('matchings.answer', $matching), [
            'matching_item_id' => $item->id,
            'response' => $item->right_text,
        ]);

        $response->assertOk();
        $response->assertJson([
            'correct' => true,
            'already_answered' => false,
            'left' => 'HTTP',
            'right' => 'Protocolo de transferencia',
            'score' => 10,
        ]);

        $this->assertDatabaseCount('matching_answers', 1);
        $this->assertDatabaseHas('matching_answers', ['is_correct' => true, 'score' => 10]);
    }

    #[Test]
    public function no_se_puntua_dos_veces_la_misma_pareja(): void
    {
        $matching = $this->buildDefaultMatching($this->makeActivity());
        $item = $matching->items()->first();

        $this->postJson(route('matchings.answer', $matching), [
            'matching_item_id' => $item->id,
            'response' => $item->right_text,
        ])->assertJson(['correct' => true, 'already_answered' => false]);

        $this->postJson(route('matchings.answer', $matching), [
            'matching_item_id' => $item->id,
            'response' => $item->right_text,
        ])->assertJson(['correct' => true, 'already_answered' => true, 'score' => 0]);

        $this->assertDatabaseCount('matching_answers', 1);
    }

    #[Test]
    public function se_marca_incorrecta_una_pareja_no_correspondiente(): void
    {
        $matching = $this->buildDefaultMatching($this->makeActivity());
        $item = $matching->items()->first();
        $wrongRight = $matching->items()->skip(1)->first()->right_text;

        $response = $this->postJson(route('matchings.answer', $matching), [
            'matching_item_id' => $item->id,
            'response' => $wrongRight,
        ]);

        $response->assertJson(['correct' => false]);
        $this->assertDatabaseHas('matching_answers', ['is_correct' => false, 'score' => 0]);
    }

    #[Test]
    public function se_rechaza_un_item_de_otra_actividad(): void
    {
        $matching = $this->buildDefaultMatching($this->makeActivity());
        $other = $this->buildDefaultMatching($this->makeActivity());

        $response = $this->postJson(route('matchings.answer', $matching), [
            'matching_item_id' => $other->items()->first()->id,
            'response' => 'cualquier cosa',
        ]);

        $response->assertJson(['correct' => false]);
        $this->assertDatabaseCount('matching_answers', 0);
    }
}