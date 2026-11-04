<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerMatchingRequest;
use App\Http\Requests\StoreMatchingRequest;
use App\Http\Requests\UpdateMatchingRequest;
use App\Models\Activity;
use App\Models\GameSession;
use App\Models\Matching;
use App\Services\GameSessionAnswerService;
use App\Services\GameSessionService;
use App\Services\MatchingService;
use App\Services\ParticipationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MatchingController extends Controller
{
    public function __construct(
        protected MatchingService $matchingService,
        protected ParticipationService $participationService,
        protected GameSessionService $gameSessionService,
        protected GameSessionAnswerService $gameSessionAnswerService,
    ) {}

    /**
     * Mostrar formulario para configurar un nuevo Matching.
     */
    public function configure(int $id): View
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $matching = $activity->matching;

        return view('teacher.matching.configure', [
            'activity' => $activity,
            'matching' => $matching,
            'editing' => (bool) $matching,
        ]);
    }

    /**
     * Guardar configuración del Matching.
     */
    public function store(
        StoreMatchingRequest $request,
        int $id
    ): RedirectResponse {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $data = $request->validated();

        $this->matchingService->buildMatching(
            $activity,
            $data['items']
        );

        return redirect()
            ->route('teacher.matching.edit', $id)
            ->with(
                'success',
                'Actividad de unir conceptos guardada correctamente.'
            );
    }

    /**
     * Mostrar formulario para editar un Matching existente.
     */
    public function edit(int $id): View
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $matching = $activity->matching;

        return view('teacher.matching.configure', [
            'activity' => $activity,
            'matching' => $matching,
            'editing' => (bool) $matching,
        ]);
    }

    /**
     * Actualizar configuración del Matching.
     */
    public function update(
        UpdateMatchingRequest $request,
        int $id
    ): RedirectResponse {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $data = $request->validated();

        $this->matchingService->buildMatching(
            $activity,
            $data['items']
        );

        return redirect()
            ->route('teacher.matching.edit', $id)
            ->with(
                'success',
                'Actividad de unir conceptos actualizada correctamente.'
            );
    }

    /**
     * Mostrar el juego de Matching.
     *
     * La participación NO se crea aquí.
     * Debe haber sido creada previamente por:
     *
     * ParticipationController::start()
     *        ↓
     * ParticipationService::start()
     *
     * Aquí solamente recuperamos la participación activa.
     */
    public function play(int $id)
    {
        $activity = Activity::findOrFail($id);

        $matching = $activity->matching;

        abort_unless(
            $matching,
            404,
            'Esta actividad aún no tiene una actividad de unir conceptos.'
        );

        $participation = $this->participationService->getForPlay($activity);

        if ($participation->status === 'expired') {
            return redirect()->route(
                'student.participation.result',
                $activity->id
            );
        }

        $remainingSeconds = $this->participationService->remainingSeconds(
            $participation,
            $activity
        );

        $items = $matching->items()->get();

        return view('student.matching.play', [
            'activity' => $activity,
            'matching' => $matching,
            'participation' => $participation,
            'items' => $items,
            'remainingSeconds' => $remainingSeconds,
            'rightOptions' => $items->shuffle(),
            'correctIds' => $this->matchingService->correctItemIds(
                $matching,
                $participation
            ),
            'earnedPoints' => $this->matchingService->earnedScore(
                $matching,
                $participation
            ),
            'maxScore' => $this->matchingService->maxScore(
                $matching
            ),
        ]);
    }

    /**
     * Sala multijugador de Matching (carrera libre).
     */
    public function playSession(int $sessionId): View|RedirectResponse
    {
        $session = GameSession::findOrFail($sessionId);

        $activity = $session->activity;
        $matching = $activity->matching;

        abort_if($matching === null, 404);

        $participation = $this->gameSessionService->participationOf(
            $session,
            request()->user()
        );

        abort_if($participation === null, 403);

        $session = $this->gameSessionService->closeRoundIfDue($session);

        if ($session->status !== GameSession::STATUS_PLAYING) {
            return redirect()->route('student.game-sessions.result', $session->id);
        }

        Gate::authorize('play', $session);

        $items = $matching->items()->get();

        return view('student.matching.multiplayer', [
            'session' => $session,
            'activity' => $activity,
            'matching' => $matching,
            'participation' => $participation,
            'items' => $items,
            'remainingSeconds' => $this->gameSessionService->remainingSeconds($session),
            'rightOptions' => $items->shuffle(),
            'correctIds' => $this->matchingService->correctItemIds(
                $matching,
                $participation
            ),
            'earnedPoints' => $this->matchingService->earnedScore(
                $matching,
                $participation
            ),
            'maxScore' => $this->matchingService->maxScore(
                $matching
            ),
        ]);
    }

    /**
     * Procesar una respuesta de Matching.
     */
    public function answer(
        AnswerMatchingRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $matching = Matching::findOrFail(
            (int) $data['matching_id']
        );

        return response()->json($this->gameSessionAnswerService->answer(
            $matching->activity,
            isset($data['participation_id']) ? (int) $data['participation_id'] : null,
            isset($data['session_id']) ? (int) $data['session_id'] : null,
            fn ($participation) => $this->matchingService->checkAnswer(
                $matching,
                $participation,
                (int) $data['matching_item_id'],
                (string) $data['response']
            )
        ));
    }
}
