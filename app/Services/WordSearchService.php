<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Participation;
use App\Models\Word;
use App\Models\Wordsearch;
use App\Models\WordsearchAnswer;

class WordSearchService
{
    public const DIRECTION_HORIZONTAL = 'horizontal';

    public const DIRECTION_VERTICAL = 'vertical';

    /**
     * Genera un grid con las palabras colocadas en horizontal o vertical.
     *
     * Devuelve la estructura lista para persistir:
     * [
     *   'grid'       => matriz rows x columns de letras,
     *   'placements' => filas de Word a crear,
     *   'failed'     => palabras que no se pudieron colocar,
     * ]
     *
     * @throws \InvalidArgumentException si las dimensiones son inválidas.
     */
    public function generate(int $rows, int $columns, array $words): array
    {
        if ($rows < 2 || $columns < 2) {
            throw new \InvalidArgumentException('El grid debe tener al menos 2 filas y 2 columnas.');
        }

        if ($rows > 30 || $columns > 30) {
            throw new \InvalidArgumentException('El grid no puede superar 30 filas o columnas.');
        }

        $grid = array_fill(0, $rows, array_fill(0, $columns, null));

        $entries = array_values(array_map(
            static fn (array $entry) => [
                'word' => (string) ($entry['word'] ?? ''),
                'score' => max(1, (int) ($entry['score'] ?? 1)),
            ],
            $words
        ));

        // Las palabras más largas primero para maximizar la probabilidad de colocación.
        usort($entries, static fn (array $a, array $b) => mb_strlen($b['word']) <=> mb_strlen($a['word']));

        $placements = [];
        $failed = [];

        foreach ($entries as $entry) {
            $word = $this->normalize($entry['word']);

            if ($word === '') {
                continue;
            }

            $maxLength = max($rows, $columns);

            if (mb_strlen($word) > $maxLength) {
                $failed[] = ['word' => $entry['word'], 'reason' => 'too_long'];

                continue;
            }

            $placed = $this->placeWord($grid, $word, $rows, $columns);

            if ($placed === null) {
                $failed[] = ['word' => $entry['word'], 'reason' => 'no_space'];

                continue;
            }

            [$row, $column, $direction] = $placed;

            $letters = $this->letters($word);

            foreach ($letters as $offset => $letter) {
                if ($direction === self::DIRECTION_HORIZONTAL) {
                    $grid[$row][$column + $offset] = $letter;
                } else {
                    $grid[$row + $offset][$column] = $letter;
                }
            }

            $placements[] = [
                'word' => $entry['word'],
                'row' => $row,
                'column' => $column,
                'direction' => $direction,
                'score' => $entry['score'],
            ];
        }

        foreach ($grid as $r => $rowCells) {
            foreach ($rowCells as $c => $cell) {
                if ($cell === null) {
                    $grid[$r][$c] = $this->randomLetter();
                }
            }
        }

        return [
            'grid' => $grid,
            'placements' => $placements,
            'failed' => $failed,
        ];
    }

    /**
     * Persiste (o regenera) una sopa de letras para una actividad.
     */
    public function buildWordsearch(Activity $activity, int $rows, int $columns, array $words): Wordsearch
    {
        $result = $this->generate($rows, $columns, $words);

        if ($result['placements'] === []) {
            throw new \InvalidArgumentException('Ninguna palabra pudo ser colocada en el grid.');
        }

        $wordsearch = $activity->wordsearch()->updateOrCreate(
            [],
            [
                'rows' => $rows,
                'columns' => $columns,
                'grid' => $result['grid'],
            ]
        );

        $wordsearch->words()->delete();

        foreach ($result['placements'] as $placement) {
            $wordsearch->words()->create($placement);
        }

        return $wordsearch;
    }

    /**
     * Palabras ya encontradas por una participación.
     */
    public function foundWords(Wordsearch $wordsearch, Participation $participation): array
    {
        return WordsearchAnswer::where('participation_id', $participation->id)
            ->whereIn('word_id', $wordsearch->words()->pluck('id'))
            ->pluck('word_id')
            ->all();
    }

    /**
     * Comprueba una selección del estudiante y, si es correcta, registra la respuesta.
     *
     * Devuelve un array con:
     * - correct
     * - already_found
     * - word (original, si fue correcta)
     * - score (acumulado por esta palabra)
     * - cells (posiciones canónicas de la palabra)
     * - error (motivo si no fue correcta)
     */
    public function checkAnswer(
        Wordsearch $wordsearch,
        Participation $participation,
        int $startRow,
        int $startColumn,
        int $endRow,
        int $endColumn
    ): array {
        $grid = $wordsearch->grid;

        $selected = $this->selectionCells($grid, $startRow, $startColumn, $endRow, $endColumn);

        if ($selected === null) {
            return $this->error('Debes seleccionar celdas en línea recta (horizontal o vertical).');
        }

        $alreadyFound = $this->foundWords($wordsearch, $participation);

        foreach ($wordsearch->words as $word) {
            if ($selected !== $this->wordCells($word)) {
                continue;
            }

            if (in_array($word->id, $alreadyFound, true)) {
                return [
                    'correct' => true,
                    'already_found' => true,
                    'word' => $word->word,
                    'score' => 0,
                    'cells' => $selected,
                    'error' => null,
                ];
            }

            $answer = WordsearchAnswer::create([
                'participation_id' => $participation->id,
                'word_id' => $word->id,
                'score' => $word->score,
                'found_at' => now(),
            ]);

            return [
                'correct' => true,
                'already_found' => false,
                'word' => $word->word,
                'score' => $answer->score,
                'cells' => $selected,
                'error' => null,
            ];
        }

        return $this->error('Esa selección no corresponde a ninguna palabra.');
    }

    public function normalize(string $word): string
    {
        return mb_strtoupper(trim($word));
    }

    /**
     * @return list<string>
     */
    protected function letters(string $word): array
    {
        $letters = [];
        $length = mb_strlen($word);

        for ($i = 0; $i < $length; $i++) {
            $letters[] = mb_substr($word, $i, 1);
        }

        return $letters;
    }

    protected function randomLetter(): string
    {
        return mb_chr(rand(65, 90));
    }

    /**
     * Busca una posición libre para la palabra. Devuelve [row, column, direction] o null.
     */
    protected function placeWord(array &$grid, string $word, int $rows, int $columns): ?array
    {
        foreach ([self::DIRECTION_HORIZONTAL, self::DIRECTION_VERTICAL] as $direction) {
            for ($row = 0; $row < $rows; $row++) {
                for ($column = 0; $column < $columns; $column++) {
                    if ($this->fits($grid, $word, $row, $column, $direction, $rows, $columns)) {
                        return [$row, $column, $direction];
                    }
                }
            }
        }

        return null;
    }

    protected function fits(
        array $grid,
        string $word,
        int $row,
        int $column,
        string $direction,
        int $rows,
        int $columns
    ): bool {
        $letters = $this->letters($word);
        $length = count($letters);

        if ($direction === self::DIRECTION_HORIZONTAL) {
            if ($column + $length > $columns) {
                return false;
            }

            foreach ($letters as $offset => $letter) {
                $cell = $grid[$row][$column + $offset];
                if ($cell !== null && $cell !== $letter) {
                    return false;
                }
            }

            return true;
        }

        if ($row + $length > $rows) {
            return false;
        }

        foreach ($letters as $offset => $letter) {
            $cell = $grid[$row + $offset][$column];
            if ($cell !== null && $cell !== $letter) {
                return false;
            }
        }

        return true;
    }

    /**
     * Celdas cubiertas por la selección del estudiante, o null si no es recta/válida.
     *
     * @return list<array{0:int,1:int}>|null
     */
    protected function selectionCells(array $grid, int $startRow, int $startColumn, int $endRow, int $endColumn): ?array
    {
        $rows = count($grid);
        $columns = count($grid[0] ?? []);

        if (
            $startRow < 0 || $startRow >= $rows ||
            $startColumn < 0 || $startColumn >= $columns ||
            $endRow < 0 || $endRow >= $rows ||
            $endColumn < 0 || $endColumn >= $columns
        ) {
            return null;
        }

        if ($startRow === $endRow && $startColumn === $endColumn) {
            return [[$startRow, $startColumn]];
        }

        $cells = [];

        if ($startRow === $endRow) {
            $min = min($startColumn, $endColumn);
            $max = max($startColumn, $endColumn);

            for ($c = $min; $c <= $max; $c++) {
                $cells[] = [$startRow, $c];
            }
        } elseif ($startColumn === $endColumn) {
            $min = min($startRow, $endRow);
            $max = max($startRow, $endRow);

            for ($r = $min; $r <= $max; $r++) {
                $cells[] = [$r, $startColumn];
            }
        } else {
            return null;
        }

        return $cells;
    }

    /**
     * Celdas canónicas (ordenadas fila/columna) que ocupa una palabra.
     *
     * @return list<array{0:int,1:int}>
     */
    protected function wordCells(Word $word): array
    {
        $cells = [];
        $length = mb_strlen($this->normalize($word->word));

        if ($word->direction === self::DIRECTION_HORIZONTAL) {
            for ($i = 0; $i < $length; $i++) {
                $cells[] = [$word->row, $word->column + $i];
            }
        } else {
            for ($i = 0; $i < $length; $i++) {
                $cells[] = [$word->row + $i, $word->column];
            }
        }

        return $cells;
    }

    /**
     * @return array{correct:false,already_found:false,word:null,score:0,cells:[],error:string}
     */
    protected function error(string $message): array
    {
        return [
            'correct' => false,
            'already_found' => false,
            'word' => null,
            'score' => 0,
            'cells' => [],
            'error' => $message,
        ];
    }
}