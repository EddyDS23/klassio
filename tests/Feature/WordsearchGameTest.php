<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\WordSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WordsearchGameTest extends TestCase
{
    use RefreshDatabase;

    private WordSearchService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(WordSearchService::class);

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
            'code' => 'WS' . strtoupper(uniqid()),
        ]);

        return Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Sopa demo',
            'type' => 'word_search',
            'mode' => 'individual',
            'max_score' => 100,
            'time_limit' => 300,
            'status' => 'draft',
        ]);
    }

    private function correctPayload(Activity $activity): array
    {
        $wordsearch = $activity->wordsearch;
        $word = $wordsearch->words->first();

        return [
            'wordsearch' => $wordsearch,
            'payload' => [
                'wordsearch_id' => $wordsearch->id,
                'start_row' => $word->row,
                'start_column' => $word->column,
                'end_row' => $word->direction === 'horizontal' ? $word->row : $word->row + mb_strlen($word->word) - 1,
                'end_column' => $word->direction === 'horizontal' ? $word->column + mb_strlen($word->word) - 1 : $word->column,
            ],
        ];
    }

    private function payload(): array
    {
        return [
            'rows' => 10,
            'columns' => 10,
            'words' => [
                ['word' => 'HTTP', 'score' => 10],
                ['word' => 'LARAVEL', 'score' => 10],
                ['word' => 'API', 'score' => 5],
                ['word' => 'PHP', 'score' => 5],
                ['word' => 'MYSQL', 'score' => 10],
            ],
        ];
    }

    #[Test]
    public function el_maestro_configura_una_sopa_desde_el_formulario(): void
    {
        $activity = $this->makeActivity();

        $this->get(route('teacher.word-search.configure', $activity->id))
            ->assertOk()
            ->assertSee('Configurar Sopa de Letras');

        $response = $this->post(route('teacher.word-search.store', $activity->id), $this->payload());

        $response->assertRedirect();

        $this->assertDatabaseHas('wordsearches', ['activity_id' => $activity->id, 'rows' => 10, 'columns' => 10]);
        $this->assertSame(5, Activity::find($activity->id)->wordsearch->words()->count());
    }

    #[Test]
    public function la_sopa_no_se_genera_sin_palabras(): void
    {
        $activity = $this->makeActivity();

        $response = $this->post(route('teacher.word-search.store', $activity->id), [
            'rows' => 10,
            'columns' => 10,
            'words' => [],
        ]);

        $response->assertSessionHasErrors('words');
    }

    #[Test]
    public function actualizar_la_sopa_reemplaza_las_palabras_anteriores(): void
    {
        $activity = $this->makeActivity();

        $this->post(route('teacher.word-search.store', $activity->id), [
            'rows' => 10,
            'columns' => 10,
            'words' => [['word' => 'HTTP', 'score' => 10]],
        ]);

        $this->get(route('teacher.word-search.edit', $activity->id))
            ->assertOk()
            ->assertSee('Editar Sopa de Letras');

        $this->put(route('teacher.word-search.update', $activity->id), [
            'rows' => 10,
            'columns' => 10,
            'words' => [['word' => 'PHP', 'score' => 10]],
        ]);

        $this->assertDatabaseCount('wordsearches', 1);
        $this->assertSame('PHP', Activity::find($activity->id)->wordsearch->words()->firstOrFail()->word);
    }

    #[Test]
    public function el_estudiante_responde_una_palabra_correcta(): void
    {
        $activity = $this->makeActivity();
        $this->service->buildWordsearch($activity, 10, 10, [
            ['word' => 'HTTP', 'score' => 10],
        ]);

        $this->get(route('student.word-search.play', $activity->id))
            ->assertOk()
            ->assertSee('HTTP');

        ['wordsearch' => $wordsearch, 'payload' => $payload] = $this->correctPayload($activity);

        $response = $this->postJson(route('student.word-search.answer'), $payload);

        $response->assertOk();
        $response->assertJson([
            'correct' => true,
            'already_found' => false,
            'word' => 'HTTP',
            'score' => 10,
        ]);

        $this->assertDatabaseCount('wordsearch_answers', 1);
        $this->assertDatabaseHas('wordsearch_answers', ['score' => 10]);
    }

    #[Test]
    public function no_se_puntua_dos_veces_la_misma_palabra(): void
    {
        $activity = $this->makeActivity();
        $this->service->buildWordsearch($activity, 10, 10, [
            ['word' => 'HTTP', 'score' => 10],
        ]);

        ['wordsearch' => $wordsearch, 'payload' => $payload] = $this->correctPayload($activity);

        $this->postJson(route('student.word-search.answer'), $payload)
            ->assertJson(['correct' => true, 'already_found' => false]);

        $this->postJson(route('student.word-search.answer'), $payload)
            ->assertJson(['correct' => true, 'already_found' => true, 'score' => 0]);

        $this->assertDatabaseCount('wordsearch_answers', 1);
    }

    #[Test]
    public function rechaza_coordenadas_incorrectas(): void
    {
        $activity = $this->makeActivity();
        $this->service->buildWordsearch($activity, 10, 10, [
            ['word' => 'HTTP', 'score' => 10],
        ]);
        $wordsearch = $activity->wordsearch;

        $response = $this->postJson(route('student.word-search.answer'), [
            'wordsearch_id' => $wordsearch->id,
            'start_row' => 0,
            'start_column' => 0,
            'end_row' => 0,
            'end_column' => 2,
        ]);

        $response->assertJson(['correct' => false]);
        $this->assertDatabaseCount('wordsearch_answers', 0);
    }

    #[Test]
    public function rechaza_una_seleccion_diagonal(): void
    {
        $activity = $this->makeActivity();
        $this->service->buildWordsearch($activity, 10, 10, [
            ['word' => 'HTTP', 'score' => 10],
        ]);

        $response = $this->postJson(route('student.word-search.answer'), [
            'wordsearch_id' => $activity->wordsearch->id,
            'start_row' => 1,
            'start_column' => 1,
            'end_row' => 3,
            'end_column' => 3,
        ]);

        $response->assertJson(['correct' => false]);
        $this->assertDatabaseCount('wordsearch_answers', 0);
    }
}
