<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Matching;
use App\Models\MatchingAnswer;
use App\Models\MatchingItem;
use App\Models\Participation;

class MatchingService
{
    /**
     * Persiste (o regenera) los pares de una actividad de tipo matching.
     */
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
     * Puntaje acumulado por una participación en una actividad matching.
     */
    public function earnedScore(Matching $matching, Participation $participation): int
    {
        return (int) MatchingAnswer::where('participation_id', $participation->id)
            ->where('is_correct', true)
            ->whereIn('matching_item_id', $matching->items()->pluck('id'))
            ->sum('score');
    }

    /**
     * Ítems ya respondidos CORRECTAMENTE por una participación (pares bloqueados).
     */
    public function correctItemIds(Matching $matching, Participation $participation): array
    {
        return MatchingAnswer::where('participation_id', $participation->id)
            ->where('is_correct', true)
            ->whereIn('matching_item_id', $matching->items()->pluck('id'))
            ->pluck('matching_item_id')
            ->all();
    }

    /**
     * Comprueba la respuesta del estudiante. Este envía el matching_item_id
     * (el concepto de la columna izquierda) y la opción elegida de la derecha
     * (response); el servidor valida contra la pareja guardada en la base.
     *
     * Los pares correctos quedan bloqueados (no se puntúan dos veces). Los
     * intentos incorrectos se registran para auditoría pero permiten reintento.
     *
     * Devuelve un array con:
     * - correct
     * - already_answered
     * - left / right (textos del ítem)
     * - score (puntaje otorgado o 0)
     * - error
     */
    public function checkAnswer(
        Matching $matching,
        Participation $participation,
        int $matchingItemId,
        string $response
    ): array {
        $item = MatchingItem::query()
            ->where('matching_id', $matching->id)
            ->find($matchingItemId);

        if (! $item) {
            return $this->error('Ese ítem no pertenece a esta actividad.');
        }

        $isCorrect = $this->normalize($response) === $item->right_text;
        $correctIds = $this->correctItemIds($matching, $participation);

        if ($isCorrect && in_array($item->id, $correctIds, true)) {
            return [
                'correct' => true,
                'already_answered' => true,
                'left' => $item->left_text,
                'right' => $item->right_text,
                'score' => 0,
                'error' => null,
            ];
        }

        $answer = MatchingAnswer::create([
            'participation_id' => $participation->id,
            'matching_item_id' => $item->id,
            'response' => $response,
            'is_correct' => $isCorrect,
            'score' => $isCorrect ? $item->score : 0,
            'answered_at' => now(),
        ]);

        if (! $isCorrect) {
            return [
                'correct' => false,
                'already_answered' => false,
                'left' => $item->left_text,
                'right' => $item->right_text,
                'score' => 0,
                'error' => 'Esa pareja no es correcta.',
            ];
        }

        return [
            'correct' => true,
            'already_answered' => false,
            'left' => $item->left_text,
            'right' => $item->right_text,
            'score' => $answer->score,
            'error' => null,
        ];
    }

    public function normalize(string $text): string
    {
        return trim($text);
    }

    /**
     * @return array{correct:false,already_answered:false,left:null,right:null,score:0,error:string}
     */
    protected function error(string $message): array
    {
        return [
            'correct' => false,
            'already_answered' => false,
            'left' => null,
            'right' => null,
            'score' => 0,
            'error' => $message,
        ];
    }
}