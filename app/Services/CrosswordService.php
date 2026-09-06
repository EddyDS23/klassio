<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Crossword;
use App\Models\CrosswordWord;

class CrosswordService
{
    // Tamaño máximo del grid
    private const MAX_SIZE = 20;

    // -------------------------------------------------------------------------
    // Públicos
    // -------------------------------------------------------------------------

    /**
     * Crea un nuevo crossword para la actividad dada.
     * Retorna un array con:
     *   'crossword' => Crossword
     *   'placed'    => string[]   palabras colocadas
     *   'skipped'   => string[]   palabras que no pudieron intersectarse
     */
    public function store(Activity $activity, array $data): array
    {
        ['grid' => $grid, 'placed' => $placed, 'skipped' => $skipped] =
            $this->buildGrid($data['words']);

        $rows    = count($grid);
        $columns = $rows > 0 ? count($grid[0]) : 0;

        $crossword = $activity->crossword;

        if (!$crossword) {
            throw new \RuntimeException(
                'La actividad no tiene una configuración de crucigrama.'
            );
        }

        $crossword->update([
            'rows'    => $rows,
            'columns' => $columns,
            'grid'    => $grid,
        ]);

        $this->savePlacedWords($crossword, $placed);

        return [
            'crossword' => $crossword->fresh(),
            'placed'    => array_column($placed, 'word'),
            'skipped'   => $skipped,
        ];
    }

    /**
     * Actualiza el crossword existente regenerando el grid y las palabras.
     */
    public function update(Crossword $crossword, array $data): array
    {
        ['grid' => $grid, 'placed' => $placed, 'skipped' => $skipped] =
            $this->buildGrid($data['words']);

        $rows    = count($grid);
        $columns = $rows > 0 ? count($grid[0]) : 0;

        $crossword->update([
            'rows'    => $rows,
            'columns' => $columns,
            'grid'    => $grid,
        ]);

        // Borrar palabras anteriores y reinsertar
        $crossword->words()->delete();
        $this->savePlacedWords($crossword, $placed);

        return [
            'crossword' => $crossword->fresh(),
            'placed'    => array_column($placed, 'word'),
            'skipped'   => $skipped,
        ];
    }

    /**
     * Valida la respuesta del estudiante contra la palabra correcta.
     * Case-insensitive, ignora espacios extremos.
     */
    public function validateAnswer(CrosswordWord $crosswordWord, string $response): bool
    {
        return strtolower(trim($response)) === strtolower(trim($crosswordWord->word));
    }

    // -------------------------------------------------------------------------
    // Construcción del grid
    // -------------------------------------------------------------------------

    /**
     * Algoritmo principal.
     * Recibe array de ['word'=>'...', 'clue'=>'...', 'score'=>N]
     * Retorna grid 2D, palabras colocadas con coordenadas, y palabras omitidas.
     */
    private function buildGrid(array $words): array
    {
        // Normalizar a minúsculas y ordenar de mayor a menor longitud
        $words = array_map(function ($w) {
            return [
                'word'  => strtolower(trim($w['word'])),
                'clue'  => $w['clue'],
                'score' => (int) $w['score'],
            ];
        }, $words);

        usort($words, fn($a, $b) => strlen($b['word']) - strlen($a['word']));

        // Grid interno: null = vacío, string = letra
        $grid = array_fill(0, self::MAX_SIZE, array_fill(0, self::MAX_SIZE, null));

        $placed  = [];
        $skipped = [];

        foreach ($words as $wordData) {
            $word = $wordData['word'];

            if (empty($placed)) {
                // Primera palabra: centrada horizontal
                $row    = (int) (self::MAX_SIZE / 2);
                $col    = (int) ((self::MAX_SIZE - strlen($word)) / 2);
                $dir    = 'horizontal';
                $grid   = $this->placeWord($grid, $word, $row, $col, $dir);
                $placed[] = array_merge($wordData, [
                    'row'       => $row,
                    'column'    => $col,
                    'direction' => $dir,
                ]);
                continue;
            }

            // Intentar intersectar con cada palabra ya colocada
            $wasPlaced = false;

            foreach ($placed as $placedWord) {
                $result = $this->tryIntersect($grid, $wordData, $placedWord);

                if ($result !== null) {
                    $grid     = $result['grid'];
                    $placed[] = array_merge($wordData, [
                        'row'       => $result['row'],
                        'column'    => $result['column'],
                        'direction' => $result['direction'],
                    ]);
                    $wasPlaced = true;
                    break;
                }
            }

            if (! $wasPlaced) {
                $skipped[] = $wordData['word'];
            }
        }

        // Recortar el grid para eliminar filas/columnas vacías en los bordes
        $grid = $this->trimGrid($grid);

        return [
            'grid'    => $grid,
            'placed'  => $placed,
            'skipped' => $skipped,
        ];
    }

    /**
     * Intenta encontrar una posición válida para $wordData intersectando con $placedWord.
     * Retorna null si no hay ninguna posición válida.
     */
    private function tryIntersect(array $grid, array $wordData, array $placedWord): ?array
    {
        $newWord     = $wordData['word'];
        $existingWord = $placedWord['word'];
        $existingDir  = $placedWord['direction'];
        $newDir       = $existingDir === 'horizontal' ? 'vertical' : 'horizontal';

        // Buscar letras en común
        for ($i = 0; $i < strlen($existingWord); $i++) {
            $letter = $existingWord[$i];

            for ($j = 0; $j < strlen($newWord); $j++) {
                if ($newWord[$j] !== $letter) {
                    continue;
                }

                // Coordenada de la letra compartida en el grid
                if ($existingDir === 'horizontal') {
                    $intersectRow = $placedWord['row'];
                    $intersectCol = $placedWord['column'] + $i;
                } else {
                    $intersectRow = $placedWord['row'] + $i;
                    $intersectCol = $placedWord['column'];
                }

                // Calcular inicio de la nueva palabra
                if ($newDir === 'horizontal') {
                    $newRow = $intersectRow;
                    $newCol = $intersectCol - $j;
                } else {
                    $newRow = $intersectRow - $j;
                    $newCol = $intersectCol;
                }

                if ($this->canPlace($grid, $newWord, $newRow, $newCol, $newDir)) {
                    $newGrid = $this->placeWord($grid, $newWord, $newRow, $newCol, $newDir);
                    return [
                        'grid'      => $newGrid,
                        'row'       => $newRow,
                        'column'    => $newCol,
                        'direction' => $newDir,
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Verifica si una palabra puede colocarse en la posición dada sin colisiones.
     */
    private function canPlace(array $grid, string $word, int $row, int $col, string $direction): bool
    {
        $len = strlen($word);

        // Verificar que cabe dentro del grid
        if ($direction === 'horizontal') {
            if ($col < 0 || $col + $len > self::MAX_SIZE) return false;
            if ($row < 0 || $row >= self::MAX_SIZE)        return false;
        } else {
            if ($row < 0 || $row + $len > self::MAX_SIZE) return false;
            if ($col < 0 || $col >= self::MAX_SIZE)        return false;
        }

        // Verificar celda anterior (no debe haber letra antes del inicio)
        if ($direction === 'horizontal') {
            if ($col > 0 && $grid[$row][$col - 1] !== null) return false;
            if ($col + $len < self::MAX_SIZE && $grid[$row][$col + $len] !== null) return false;
        } else {
            if ($row > 0 && $grid[$row - 1][$col] !== null) return false;
            if ($row + $len < self::MAX_SIZE && $grid[$row + $len][$col] !== null) return false;
        }

        // Verificar celda por celda
        for ($i = 0; $i < $len; $i++) {
            $r = $direction === 'horizontal' ? $row       : $row + $i;
            $c = $direction === 'horizontal' ? $col + $i  : $col;

            $current = $grid[$r][$c];

            if ($current === null) {
                // Celda vacía: verificar que no haya letras adyacentes paralelas
                if ($direction === 'horizontal') {
                    if ($r > 0 && $grid[$r - 1][$c] !== null) return false;
                    if ($r < self::MAX_SIZE - 1 && $grid[$r + 1][$c] !== null) return false;
                } else {
                    if ($c > 0 && $grid[$r][$c - 1] !== null) return false;
                    if ($c < self::MAX_SIZE - 1 && $grid[$r][$c + 1] !== null) return false;
                }
            } elseif ($current !== $word[$i]) {
                // Celda ocupada con letra distinta: colisión
                return false;
            }
            // Si $current === $word[$i]: intersección válida, ok
        }

        return true;
    }

    /**
     * Coloca la palabra en el grid y retorna el grid actualizado.
     * Asume que canPlace() ya fue verificado.
     */
    private function placeWord(array $grid, string $word, int $row, int $col, string $direction): array
    {
        for ($i = 0; $i < strlen($word); $i++) {
            $r = $direction === 'horizontal' ? $row      : $row + $i;
            $c = $direction === 'horizontal' ? $col + $i : $col;
            $grid[$r][$c] = $word[$i];
        }
        return $grid;
    }

    /**
     * Elimina filas y columnas vacías en los bordes del grid,
     * dejando un margen de 1 celda alrededor del contenido.
     * También actualiza las coordenadas de las palabras colocadas.
     */
    private function trimGrid(array $grid): array
    {
        $minRow = self::MAX_SIZE;
        $maxRow = 0;
        $minCol = self::MAX_SIZE;
        $maxCol = 0;

        for ($r = 0; $r < self::MAX_SIZE; $r++) {
            for ($c = 0; $c < self::MAX_SIZE; $c++) {
                if ($grid[$r][$c] !== null) {
                    $minRow = min($minRow, $r);
                    $maxRow = max($maxRow, $r);
                    $minCol = min($minCol, $c);
                    $maxCol = max($maxCol, $c);
                }
            }
        }

        // Si no hay nada colocado
        if ($minRow > $maxRow) {
            return [];
        }

        // Margen de 1
        $minRow = max(0, $minRow - 1);
        $minCol = max(0, $minCol - 1);
        $maxRow = min(self::MAX_SIZE - 1, $maxRow + 1);
        $maxCol = min(self::MAX_SIZE - 1, $maxCol + 1);

        $trimmed = [];
        for ($r = $minRow; $r <= $maxRow; $r++) {
            $trimmedRow = [];
            for ($c = $minCol; $c <= $maxCol; $c++) {
                $trimmedRow[] = $grid[$r][$c] ?? null;
            }
            $trimmed[] = $trimmedRow;
        }

        return $trimmed;
    }

    // -------------------------------------------------------------------------
    // Persistencia
    // -------------------------------------------------------------------------

    /**
     * Guarda las palabras colocadas en la base de datos.
     * Las coordenadas ya son relativas al grid recortado.
     * Necesitamos recalcularlas respecto al grid recortado.
     */
    private function savePlacedWords(Crossword $crossword, array $placed): void
    {
        // El trimGrid puede haber desplazado las coordenadas.
        // Recalculamos el offset usando la primera letra de la primera palabra
        // comparando con el grid guardado.
        $grid = $crossword->grid;

        // Encontrar el offset: buscamos en el grid la letra de la primera palabra
        // en su posición esperada. El offset es la diferencia.
        // Más simple: guardamos las palabras tal como vienen de placed[]
        // y ajustamos restando el minRow/minCol que usamos en trimGrid.
        // Como trimGrid ya corrió antes de llamar a este método,
        // recalculamos el offset buscando la primera letra de cada palabra en el grid.

        foreach ($placed as $wordData) {
            // Buscar la posición real en el grid recortado
            [$row, $col] = $this->findWordInGrid(
                $grid,
                $wordData['word'],
                $wordData['direction']
            );

            CrosswordWord::create([
                'crossword_id' => $crossword->id,
                'word'         => $wordData['word'],
                'clue'         => $wordData['clue'],
                'row'          => $row,
                'column'       => $col,
                'direction'    => $wordData['direction'],
                'score'        => $wordData['score'],
            ]);
        }
    }

    /**
     * Busca la posición inicial de una palabra en el grid recortado.
     * Retorna [row, col].
     */
    private function findWordInGrid(array $grid, string $word, string $direction): array
    {
        $rows = count($grid);
        $cols = $rows > 0 ? count($grid[0]) : 0;
        $len  = strlen($word);

        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                if ($grid[$r][$c] !== $word[0]) {
                    continue;
                }

                $match = true;
                for ($i = 1; $i < $len; $i++) {
                    $nr = $direction === 'horizontal' ? $r      : $r + $i;
                    $nc = $direction === 'horizontal' ? $c + $i : $c;

                    if ($nr >= $rows || $nc >= $cols || $grid[$nr][$nc] !== $word[$i]) {
                        $match = false;
                        break;
                    }
                }

                if ($match) {
                    return [$r, $c];
                }
            }
        }

        // Fallback (no debería ocurrir)
        return [0, 0];
    }
}
