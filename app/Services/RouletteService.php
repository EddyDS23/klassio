<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Participation;
use App\Models\Roulette;
use App\Models\RouletteAnswer;
use App\Models\RouletteItem;
use Illuminate\Database\Eloquent\Collection;

class RouletteService
{
    public function __construct(private ParticipationService $participationService) {}

    private const OPTION_KEYS = ['a', 'b', 'c', 'd'];

    /**
     * Crea o reemplaza la configuración de la ruleta.
     * Los ítems anteriores se eliminan por completo.
     */
    public function buildRoulette(Activity $activity, array $items): Roulette
    {
        $roulette = $activity->roulette()->updateOrCreate([], []);

        $roulette->items()->delete();

        foreach ($items as $item) {
            $roulette->items()->create($this->normalizeItem($item));
        }

        return $roulette;
    }

    /**
     * Puntaje máximo posible de la ruleta.
     */
    public function maxScore(Roulette $roulette): int
    {
        return (int) $roulette->items()->sum('points');
    }

    /**
     * Puntaje acumulado por una participación.
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
     * Ítems pendientes: los que aún no ha respondido la participación.
     */
    public function pendingItems(
        Roulette $roulette,
        Participation $participation
    ): Collection {
        $answered = $this->answeredItemIds($roulette, $participation);

        return $roulette->items()
            ->when($answered, fn ($q) => $q->whereNotIn('id', $answered))
            ->orderBy('id')
            ->get();
    }

    /**
     * Selecciona un ítem al azar entre los pendientes.
     * Retorna null si ya se respondieron todos.
     */
    public function getRandomItem(
        Roulette $roulette,
        Participation $participation
    ): ?RouletteItem {
        $pending = $this->pendingItems($roulette, $participation);

        return $pending->isEmpty() ? null : $pending->random();
    }

    /**
     * Serializa un ítem para el frontend.
     * No expone la opción correcta.
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
     * Comprueba la respuesta del estudiante y guarda la RouletteAnswer.
     *
     * @return array{
     *     is_correct: bool,
     *     score: int,
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
        string $response
    ): array {
        if ($participation->status !== 'started') {
            return $this->error('La participación ya ha finalizado.');
        }

        $item = RouletteItem::query()
            ->where('roulette_id', $roulette->id)
            ->find($rouletteItemId);

        if (! $item) {
            return $this->error('Ese ítem no pertenece a esta actividad.');
        }

        if (in_array($item->id, $this->answeredItemIds($roulette, $participation), true)) {
            return $this->alreadyAnswered($roulette, $participation);
        }

        $responseKey = strtolower(trim($response));

        $isCorrect = $responseKey === $item->correct_option;

        $responseText = in_array($responseKey, self::OPTION_KEYS, true)
            ? (string) $item->{'option_'.$responseKey}
            : $responseKey;

        RouletteAnswer::create([
            'participation_id' => $participation->id,
            'roulette_item_id' => $item->id,
            'response' => $responseText,
            'is_correct' => $isCorrect,
            'score' => $isCorrect ? $item->points : 0,
            'answered_at' => now(),
        ]);

        $this->participationService->syncScore($participation);

        $answered = count($this->answeredItemIds($roulette, $participation));
        $total = $roulette->items()->count();

        $completed = $total > 0 && $answered >= $total;

        if ($completed) {
            $this->participationService->finish($participation->fresh());
        }

        return [
            'is_correct' => $isCorrect,
            'score' => $isCorrect ? $item->points : 0,
            'already_answered' => false,
            'completed' => $completed,
            'correct_option' => $item->correct_option,
            'correct_text' => (string) $item->{'option_'.$item->correct_option},
            'answered' => $answered,
            'total' => $total,
            'error' => null,
        ];
    }

    private function alreadyAnswered(
        Roulette $roulette,
        Participation $participation
    ): array {
        return [
            'is_correct' => true,
            'score' => 0,
            'already_answered' => true,
            'completed' => false,
            'correct_option' => null,
            'correct_text' => null,
            'answered' => count($this->answeredItemIds($roulette, $participation)),
            'total' => $roulette->items()->count(),
            'error' => null,
        ];
    }

    private function normalizeItem(array $item): array
    {
        $correct = strtolower((string) ($item['correct_option'] ?? 'a'));

        if (! in_array($correct, self::OPTION_KEYS, true)) {
            $correct = 'a';
        }

        $normalized = [
            'question' => $this->normalizeText($item['question'] ?? ''),
            'correct_option' => $correct,
            'points' => max(1, (int) ($item['points'] ?? 10)),
        ];

        foreach (self::OPTION_KEYS as $key) {
            $normalized['option_'.$key] = $this->normalizeText(
                $item['option_'.$key] ?? ''
            );
        }

        return $normalized;
    }

    private function normalizeText(string $text): string
    {
        return trim($text);
    }

    private function error(string $message): array
    {
        return [
            'is_correct' => false,
            'score' => 0,
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
