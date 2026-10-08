<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRouletteRequest;
use App\Http\Requests\AnswerRouletteSessionRequest;
use App\Http\Requests\StoreRouletteRequest;
use App\Http\Requests\UpdateRouletteRequest;
use App\Models\Activity;
use App\Models\GameSession;
use App\Models\Roulette;
use App\Models\RouletteAnswer;
use App\Services\GameSessionService;
use App\Services\ParticipationService;
use App\Services\RouletteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RouletteController extends Controller
{
    public function __construct(
        protected RouletteService $rouletteService,
        protected ParticipationService $participationService,
        protected GameSessionService $gameSessionService
    ) {}

    /**
     * Mostrar formulario para configurar una nueva Ruleta.
     */
    public function configure(int $id): View
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $roulette = $activity->roulette;

        return view('teacher.roulette.configure', [
            'activity' => $activity,
            'roulette' => $roulette,
            'editing' => (bool) $roulette,
        ]);
    }

    /**
     * Guardar configuración de la Ruleta.
     */
    public function store(
        StoreRouletteRequest $request,
        int $id
    ): RedirectResponse {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $this->rouletteService->buildRoulette(
            $activity,
            $request->validated()['items']
        );

        return redirect()
            ->route('teacher.roulette.edit', $id)
            ->with(
                'success',
                'Actividad de ruleta guardada correctamente.'
            );
    }

    /**
     * Mostrar formulario para editar una Ruleta existente.
     */
    public function edit(int $id): View
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $roulette = $activity->roulette;

        return view('teacher.roulette.configure', [
            'activity' => $activity,
            'roulette' => $roulette,
            'editing' => (bool) $roulette,
        ]);
    }

    /**
     * Actualizar configuración de la Ruleta.
     */
    public function update(
        UpdateRouletteRequest $request,
        int $id
    ): RedirectResponse {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $this->rouletteService->buildRoulette(
            $activity,
            $request->validated()['items']
        );

        return redirect()
            ->route('teacher.roulette.edit', $id)
            ->with(
                'success',
                'Actividad de ruleta actualizada correctamente.'
            );
    }

    /**
     * Mostrar el juego de la Ruleta.
     *
     * La participación NO se crea aquí.
     * ParticipationController::start() es responsable
     * de iniciar la participación.
     */
    public function play(int $id): View|RedirectResponse
    {
        $activity = Activity::findOrFail($id);

        $roulette = $activity->roulette;

        abort_unless(
            $roulette,
            404,
            'Esta actividad aún no tiene una ruleta configurada.'
        );

        $participation =
            $this->participationService->getForPlay($activity);

        if ($participation->status === 'expired') {
            return redirect()->route(
                'student.participation.result',
                $activity->id
            );
        }

        $remainingSeconds =
            $this->participationService->remainingSeconds(
                $participation,
                $activity
            );

        $totalItems = $roulette->items()->count();

        $answeredIds = RouletteAnswer::where(
            'participation_id',
            $participation->id
        )
            ->pluck('roulette_item_id')
            ->toArray();

        return view('student.roulette.play', [
            'activity' => $activity,
            'roulette' => $roulette,
            'participation' => $participation,

            'answeredIds' => $answeredIds,
            'answeredCount' => count($answeredIds),
            'totalItems' => $totalItems,

            /*
             * Score real de la actividad.
             *
             * ParticipationService::syncScore()
             * ya lo normalizó contra Activity.max_score.
             */
            'earnedPoints' => $participation->score,

            /*
             * El máximo de la actividad es Activity.max_score.
             */
            'maxScore' => $activity->max_score,

            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    /**
     * Endpoint de giro.
     *
     * Devuelve un ítem aleatorio pendiente.
     */
    public function spin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activity_id' => [
                'required',
                'integer',
                'exists:activities,id',
            ],
        ]);

        $activity = Activity::findOrFail(
            (int) $validated['activity_id']
        );

        $roulette = $activity->roulette;

        abort_if($roulette === null, 404);

        $participation =
            $this->participationService->getActive($activity);

        $answered = count(
            $this->rouletteService->answeredItemIds(
                $roulette,
                $participation
            )
        );

        $total = $roulette->items()->count();

        $item = $this->rouletteService->getRandomItem(
            $roulette,
            $participation
        );

        if ($item === null) {
            return response()->json([
                'completed' => true,
                'item' => null,
                'answered' => $answered,
                'total' => $total,

                /*
                 * Score real de la actividad.
                 */
                'participation_score' => $participation->score,

                /*
                 * Máximo real de la actividad.
                 */
                'max_score' => $activity->max_score,
            ]);
        }

        return response()->json([
            'completed' => false,
            'item' => $this->rouletteService->serializeItem($item),
            'answered' => $answered,
            'total' => $total,

            'participation_score' => $participation->score,
            'max_score' => $activity->max_score,
        ]);
    }

    /**
     * Procesar una respuesta de la Ruleta.
     */
    public function answer(
        AnswerRouletteRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $roulette = Roulette::findOrFail(
            (int) $data['roulette_id']
        );

        $participation =
            $this->participationService->getActive(
                $roulette->activity
            );

        $result = $this->rouletteService->checkAnswer(
            $roulette,
            $participation,
            (int) $data['roulette_item_id'],
            (string) $data['response']
        );

        return response()->json($result);
    }

    // -------------------------------------------------------------------------
    // Modo multijugador (GameSession)
    // -------------------------------------------------------------------------

    /**
     * Mostrar la partida de Ruleta multijugador.
     */
    public function playSession(int $sessionId): View|RedirectResponse
    {
        $session = GameSession::findOrFail($sessionId);

        Gate::authorize('play', $session);

        $activity = $session->activity;
        $roulette = $activity->roulette;

        abort_if($roulette === null, 404);

        $participation = $this->gameSessionService->participationOf(
            $session,
            request()->user()
        );

        abort_if($participation === null, 403);

        if ($this->gameSessionService->isExpired($session)) {
            $this->gameSessionService->finish($session);

            return redirect()->route('student.game-sessions.result', $session->id);
        }

        $answeredIds = RouletteAnswer::where(
            'participation_id',
            $participation->id
        )
            ->pluck('roulette_item_id')
            ->toArray();

        return view('student.roulette.multiplayer', [
            'session' => $session,
            'activity' => $activity,
            'roulette' => $roulette,
            'participation' => $participation,
            'state' => $this->gameSessionService->getState($session),
            'answeredIds' => $answeredIds,
            'answeredCount' => count($answeredIds),
            'totalItems' => $roulette->items()->count(),
            'earnedPoints' => $participation->score,
            'maxScore' => $activity->max_score,
            'remainingSeconds' => $this->gameSessionService->remainingSeconds($session),
            'isMyTurn' => $session->current_turn_participation_id === $participation->id,
        ]);
    }

    /**
     * Endpoint de giro en multijugador.
     *
     * Solo puede girar el jugador dueño del turno.
     */
    public function spinSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => [
                'required',
                'integer',
                'exists:game_sessions,id',
            ],
        ]);

        $session = GameSession::findOrFail(
            (int) $validated['session_id']
        );

        Gate::authorize('play', $session);

        $participation = $this->gameSessionService->participationOf(
            $session,
            $request->user()
        );

        abort_if($participation === null, 403);

        if ($this->gameSessionService->isExpired($session)) {
            $this->gameSessionService->finish($session);

            return response()->json([
                'completed' => true,
                'finished' => true,
                'participation_score' => $participation->fresh()->score,
            ]);
        }

        if ($session->fresh()->current_turn_participation_id !== $participation->id) {
            return response()->json([
                'error' => 'No es tu turno.',
            ], 403);
        }

        $state = $session->state ?? [];

        if (isset($state['roulette_item_id']) && $state['roulette_item_id'] !== null) {
            return response()->json([
                'error' => 'Primero responde la pregunta actual.',
            ], 422);
        }

        $roulette = $session->activity->roulette;

        abort_if($roulette === null, 404);

        $item = $this->rouletteService->getRandomItem(
            $roulette,
            $participation
        );

        if ($item === null) {
            $this->gameSessionService->finish($session);

            return response()->json([
                'completed' => true,
                'finished' => true,
                'participation_score' => $participation->fresh()->score,
                'max_score' => $session->activity->max_score,
            ]);
        }

        $state['roulette_item_id'] = $item->id;

        $session->update(['state' => $state]);

        $answered = count(
            $this->rouletteService->answeredItemIds(
                $roulette,
                $participation
            )
        );

        return response()->json([
            'completed' => false,
            'finished' => false,
            'item' => $this->rouletteService->serializeItem($item),
            'answered' => $answered,
            'total' => $roulette->items()->count(),
            'participation_score' => $participation->fresh()->score,
            'max_score' => $session->activity->max_score,
        ]);
    }

    /**
     * Procesar una respuesta en multijugador.
     */
    public function answerSession(
        AnswerRouletteSessionRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $session = GameSession::findOrFail(
            (int) $data['session_id']
        );

        Gate::authorize('play', $session);

        $participation = $this->gameSessionService->participationOf(
            $session,
            $request->user()
        );

        abort_if($participation === null, 403);

        if ($this->gameSessionService->isExpired($session)) {
            $this->gameSessionService->finish($session);

            return response()->json([
                'completed' => true,
                'finished' => true,
                'participation_score' => $participation->fresh()->score,
            ]);
        }

        if ($session->fresh()->current_turn_participation_id !== $participation->id) {
            return response()->json([
                'error' => 'No es tu turno.',
            ], 403);
        }

        $state = $session->state ?? [];

        $pendingItemId = $state['roulette_item_id'] ?? null;

        if ($pendingItemId === null) {
            return response()->json([
                'error' => 'Primero gira la ruleta.',
            ], 422);
        }

        if ((int) $pendingItemId !== (int) $data['roulette_item_id']) {
            return response()->json([
                'error' => 'Responde la pregunta actual de la ruleta.',
            ], 422);
        }

        $roulette = $session->activity->roulette;

        abort_if($roulette === null, 404);

        $result = $this->rouletteService->checkAnswer(
            $roulette,
            $participation,
            (int) $data['roulette_item_id'],
            (string) $data['response'],
            autoFinish: false
        );

        if ($result['error'] !== null) {
            return response()->json($result, 422);
        }

        unset($state['roulette_item_id']);

        $session->update(['state' => $state]);

        $this->gameSessionService->notifyScore(
            $session,
            $participation->fresh()
        );

        $finished = false;

        if ($result['completed']) {
            $this->gameSessionService->finish($session);
            $finished = true;
        } else {
            $this->gameSessionService->nextTurn($session->fresh());
        }

        return response()->json(array_merge($result, [
            'finished' => $finished,
            'session_status' => $finished ? 'finished' : $session->fresh()->status,
        ]));
    }
}
