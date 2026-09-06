<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerMatchingRequest;
use App\Http\Requests\StoreMatchingRequest;
use App\Models\Activity;
use App\Models\Matching;
use App\Models\Participation;
use App\Services\MatchingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class MatchingController extends Controller
{
    public function __construct(
        protected MatchingService $service
    ) {
    }

    public function create(): View
    {
        $activities = Activity::query()
            ->where('type', 'matching')
            ->orderBy('id')
            ->get();

        return view('teacher.activities.matching.create', compact('activities'));
    }

    public function store(StoreMatchingRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();

        $activity = Activity::findOrFail($data['activity_id']);

        $this->service->buildMatching($activity, $data['items']);

        return redirect()
            ->route('matchings.show', $activity->matching)
            ->with('status', 'Actividad de unir conceptos generada correctamente.');
    }

    public function show(Matching $matching): View
    {
        return view('teacher.activities.matching.show', [
            'matching' => $matching,
            'items' => $matching->items()->orderBy('left_text')->get(),
        ]);
    }

    public function play(Matching $matching): View
    {
        $participation = $this->resolveParticipation($matching);

        $items = $matching->items()->get();

        return view('student.activities.matching.play', [
            'matching' => $matching,
            'participation' => $participation,
            'items' => $items,
            'rightOptions' => $items->shuffle(),
            'correctIds' => $this->service->correctItemIds($matching, $participation),
            'earnedPoints' => $this->service->earnedScore($matching, $participation),
            'maxScore' => $this->service->maxScore($matching),
        ]);
    }

    public function answer(AnswerMatchingRequest $request, Matching $matching): JsonResponse
    {
        $data = $request->validated();

        $participation = $this->resolveParticipation($matching);

        return response()->json(
            $this->service->checkAnswer(
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