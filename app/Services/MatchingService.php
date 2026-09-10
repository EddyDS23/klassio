<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Matching;
use App\Models\MatchingAnswer;
use App\Models\MatchingItem;
use App\Models\Participation;

class MatchingService
{
    public function buildMatching(Activity $activity, array $items): Matching
    {
        $normalized = array_map(
            fn (array $item) => [
                'left_text' => $this->normalize($item['left']),
                'right_text' => $this->normalize($item['right']),
                'score' => max(1, (int) ($item['score'] ?? 1)),
            ],
            $items
        );

        $matching = $activity->matching()->updateOrCreate([], []);

        $matching->items()->delete();

        foreach ($normalized as $item) {
            $matching->items()->create($item);
        }

        return $matching;
    }

    /**
     * Puntaje máximo posible para una actividad matching.
     */
    public function maxScore(Matching $matching): int
    {
        return (int) $matching->items()->sum('score');
    }

    /**
     * Puntaje acumulado por una participación.
     */
    public function earnedScore(
        Matching $matching,
        Participation $participation
    ): int {
        return (int) MatchingAnswer::where(
            'participation_id',
            $participation->id
        )
            ->where('is_correct', true)
            ->whereIn(
                'matching_item_id',
                $matching->items()->pluck('id')
            )
            ->sum('score');
    }

    /**
     * Ítems ya respondidos correctamente.
     */
    public function correctItemIds(
        Matching $matching,
        Participation $participation
    ): array {
        return MatchingAnswer::where(
            'participation_id',
            $participation->id
        )
            ->where('is_correct', true)
            ->whereIn(
                'matching_item_id',
                $matching->items()->pluck('id')
            )
            ->pluck('matching_item_id')
            ->all();
    }

    /**
     * Comprueba la respuesta del estudiante.
     *
     * Si la respuesta completa todos los pares correctamente,
     * la participación se marca como completed.
     */
    public function checkAnswer(
        Matching $matching,
        Participation $participation,
        int $matchingItemId,
        string $response
    ): array {
        /*
         * No permitir respuestas después de completar
         * la participación.
         */
        if ($participation->status !== 'started') {
            return [
                'correct' => false,
                'already_answered' => false,
                'left' => null,
                'right' => null,
                'score' => 0,
                'completed' => true,
                'error' => 'La participación ya ha finalizado.',
            ];
        }

        /*
         * Buscar el ítem y asegurarnos de que pertenece
         * al matching actual.
         */
        $item = MatchingItem::query()
            ->where('matching_id', $matching->id)
            ->find($matchingItemId);

        if (! $item) {
            return $this->error(
                'Ese ítem no pertenece a esta actividad.'
            );
        }

        /*
         * Verificar si el par ya había sido acertado.
         */
        $correctIds = $this->correctItemIds(
            $matching,
            $participation
        );

        if (in_array($item->id, $correctIds, true)) {
            return [
                'correct' => true,
                'already_answered' => true,
                'left' => $item->left_text,
                'right' => $item->right_text,
                'score' => 0,
                'completed' => false,
                'error' => null,
            ];
        }

        /*
         * Validar la respuesta.
         */
        $isCorrect =
            $this->normalize($response) ===
            $this->normalize($item->right_text);

        /*
         * Registrar la respuesta.
         */
        $answer = MatchingAnswer::create([
            'participation_id' => $participation->id,
            'matching_item_id' => $item->id,
            'response' => $response,
            'is_correct' => $isCorrect,
            'score' => $isCorrect ? $item->score : 0,
            'answered_at' => now(),
        ]);

        /*
         * Si es incorrecta, no se modifica el score.
         */
        if (! $isCorrect) {
            return [
                'correct' => false,
                'already_answered' => false,
                'left' => $item->left_text,
                'right' => $item->right_text,
                'score' => 0,
                'completed' => false,
                'error' => 'Esa pareja no es correcta.',
            ];
        }

        /*
         * Actualizar el score acumulado.
         */
        $earnedScore = $this->earnedScore(
            $matching,
            $participation
        );

        $participation->update([
            'score' => $earnedScore,
        ]);

        /*
         * Comprobar si ya encontró todos los pares.
         */
        $totalItems = $matching->items()->count();

        $correctItems = count(
            $this->correctItemIds(
                $matching,
                $participation
            )
        );

        $completed = $totalItems > 0 &&
            $correctItems >= $totalItems;

        /*
         * Si completó todos los pares, cerrar
         * la participación.
         */
        if ($completed) {
            $participation->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        return [
            'correct' => true,
            'already_answered' => false,
            'left' => $item->left_text,
            'right' => $item->right_text,
            'score' => $answer->score,
            'completed' => $completed,
            'error' => null,
        ];
    }

    public function normalize(string $text): string
    {
        return trim($text);
    }

    /**
     * @return array{
     *     correct:false,
     *     already_answered:false,
     *     left:null,
     *     right:null,
     *     score:0,
     *     completed:false,
     *     error:string
     * }
     */
    protected function error(string $message): array
    {
        return [
            'correct' => false,
            'already_answered' => false,
            'left' => null,
            'right' => null,
            'score' => 0,
            'completed' => false,
            'error' => $message,
        ];
    }
}