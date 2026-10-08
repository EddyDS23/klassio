<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinGameSessionRequest;
use App\Http\Requests\StoreGameSessionRequest;
use App\Models\Activity;
use App\Models\Enrollment;
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

class GameSessionController extends Controller
{
    public function __construct(
        protected GameSessionService $gameSessionService,
        protected ParticipationService $participationService
    ) {}

    /**
     * Formulario para crear o unirse a una sala.
     */
    public function create(Request $request): View
    {
        abort_unless(Auth::user()->role === 'student', 403);

        $classIds = Enrollment::where('student_id', Auth::id())
            ->where('status', 'active')
            ->pluck('class_id');

        $activities = Activity::with('schoolClass')
            ->where('status', 'published')
            ->whereIn('class_id', $classIds)
            ->orderBy('title')
            ->get();

        $selectedActivityId = (int) $request->query('activity_id', 0) ?: null;

        if ($selectedActivityId !== null && $activities->doesntContain('id', $selectedActivityId)) {
            $selectedActivityId = null;
        }

        return view('student.game_sessions.create', [
            'activities' => $activities,
            'selectedActivityId' => $selectedActivityId,
        ]);
    }

    /**
     * Crea la sala y une al creador como host.
     */
    public function store(StoreGameSessionRequest $request): RedirectResponse
    {
        Gate::authorize('create', GameSession::class);

        $activity = Activity::findOrFail(
            (int) $request->validated()['activity_id']
        );

        try {
            $session = $this->gameSessionService->create(
                $activity,
                (int) $request->validated()['max_players'],
                Auth::id()
            );

            $this->gameSessionService->join($session, Auth::user());
        } catch (RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('student.game-sessions.show', $session->id);
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
            'isHost' => $session->created_by === Auth::id(),
            'canStart' => $this->gameSessionService->canStart($session),
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
     * El host inicia la partida.
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

        return redirect()->route('student.game-sessions.play', $session->id);
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
