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

    public const DIRECTION_DIAGONAL = 'diagonal';

    public const DIRECTION_HORIZONTAL_REVERSE = 'horizontal_reverse';

    public const DIRECTION_VERTICAL_REVERSE = 'vertical_reverse';

    public const DIRECTION_DIAGONAL_REVERSE = 'diagonal_reverse';

    public const DIRECTION_DIAGONAL_ALT = 'diagonal_alt';

    public const DIRECTION_DIAGONAL_ALT_REVERSE = 'diagonal_alt_reverse';

    /**
     * Desplazamientos (fila, columna) por dirección, tomando a row/column
     * como el inicio de la palabra (primera letra).
     *
     * @var array<string, array{0:int,1:int}>
     */
    private const DIRECTION_DELTAS = [
        self::DIRECTION_HORIZONTAL => [0, 1],
        self::DIRECTION_VERTICAL => [1, 0],
        self::DIRECTION_DIAGONAL => [1, 1],
        self::DIRECTION_HORIZONTAL_REVERSE => [0, -1],
        self::DIRECTION_VERTICAL_REVERSE => [-1, 0],
        self::DIRECTION_DIAGONAL_REVERSE => [-1, -1],
        self::DIRECTION_DIAGONAL_ALT => [1, -1],
        self::DIRECTION_DIAGONAL_ALT_REVERSE => [-1, 1],
    ];

    private const DIRECTION_LABELS = [
        self::DIRECTION_HORIZONTAL => 'Horizontal →',
        self::DIRECTION_VERTICAL => 'Vertical ↓',
        self::DIRECTION_DIAGONAL => 'Diagonal ↘',
        self::DIRECTION_HORIZONTAL_REVERSE => 'Horizontal ← (al revés)',
        self::DIRECTION_VERTICAL_REVERSE => 'Vertical ↑ (al revés)',
        self::DIRECTION_DIAGONAL_REVERSE => 'Diagonal ↖ (al revés)',
        self::DIRECTION_DIAGONAL_ALT => 'Diagonal ↙',
        self::DIRECTION_DIAGONAL_ALT_REVERSE => 'Diagonal ↗ (al revés)',
    ];

    /**
     * Genera un grid con las palabras colocadas en cualquiera de las
     * 8 direcciones (horizontal, vertical, diagonal y sus reversos).
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

            foreach ($this->wordOffsetCells($word, $row, $column, $direction) as [$r, $c, $letter]) {
                $grid[$r][$c] = $letter;
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
     * - cells (posiciones de la palabra)
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
            return $this->error('Debes seleccionar celdas en línea recta (horizontal, vertical o diagonal).');
        }

        [$selectedSorted] = [$this->sortCells($selected)];
        $alreadyFound = $this->foundWords($wordsearch, $participation);

        foreach ($wordsearch->words as $word) {
            if ($selectedSorted !== $this->sortCells($this->wordCells($word))) {
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
     * Códigos de dirección soportados.
     *
     * @return list<string>
     */
    public function directions(): array
    {
        return array_keys(self::DIRECTION_DELTAS);
    }

    public function directionLabel(string $direction): string
    {
        return self::DIRECTION_LABELS[$direction] ?? $direction;
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
     * @return array{0:int,1:int}
     */
    public function directionDelta(string $direction): array
    {
        return self::DIRECTION_DELTAS[$direction] ?? [0, 1];
    }

    /**
     * Celdas con su letra, en el orden en que se lee la palabra.
     *
     * @return list<array{0:int,1:int,2:string}>
     */
    private function wordOffsetCells(string $word, int $row, int $column, string $direction): array
    {
        [$dr, $dc] = $this->directionDelta($direction);
        $cells = [];

        foreach ($this->letters($word) as $offset => $letter) {
            $cells[] = [$row + $dr * $offset, $column + $dc * $offset, $letter];
        }

        return $cells;
    }

    /**
     * Busca una posición libre para la palabra. Devuelve [row, column, direction] o null.
     */
    protected function placeWord(array &$grid, string $word, int $rows, int $columns): ?array
    {
        $directions = array_keys(self::DIRECTION_DELTAS);
        shuffle($directions);

        foreach ($directions as $direction) {
            $candidates = $this->candidateStarts($word, $direction, $rows, $columns);
            shuffle($candidates);

            foreach ($candidates as [$row, $column]) {
                if ($this->fits($grid, $word, $row, $column, $direction, $rows, $columns)) {
                    return [$row, $column, $direction];
                }
            }
        }

        return null;
    }

    /**
     * Posiciones de inicio posibles para una palabra en una dirección.
     *
     * @return list<array{0:int,1:int}>
     */
    private function candidateStarts(string $word, string $direction, int $rows, int $columns): array
    {
        [$dr, $dc] = $this->directionDelta($direction);
        $length = count($this->letters($word));
        $candidates = [];

        $rowStart = $dr > 0 ? 0 : ($dr < 0 ? ($length - 1) : 0);
        $rowEnd = $dr > 0 ? $rows - 1 - ($length - 1) * $dr : ($dr < 0 ? $rows - 1 : $rows - 1);
        $columnStart = $dc > 0 ? 0 : ($dc < 0 ? ($length - 1) : 0);
        $columnEnd = $dc > 0 ? $columns - 1 - ($length - 1) * $dc : ($dc < 0 ? $columns - 1 : $columns - 1);

        for ($row = $rowStart; $row <= $rowEnd; $row++) {
            for ($column = $columnStart; $column <= $columnEnd; $column++) {
                $candidates[] = [$row, $column];
            }
        }

        return $candidates;
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
        foreach ($this->wordOffsetCells($word, $row, $column, $direction) as [$r, $c, $letter]) {
            if ($r < 0 || $r >= $rows || $c < 0 || $c >= $columns) {
                return false;
            }

            $cell = $grid[$r][$c];

            if ($cell !== null && $cell !== $letter) {
                return false;
            }
        }

        return true;
    }

    /**
     * Celdas cubiertas por la selección del estudiante, o null si no es
     * una línea recta válida (horizontal, vertical o diagonal).
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

        $dr = $endRow - $startRow;
        $dc = $endColumn - $startColumn;

        if ($dr !== 0 && $dc !== 0 && abs($dr) !== abs($dc)) {
            return null;
        }

        $stepRow = $dr === 0 ? 0 : ($dr > 0 ? 1 : -1);
        $stepColumn = $dc === 0 ? 0 : ($dc > 0 ? 1 : -1);
        $steps = max(abs($dr), abs($dc));

        $cells = [];

        for ($i = 0; $i <= $steps; $i++) {
            $cells[] = [$startRow + $stepRow * $i, $startColumn + $stepColumn * $i];
        }

        return $cells;
    }

    /**
     * Celdas que ocupa una palabra, en el orden en que se lee.
     *
     * @return list<array{0:int,1:int}>
     */
    protected function wordCells(Word $word): array
    {
        [$dr, $dc] = $this->directionDelta($word->direction);
        $cells = [];
        $length = mb_strlen($this->normalize($word->word));

        for ($i = 0; $i < $length; $i++) {
            $cells[] = [$word->row + $dr * $i, $word->column + $dc * $i];
        }

        return $cells;
    }

    /**
     * Ordena celdas por fila y columna para comparar conjuntos.
     *
     * @param list<array{0:int,1:int}> $cells
     *
     * @return list<array{0:int,1:int}>
     */
    private function sortCells(array $cells): array
    {
        usort($cells, static fn (array $a, array $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);

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