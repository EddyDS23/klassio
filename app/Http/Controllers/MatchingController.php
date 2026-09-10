<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerMatchingRequest;
use App\Http\Requests\StoreMatchingRequest;
use App\Http\Requests\UpdateMatchingRequest;
use App\Models\Activity;
use App\Models\Matching;
use App\Models\Participation;
use App\Services\MatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class MatchingController extends Controller
{
    public function __construct(
        protected MatchingService $matchingService
    ) {
    }

    public function configure(int $id): View
    {
        $activity = Activity::findOrFail($id);

        return view('teacher.matching.configure', [
            'activity' => $activity,
            'matching' => null,
            'editing' => false,
        ]);
    }

    public function store(StoreMatchingRequest $request, int $id): RedirectResponse
    {
        $activity = Activity::findOrFail($id);

        $data = $request->validated();

        $this->matchingService->buildMatching($activity, $data['items']);

        return redirect()
            ->route('teacher.matching.edit', $id)
            ->with('status', 'Actividad de unir conceptos guardada correctamente.');
    }

    public function edit(int $id): View
    {
        $activity = Activity::findOrFail($id);

        $matching = $activity->matching;

        return view('teacher.matching.configure', [
            'activity' => $activity,
            'matching' => $matching,
            'editing' => (bool) $matching,
        ]);
    }

    public function update(UpdateMatchingRequest $request, int $id): RedirectResponse
    {
        $activity = Activity::findOrFail($id);

        $data = $request->validated();

        $this->matchingService->buildMatching($activity, $data['items']);

        return redirect()
            ->route('teacher.matching.edit', $id)
            ->with('status', 'Actividad de unir conceptos actualizada correctamente.');
    }

    public function play(int $id): View
    {
        $activity = Activity::findOrFail($id);

        $matching = $activity->matching;

        abort_unless($matching, 404, 'Esta actividad aún no tiene una actividad de unir conceptos.');

        $participation = $this->resolveParticipation($matching);

        $items = $matching->items()->get();

        return view('student.matching.play', [
            'matching' => $matching,
            'participation' => $participation,
            'items' => $items,
            'rightOptions' => $items->shuffle(),
            'correctIds' => $this->matchingService->correctItemIds($matching, $participation),
            'earnedPoints' => $this->matchingService->earnedScore($matching, $participation),
            'maxScore' => $this->matchingService->maxScore($matching),
        ]);
    }

    public function answer(AnswerMatchingRequest $request): JsonResponse
    {
        $data = $request->validated();

        $matching = Matching::findOrFail((int) $data['matching_id']);

        $participation = $this->resolveParticipation($matching);

        return response()->json(
            $this->matchingService->checkAnswer(
                $matching,
                $participation,
                (int) $data['matching_item_id'],
                (string) $data['response']
            )
        );
    }

    /**
     * Resuelve la participación de la sesión (sin autenticación por el momento).
     * Posteriormente la integración con el sistema de participaciones la reemplazará.
     */
    protected function resolveParticipation(Matching $matching): Participation
    {
        $key = "matching_participation_{$matching->id}";

        if ($sessionId = Session::get($key)) {
            $participation = Participation::find($sessionId);

            if ($participation) {
                return $participation;
            }
        }

        $participation = Participation::create([
            'activity_id' => $matching->activity_id,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        Session::put($key, $participation->id);

        return $participation;
    }
}