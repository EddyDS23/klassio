<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameSessionRequest;
use App\Models\Activity;
use App\Models\GameSession;
use App\Models\SchoolClass;
use App\Services\GameSessionService;
use App\Services\ParticipationService;
use App\Support\PeriodResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
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
     * Formulario para crear una sala multijugador. El profesor elige primero
     * la clase (solo clases activas, agrupadas por periodo) y después una de
     * las actividades publicadas de esa clase; nunca se listan actividades
     * mezcladas de varias clases.
     *
     * Con ?activity_id=X se preselecciona la clase y la actividad (ej. el link
     * desde una actividad). Un class_id ajeno o inexistente hace que no haya
     * clase preseleccionada.
     */
    public function create(Request $request): View
    {
        $classes = SchoolClass::where('teacher_id', Auth::id())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $classGroups = $classes
            ->groupBy(fn (SchoolClass $class) => PeriodResolver::forDate($class->created_at))
            ->map(fn (Collection $group, string $code): array => [
                'code' => $code,
                'label' => PeriodResolver::label($code),
                'isCurrent' => $code === PeriodResolver::current(),
                'classes' => $group,
            ])
            ->sortByDesc(fn (array $group): array => [$group['isCurrent'], $group['code']])
            ->values();

        $selectedClassId = (int) $request->query('class_id', 0) ?: null;
        $selectedActivityId = (int) $request->query('activity_id', 0) ?: null;

        // Un activity_id válido define la clase directamente.
        $selectedClass = null;

        if ($selectedActivityId !== null) {
            $activity = Activity::where('teacher_id', Auth::id())
                ->where('status', 'published')
                ->find($selectedActivityId);

            if ($activity !== null) {
                $selectedClass = $activity->schoolClass;
            } else {
                $selectedActivityId = null;
            }
        }

        if ($selectedClass === null && $selectedClassId !== null) {
            $selectedClass = $classes->firstWhere('id', $selectedClassId);
        }

        if ($selectedClass === null) {
            // Sin clase elegida (o clase ajena): no se lista ninguna actividad.
            $selectedClassId = null;
            $selectedActivityId = null;
        }

        $activities = collect();

        if ($selectedClass !== null) {
            $activities = Activity::where('teacher_id', Auth::id())
                ->where('status', 'published')
                ->where('class_id', $selectedClass->id)
                ->orderBy('title')
                ->get();

            if ($selectedActivityId !== null && $activities->doesntContain('id', $selectedActivityId)) {
                $selectedActivityId = null;
            }
        }

        return view('teacher.game_sessions.create', [
            'activities' => $activities,
            'classGroups' => $classGroups,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
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
     * Cierra una sala que el profesor creó pero ningún estudiante utilizó.
     */
    public function cancel(int $id): RedirectResponse
    {
        $session = GameSession::findOrFail($id);

        Gate::authorize('cancel', $session);

        try {
            $this->gameSessionService->cancelWaitingSession($session);
        } catch (RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('teacher.dashboard')
            ->with('success', 'La sala se cerró y ya no aparece entre las partidas activas.');
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
