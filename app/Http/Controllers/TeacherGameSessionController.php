<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameSessionRequest;
use App\Models\Activity;
use App\Models\GameSession;
use App\Services\GameSessionService;
use App\Services\ParticipationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use RuntimeException;

class TeacherGameSessionController extends Controller
{
    public function __construct(
        protected GameSessionService $gameSessionService,
        protected ParticipationService $participationService
    ) {}

    /**
     * Formulario para crear una sala multijugador.
     */
    public function create(Request $request): View
    {
        $activities = Activity::with('schoolClass')
            ->where('teacher_id', Auth::id())
            ->where('status', 'published')
            ->orderBy('title')
            ->get();

        $selectedActivityId = (int) $request->query('activity_id', 0) ?: null;

        if ($selectedActivityId !== null && $activities->doesntContain('id', $selectedActivityId)) {
            $selectedActivityId = null;
        }

        return view('teacher.game_sessions.create', [
            'activities' => $activities,
            'selectedActivityId' => $selectedActivityId,
        ]);
    }

    /**
     * Crea la sala. El profesor no participa como jugador.
     */
    public function store(StoreGameSessionRequest $request): RedirectResponse
    {
        Gate::authorize('create', GameSession::class);

        $activity = Activity::findOrFail(
            (int) $request->validated()['activity_id']
        );

        abort_unless($activity->teacher_id === Auth::id(), 403);

        try {
            $session = $this->gameSessionService->create(
                $activity,
                (int) $request->validated()['max_players'],
                Auth::id()
            );
        } catch (RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('teacher.game-sessions.show', $session->id);
    }

    /**
     * Sala de espera del profesor: código, jugadores e inicio.
     */
    public function show(int $id): View
    {
        $session = GameSession::findOrFail($id);

        Gate::authorize('view', $session);

        return view('teacher.game_sessions.show', [
            'session' => $session,
            'players' => $this->gameSessionService->players($session),
            'state' => $this->gameSessionService->getState($session),
            'canStart' => $this->gameSessionService->canStart($session),
        ]);
    }

    /**
     * El profesor inicia la partida.
     */
    public function start(int $id): RedirectResponse
    {
        $session = GameSession::findOrFail($id);

        Gate::authorize('start', $session);

        try {
            $session = $this->gameSessionService->start($session);
        } catch (RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('teacher.game-sessions.show', $session->id);
    }

    /**
     * El profesor avanza la partida: gira la ruleta o abre la siguiente
     * pregunta del kahoot, según el tipo de actividad.
     */
    public function spin(Request $request, int $id): JsonResponse
    {
        $session = GameSession::findOrFail($id);

        Gate::authorize('spin', $session);

        $seconds = (int) $request->input(
            'seconds',
            GameSessionService::ROULETTE_ROUND_SECONDS
        );

        try {
            $type = $session->activity->type;

            if (in_array($type, GameSessionService::RACE_TYPES, true)) {
                throw new RuntimeException(
                    'Este juego se juega en carrera libre: no hay rondas que avanzar.'
                );
            }

            $session = $type === 'kahoot'
                ? $this->gameSessionService->startKahootRound($session, $seconds)
                : $this->gameSessionService->startRound($session, $seconds);
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'finished' => $session->status !== GameSession::STATUS_PLAYING,
            'roulette_item_id' => $this->gameSessionService->currentRouletteItemId($session),
            'question_id' => $this->gameSessionService->currentKahootQuestionId($session),
            'round_seconds' => $session->state['round_seconds'] ?? null,
            'state' => $this->gameSessionService->getState($session),
        ]);
    }

    /**
     * Resultados generales de la partida para el profesor.
     */
    public function result(int $id): View
    {
        $session = GameSession::findOrFail($id);

        Gate::authorize('result', $session);

        return view('teacher.game_sessions.result', [
            'session' => $session,
            'players' => $session->participations()
                ->with('student')
                ->orderByDesc('score')
                ->get(),
            'state' => $this->gameSessionService->getState($session),
        ]);
    }
}
