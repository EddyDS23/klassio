<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Participation;
use App\Models\Roulette;
use App\Models\RouletteAnswer;
use App\Models\RouletteItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RouletteService
{
    public function __construct(
        private ParticipationService $participationService
    ) {}

    private const OPTION_KEYS = ['a', 'b', 'c', 'd'];

    /**
     * Crea o reemplaza la configuración de la Ruleta.
     *
     * Los puntos de los ítems representan el peso interno
     * de cada pregunta. El puntaje final de la actividad
     * se determina mediante Activity.max_score.
     */
    public function buildRoulette(
        Activity $activity,
        array $items
    ): Roulette {
        return DB::transaction(function () use ($activity, $items) {
            $roulette = $activity->roulette()->updateOrCreate([], []);

            $roulette->items()->delete();

            foreach ($items as $item) {
                $roulette->items()->create(
                    $this->normalizeItem($item)
                );
            }

            return $roulette->fresh();
        });
    }

    /**
     * Puntaje interno máximo posible de la Ruleta.
     *
     * IMPORTANTE:
     * Este NO es el max_score de la Activity.
     * Sirve únicamente como base para que
     * ParticipationService::syncScore() calcule
     * el porcentaje obtenido.
     */
    public function maxScore(Roulette $roulette): int
    {
        return (int) $roulette->items()->sum('points');
    }

    /**
     * Puntaje interno acumulado por una participación.
     */
    public function earnedScore(
        Roulette $roulette,
        Participation $participation
    ): int {
        return (int) RouletteAnswer::where(
            'participation_id',
            $participation->id
        )
            ->where('is_correct', true)
            ->whereIn(
                'roulette_item_id',
                $roulette->items()->pluck('id')
            )
            ->sum('score');
    }

    /**
     * Ítems ya respondidos por la participación.
     */
    public function answeredItemIds(
        Roulette $roulette,
        Participation $participation
    ): array {
        return RouletteAnswer::where(
            'participation_id',
            $participation->id
        )
            ->whereIn(
                'roulette_item_id',
                $roulette->items()->pluck('id')
            )
            ->pluck('roulette_item_id')
            ->all();
    }

    /**
     * Ítems pendientes.
     */
    public function pendingItems(
        Roulette $roulette,
        Participation $participation
    ): Collection {
        $answered = $this->answeredItemIds(
            $roulette,
            $participation
        );

        return $roulette->items()
            ->when(
                $answered,
                fn ($q) => $q->whereNotIn('id', $answered)
            )
            ->orderBy('id')
            ->get();
    }

    /**
     * Selecciona un ítem aleatorio entre los pendientes.
     */
    public function getRandomItem(
        Roulette $roulette,
        Participation $participation
    ): ?RouletteItem {
        $pending = $this->pendingItems(
            $roulette,
            $participation
        );

        return $pending->isEmpty()
            ? null
            : $pending->random();
    }

    /**
     * Serializa un ítem para el frontend.
     *
     * Nunca expone la opción correcta.
     */
    public function serializeItem(RouletteItem $item): array
    {
        $options = collect(self::OPTION_KEYS)
            ->map(fn ($key) => [
                'key' => $key,
                'text' => $item->{'option_'.$key},
            ])
            ->shuffle()
            ->values()
            ->all();

        return [
            'id' => $item->id,
            'question' => $item->question,
            'points' => $item->points,
            'options' => $options,
        ];
    }

    /**
     * Comprueba y registra una respuesta.
     *
     * Los puntos guardados en RouletteAnswer son los puntos
     * internos del ítem.
     *
     * ParticipationService::syncScore() transforma esos puntos
     * al Activity.max_score.
     *
     * @return array{
     *     is_correct: bool,
     *     score: int,
     *     participation_score: int,
     *     already_answered: bool,
     *     completed: bool,
     *     correct_option: ?string,
     *     correct_text: ?string,
     *     answered: int,
     *     total: int,
     *     error: ?string
     * }
     */
    public function checkAnswer(
        Roulette $roulette,
        Participation $participation,
        int $rouletteItemId,
        string $response,
        bool $autoFinish = true
    ): array {
        if ($participation->status !== 'started') {
            return $this->error(
                'La participación ya ha finalizado.'
            );
        }

        $item = RouletteItem::query()
            ->where('roulette_id', $roulette->id)
            ->find($rouletteItemId);

        if (! $item) {
            return $this->error(
                'Ese ítem no pertenece a esta actividad.'
            );
        }

        $answeredItemIds = $this->answeredItemIds(
            $roulette,
            $participation
        );

        if (in_array($item->id, $answeredItemIds, true)) {
            return $this->alreadyAnswered(
                $roulette,
                $participation
            );
        }

        $responseKey = strtolower(trim($response));

        $isCorrect =
            $responseKey === $item->correct_option;

        $responseText = in_array(
            $responseKey,
            self::OPTION_KEYS,
            true
        )
            ? (string) $item->{'option_'.$responseKey}
            : $responseKey;

        $score = $isCorrect
            ? $item->points
            : 0;

        /*
         * Registrar la respuesta y sincronizar el score
         * como una sola operación.
         */
        DB::transaction(function () use (
            $participation,
            $item,
            $responseText,
            $isCorrect,
            $score
        ) {
            RouletteAnswer::create([
                'participation_id' => $participation->id,
                'roulette_item_id' => $item->id,
                'response' => $responseText,
                'is_correct' => $isCorrect,
                'score' => $score,
                'answered_at' => now(),
            ]);

            $this->participationService->syncScore(
                $participation
            );
        });

        /*
         * Obtener el score real de la actividad después
         * de syncScore().
         *
         * Este valor ya está normalizado contra Activity.max_score.
         */
        $participation = $participation->fresh();

        $answeredItemIds[] = $item->id;

        $answered = count($answeredItemIds);

        $total = $roulette->items()->count();

        $completed =
            $total > 0 &&
            $answered >= $total;

        /*
         * En multijugador la finalización la decide la GameSession,
         * así que checkAnswer() se invoca con autoFinish = false.
         */
        if ($autoFinish && $completed) {
            $participation = $this->participationService->finish(
                $participation
            );
        }

        return [
            'is_correct' => $isCorrect,

            /*
             * Mantiene el puntaje interno del ítem para no romper
             * la lógica existente de Roulette.
             */
            'score' => $score,

            /*
             * Este es el score real de la Activity,
             * limitado por Activity.max_score.
             */
            'participation_score' => $participation->score,

            'already_answered' => false,
            'completed' => $completed,
            'correct_option' => $item->correct_option,
            'correct_text' => (string) $item->{'option_'.$item->correct_option},
            'answered' => $answered,
            'total' => $total,
            'error' => null,
        ];
    }

    /**
     * Respuesta para un ítem que ya había sido contestado.
     */
    private function alreadyAnswered(
        Roulette $roulette,
        Participation $participation
    ): array {
        return [
            'is_correct' => true,
            'score' => 0,
            'participation_score' => $participation->score,
            'already_answered' => true,
            'completed' => false,
            'correct_option' => null,
            'correct_text' => null,
            'answered' => count(
                $this->answeredItemIds(
                    $roulette,
                    $participation
                )
            ),
            'total' => $roulette->items()->count(),
            'error' => null,
        ];
    }

    /**
     * Normaliza la configuración de un ítem.
     */
    private function normalizeItem(array $item): array
    {
        $correct = strtolower(
            (string) ($item['correct_option'] ?? 'a')
        );

        if (! in_array(
            $correct,
            self::OPTION_KEYS,
            true
        )) {
            $correct = 'a';
        }

        $normalized = [
            'question' => $this->normalizeText(
                $item['question'] ?? ''
            ),
            'correct_option' => $correct,
            'points' => max(
                1,
                (int) ($item['points'] ?? 10)
            ),
        ];

        foreach (self::OPTION_KEYS as $key) {
            $normalized['option_'.$key] =
                $this->normalizeText(
                    $item['option_'.$key] ?? ''
                );
        }

        return $normalized;
    }

    private function normalizeText(string $text): string
    {
        return trim($text);
    }

    /**
     * Respuesta de error.
     */
    private function error(string $message): array
    {
        return [
            'is_correct' => false,
            'score' => 0,
            'participation_score' => 0,
            'already_answered' => false,
            'completed' => false,
            'correct_option' => null,
            'correct_text' => null,
            'answered' => 0,
            'total' => 0,
            'error' => $message,
        ];
    }
}
