<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Word;
use App\Services\WordSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WordSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    private WordSearchService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(WordSearchService::class);
    }

    private function makeActivity(): Activity
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $class = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Clase demo',
            'code' => 'WST' . strtoupper(uniqid()),
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

    private function makeParticipation(Activity $activity): Participation
    {
        return Participation::create([
            'activity_id' => $activity->id,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);
    }

    #[Test]
    public function genera_un_grid_con_las_dimensiones_pedidas(): void
    {
        $result = $this->service->generate(10, 8, [['word' => 'HTTP', 'score' => 10]]);

        $this->assertCount(10, $result['grid']);
        $this->assertCount(8, $result['grid'][0]);
    }

    #[Test]
    public function coloca_palabras_en_las_8_direcciones_soportadas(): void
    {
        $result = $this->service->generate(15, 15, [
            ['word' => 'API', 'score' => 10],
            ['word' => 'PHP', 'score' => 5],
            ['word' => 'LARAVEL', 'score' => 10],
        ]);

        $this->assertNotEmpty($result['placements']);

        foreach ($result['placements'] as $placement) {
            $this->assertContains($placement['direction'], $this->service->directions());
        }
    }

    #[Test]
    public function coloca_las_palabras_legibles_en_el_grid(): void
    {
        $result = $this->service->generate(15, 15, [
            ['word' => 'API', 'score' => 10],
            ['word' => 'PHP', 'score' => 5],
            ['word' => 'LARAVEL', 'score' => 10],
        ]);

        $this->assertSame([], $result['failed']);

        foreach ($result['placements'] as $placement) {
            $read = '';

            [$dr, $dc] = $this->service->directionDelta($placement['direction']);
            $word = $placement['word'];
            $length = mb_strlen($word);

            for ($i = 0; $i < $length; $i++) {
                $read .= $result['grid'][$placement['row'] + $dr * $i][$placement['column'] + $dc * $i];
            }

            $this->assertSame($word, $read);
        }
    }

    #[Test]
    public function coloca_todas_las_palabras(): void
    {
        $words = [
            ['word' => 'HTTP', 'score' => 10],
            ['word' => 'LARAVEL', 'score' => 10],
            ['word' => 'API', 'score' => 10],
            ['word' => 'PHP', 'score' => 10],
            ['word' => 'MYSQL', 'score' => 10],
        ];

        $result = $this->service->generate(12, 12, $words);

        $placed = collect($result['placements'])->pluck('word')->sort()->values()->all();

        $this->assertSame(['API', 'HTTP', 'LARAVEL', 'MYSQL', 'PHP'], $placed);
        $this->assertSame([], $result['failed']);
    }

    #[Test]
    public function la_palabra_colocada_se_refleja_en_el_grid(): void
    {
        $result = $this->service->generate(5, 5, [
            ['word' => 'HOLA', 'score' => 10],
        ]);

        $placement = $result['placements'][0];
        [$dr, $dc] = $this->service->directionDelta($placement['direction']);

        $rowLetters = [];
        $length = mb_strlen($placement['word']);

        for ($i = 0; $i < $length; $i++) {
            $rowLetters[] = $result['grid'][$placement['row'] + $dr * $i][$placement['column'] + $dc * $i];
        }

        $this->assertSame(['H', 'O', 'L', 'A'], $rowLetters);
    }

    #[Test]
    public function rellena_las_posiciones_restantes_con_letras_aleatorias(): void
    {
        $result = $this->service->generate(4, 4, [['word' => 'HOLA', 'score' => 10]]);

        $letters = array_merge(...$result['grid']);

        $this->assertCount(16, $letters);
        $this->assertTrue(collect($letters)->every(fn ($letter) => preg_match('/^[A-Z]$/', $letter) === 1));
    }

    #[Test]
    public function rechaza_palabras_mas_largas_que_el_grid(): void
    {
        $result = $this->service->generate(3, 3, [['word' => 'LARAVELPHP', 'score' => 10]]);

        $this->assertSame([], $result['placements']);
        $this->assertCount(1, $result['failed']);
        $this->assertSame('too_long', $result['failed'][0]['reason']);
    }

    #[Test]
    public function rechaza_un_grid_invalido(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->generate(1, 10, [['word' => 'HTTP', 'score' => 10]]);
    }

    #[Test]
    public function normaliza_palabras_a_mayusculas(): void
    {
        $this->assertSame('LARAVEL', $this->service->normalize(' laravel '));
    }

    #[Test]
    public function build_wordsearch_persiste_grid_y_coordenadas(): void
    {
        $activity = $this->makeActivity();

        $wordsearch = $this->service->buildWordsearch($activity, 10, 10, [
            ['word' => 'HTTP', 'score' => 10],
            ['word' => 'MYSQL', 'score' => 8],
        ]);

        $words = $wordsearch->words;

        $this->assertCount(2, $words);
        $this->assertSame(10, $wordsearch->rows);
        $this->assertSame(10, $wordsearch->columns);
        $this->assertCount(10, $wordsearch->grid);

        foreach ($words as $word) {
            $this->assertContains($word->direction, $this->service->directions());
            $this->assertNotNull($word->row);
            $this->assertNotNull($word->column);
        }
    }

    #[Test]
    public function detecta_una_palabra_correcta_en_cualquier_direccion(): void
    {
        $activity = $this->makeActivity();

        $wordsearch = $this->service->buildWordsearch($activity, 10, 10, [
            ['word' => 'HTTP', 'score' => 10],
            ['word' => 'LARAVEL', 'score' => 10],
            ['word' => 'MYSQL', 'score' => 10],
        ]);

        $participation = $this->makeParticipation($activity);

        foreach ($wordsearch->words as $word) {
            $result = $this->service->checkAnswer(
                $wordsearch,
                $participation,
                $word->row,
                $word->column,
                $this->endRow($word),
                $this->endColumn($word)
            );

            $this->assertTrue($result['correct']);
            $this->assertSame($this->normalizeWord($word), $result['word']);
            $this->assertSame($word->score, $result['score']);
        }
    }

    #[Test]
    public function detecta_selecciones_inversas(): void
    {
        $activity = $this->makeActivity();

        $wordsearch = $this->service->buildWordsearch($activity, 10, 10, [
            ['word' => 'HTTP', 'score' => 10],
        ]);

        $word = $wordsearch->words->first();
        $participation = $this->makeParticipation($activity);

        $result = $this->service->checkAnswer(
            $wordsearch,
            $participation,
            $this->endRow($word),
            $this->endColumn($word),
            $word->row,
            $word->column
        );

        $this->assertTrue($result['correct']);
        $this->assertSame($this->normalizeWord($word), $result['word']);
    }

    #[Test]
    public function acepta_una_seleccion_diagonal_cuando_corresponde_a_una_palabra(): void
    {
        $activity = $this->makeActivity();

        $wordsearch = $this->service->buildWordsearch($activity, 12, 12, [
            ['word' => 'VARIABLE', 'score' => 10],
            ['word' => 'DIAGONAL', 'score' => 10],
            ['word' => 'BLOQUE', 'score' => 10],
        ]);

        $word = $wordsearch->words->first();
        $participation = $this->makeParticipation($activity);

        $result = $this->service->checkAnswer(
            $wordsearch,
            $participation,
            $word->row,
            $word->column,
            $this->endRow($word),
            $this->endColumn($word)
        );

        $this->assertTrue($result['correct']);
    }

    #[Test]
    public function rechaza_coordenadas_incorrectas(): void
    {
        $activity = $this->makeActivity();

        $wordsearch = $this->service->buildWordsearch($activity, 10, 10, [
            ['word' => 'HTTP', 'score' => 10],
        ]);

        $word = $wordsearch->words->first();
        $participation = $this->makeParticipation($activity);

        $result = $this->service->checkAnswer(
            $wordsearch,
            $participation,
            $word->row,
            $word->column,
            $this->endRow($word),
            $this->endColumn($word) + 1
        );

        $this->assertFalse($result['correct']);
        $this->assertNotNull($result['error']);
    }

    #[Test]
    public function rechaza_una_seleccion_en_zigzag(): void
    {
        $activity = $this->makeActivity();

        $wordsearch = $this->service->buildWordsearch($activity, 10, 10, [
            ['word' => 'HTTP', 'score' => 10],
        ]);

        $participation = $this->makeParticipation($activity);

        $result = $this->service->checkAnswer($wordsearch, $participation, 1, 1, 3, 2);

        $this->assertFalse($result['correct']);
    }

    #[Test]
    public function evitar_doble_puntuacion(): void
    {
        $activity = $this->makeActivity();

        $wordsearch = $this->service->buildWordsearch($activity, 10, 10, [
            ['word' => 'HTTP', 'score' => 10],
        ]);

        $word = $wordsearch->words->first();
        $participation = $this->makeParticipation($activity);

        $answer = function () use ($wordsearch, $participation, $word) {
            return $this->service->checkAnswer(
                $wordsearch,
                $participation,
                $word->row,
                $word->column,
                $this->endRow($word),
                $this->endColumn($word)
            );
        };

        $first = $answer();
        $second = $answer();

        $this->assertTrue($first['correct']);
        $this->assertFalse($first['already_found']);

        $this->assertTrue($second['already_found']);
        $this->assertSame(0, $second['score']);

        $this->assertDatabaseCount('wordsearch_answers', 1);
    }

    private function normalizeWord(Word $word): string
    {
        return mb_strtoupper(trim($word->word));
    }

    private function endRow(Word $word): int
    {
        [$dr, $dc] = $this->service->directionDelta($word->direction);

        return $word->row + $dr * (mb_strlen($word->word) - 1);
    }

    private function endColumn(Word $word): int
    {
        [$dr, $dc] = $this->service->directionDelta($word->direction);

        return $word->column + $dc * (mb_strlen($word->word) - 1);
    }
}