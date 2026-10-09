<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinGameSessionRequest;
use App\Models\GameSession;
use App\Services\GameSessionService;
use App\Services\ParticipationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use RuntimeException;

class GameSessionController extends Controller
{
    public function __construct(
        protected GameSessionService $gameSessionService,
        protected ParticipationService $participationService
    ) {}

    /**
     * Página para entrar a una sala con un código.
     */
    public function joinPage(): View
    {
        return view('student.game_sessions.join');
    }

    /**
     * Sala de espera.
     */
    public function show(int $id): View
    {
        $session = GameSession::findOrFail($id);

        Gate::authorize('view', $session);

        $myParticipation = $this->gameSessionService->participationOf(
            $session,
            Auth::user()
        );

        return view('student.game_sessions.lobby', [
            'session' => $session,
            'players' => $this->gameSessionService->players($session),
            'state' => $this->gameSessionService->getState($session),
            'myParticipation' => $myParticipation,
        ]);
    }

    /**
     * Entrar a una sala por código.
     */
    public function join(JoinGameSessionRequest $request): RedirectResponse
    {
        $session = $this->gameSessionService->findByCode(
            (string) $request->validated()['code']
        );

        if ($session === null) {
            return redirect()
                ->back()
                ->with('error', 'No existe una sala con ese código.');
        }

        Gate::authorize('join', $session);

        try {
            $this->gameSessionService->join($session, Auth::user());
        } catch (RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('student.game-sessions.show', $session->id);
    }

    /**
     * Abandonar la sala o la partida.
     */
    public function leave(int $id): RedirectResponse
    {
        $session = GameSession::findOrFail($id);

        Gate::authorize('leave', $session);

        try {
            $this->gameSessionService->leave($session, Auth::user());
        } catch (RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('student.activities.show', $session->activity_id)
            ->with('success', 'Saliste de la sala.');
    }

    /**
     * Estado compartido de la partida (JSON).
     */
    public function state(int $id): JsonResponse
    {
        $session = GameSession::findOrFail($id);

        Gate::authorize('view', $session);

        return response()->json(
            $this->gameSessionService->getState($session)
        );
    }

    /**
     * Entrar a la partida según el tipo de actividad.
     */
    public function play(int $id): RedirectResponse
    {
        $session = GameSession::findOrFail($id);

        Gate::authorize('play', $session);

        return match ($session->activity->type) {
            'roulette' => redirect()->route('student.roulette.play-session', $session->id),
            default => redirect()
                ->route('student.game-sessions.show', $session->id)
                ->with('error', 'Este juego aún no soporta modo multijugador.'),
        };
    }

    /**
     * Resultados de la partida y resultado individual del usuario.
     */
    public function result(int $id): View
    {
        $session = GameSession::findOrFail($id);

        Gate::authorize('result', $session);

        $myParticipation = $this->gameSessionService->participationOf(
            $session,
            Auth::user()
        );

        $myResult = $myParticipation !== null
            ? $this->participationService->getResult($myParticipation)
            : null;

        return view('student.game_sessions.result', [
            'session' => $session,
            'players' => $session->participations()
                ->with('student')
                ->orderByDesc('score')
                ->get(),
            'state' => $this->gameSessionService->getState($session),
            'myResult' => $myResult,
        ]);
    }
}
