<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKahootRequest;
use App\Http\Requests\UpdateKahootRequest;
use App\Models\Activity;
use App\Models\GameSession;
use App\Models\KahootAnswer;
use App\Models\Participation;
use App\Models\Question;
use App\Services\GameSessionService;
use App\Services\KahootService;
use App\Services\ParticipationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KahootController extends Controller
{
    public function __construct(
        private KahootService $kahootService,
        private ParticipationService $participationService,
        private GameSessionService $gameSessionService
    ) {}

    // -------------------------------------------------------------------------
    // Maestro — configuración
    // -------------------------------------------------------------------------

    /**
     * Formulario de configuración inicial.
     * Solo accesible si el kahoot aún no tiene preguntas.
     */
    public function configure(int $id)
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);
        abort_if($activity->type !== 'kahoot', 404);

        $kahoot = $activity->kahoot;
        abort_if($kahoot === null, 404);

        // Si ya tiene preguntas, redirigir a edición
        if ($kahoot->questions()->exists()) {
            return redirect()->route('teacher.kahoot.edit', $activity->id);
        }

        return view('teacher.kahoot.configure', compact('activity'));
    }

    /**
     * Guardar preguntas y opciones por primera vez.
     */
    public function store(StoreKahootRequest $request, int $id)
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        abort_if($activity->type !== 'kahoot', 404);

        $kahoot = $activity->kahoot;
        abort_if($kahoot === null, 404);
        abort_if($kahoot->questions()->exists(), 409);

        $this->kahootService->store($kahoot, $request->validated());

        return redirect()
            ->route('teacher.activities.show', $activity->id)
            ->with('success', 'Kahoot configurado correctamente.');
    }

    /**
     * Formulario de edición de preguntas existentes.
     */
    public function edit(int $id)
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        abort_if($activity->type !== 'kahoot', 404);

        $kahoot = $activity->kahoot;
        abort_if($kahoot === null, 404);
        abort_if(! $kahoot->questions()->exists(), 404);

        $questions = $kahoot->questions()
            ->orderBy('position')
            ->with(['options' => fn ($q) => $q->orderBy('position')])
            ->get();

        return view('teacher.kahoot.configure', compact('activity', 'kahoot', 'questions'));
    }

    /**
     * Reemplazar todas las preguntas y opciones.
     */
    public function update(UpdateKahootRequest $request, int $id)
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        abort_if($activity->type !== 'kahoot', 404);

        $kahoot = $activity->kahoot;
        abort_if($kahoot === null, 404);

        $this->kahootService->update($kahoot, $request->validated());

        return redirect()
            ->route('teacher.activities.show', $activity->id)
            ->with('success', 'Kahoot actualizado correctamente.');
    }

    // -------------------------------------------------------------------------
    // Estudiante — gameplay
    // -------------------------------------------------------------------------

    /**
     * Vista del juego para el estudiante.
     * Requiere participación activa (status = started).
     * Las preguntas se envían al frontend sin revelar cuál es la correcta.
     */
    public function play(int $id)
    {
        $activity = Activity::findOrFail($id);

        abort_if($activity->type !== 'kahoot', 404);
        abort_if($activity->status !== 'published', 403);

        $kahoot = $activity->kahoot;
        abort_if($kahoot === null, 404);
        abort_if(! $kahoot->questions()->exists(), 404);

        $participation = $this->participationService->getForPlay($activity);

        if ($participation->status === 'expired') {
            return redirect()->route(
                'student.participation.result',
                $activity->id
            );
        }
        // Preguntas sin revelar is_correct
        $questions = $this->kahootService->getQuestionsForPlay($kahoot);

        $remainingSeconds = $this->participationService->remainingSeconds(
            $participation,
            $activity
        );

        // IDs de preguntas ya respondidas por esta participación
        $answeredIds = KahootAnswer::where('participation_id', $participation->id)
            ->pluck('question_id')
            ->toArray();

        return view('student.kahoot.play', compact(
            'activity',
            'participation',
            'questions',
            'answeredIds',
            'remainingSeconds'
        ));
    }

    /**
     * Recibe la respuesta del estudiante para una pregunta.
     * Retorna JSON: is_correct, score, correct_option_id.
     */
    public function answer(Request $request)
    {
        $validated = $request->validate([
            'participation_id' => [
                'required',
                'integer',
                'exists:participations,id',
            ],
            'question_id' => [
                'required',
                'integer',
                'exists:questions,id',
            ],
            'option_id' => [
                'required',
                'integer',
                'exists:options,id',
            ],
        ]);

        $participation = Participation::findOrFail(
            $validated['participation_id']
        );

        Gate::authorize('answer', $participation);

        $question = Question::findOrFail(
            $validated['question_id']
        );

        $result = $this->kahootService->submitAnswer(
            $participation,
            $question,
            $validated['option_id']
        );

        /*
     * La pregunta ya había sido respondida.
     */
        if (isset($result['error'])) {
            return response()->json(
                ['error' => $result['error']],
                $result['status']
            );
        }

        /*
     * Comprobar si ya se respondieron todas
     * las preguntas del Kahoot.
     */
        $totalQuestions = $participation
            ->activity
            ->kahoot
            ->questions()
            ->count();

        $answeredQuestions = KahootAnswer::where(
            'participation_id',
            $participation->id
        )->count();

        $completed =
            $totalQuestions > 0 &&
            $answeredQuestions >= $totalQuestions;

        /*
     * Si terminó todas las preguntas,
     * finalizar la Participation.
     */
        if ($completed) {
            $this->participationService->finish(
                $participation->fresh()
            );
        }

        return response()->json([
            'is_correct' => $result['is_correct'],
            'score' => $result['score'],
            'correct_option_id' => $result['correct_option_id'],
            'completed' => $completed,
        ]);
    }

    // -------------------------------------------------------------------------
    // Modo multijugador (GameSession)
    // -------------------------------------------------------------------------

    /**
     * Sala de kahoot multijugador.
     *
     * El modo es por rondas: el profesor abre la siguiente pregunta y
     * todos los jugadores responden la misma a la vez.
     */
    public function playSession(int $sessionId): View|RedirectResponse
    {
        $session = GameSession::findOrFail($sessionId);

        Gate::authorize('play', $session);

        $activity = $session->activity;
        $kahoot = $activity->kahoot;

        abort_if($kahoot === null, 404);

        $participation = $this->gameSessionService->participationOf(
            $session,
            request()->user()
        );

        abort_if($participation === null, 403);

        $session = $this->gameSessionService->closeRoundIfDue($session);

        if ($session->status !== GameSession::STATUS_PLAYING) {
            return redirect()->route('student.game-sessions.result', $session->id);
        }

        $answeredIds = KahootAnswer::where(
            'participation_id',
            $participation->id
        )
            ->pluck('question_id')
            ->toArray();

        return view('student.kahoot.multiplayer', [
            'session' => $session,
            'activity' => $activity,
            'kahoot' => $kahoot,
            'participation' => $participation,
            'state' => $this->gameSessionService->getState($session),
            'answeredIds' => $answeredIds,
            'answeredCount' => count($answeredIds),
            'totalItems' => $kahoot->questions()->count(),
            'earnedPoints' => $participation->score,
            'maxScore' => $activity->max_score,
            'remainingSeconds' => $this->gameSessionService->remainingSeconds($session),
            'roundSeconds' => $session->state['round_seconds'] ?? GameSessionService::ROULETTE_ROUND_SECONDS,
            'hasAnsweredCurrent' => $this->gameSessionService->hasAnsweredCurrentRound($session, $participation),
        ]);
    }

    /**
     * Obtiene la pregunta de la ronda actual, que es la misma para todos
     * los jugadores. Solo lectura: el profesor es quien avanza.
     *
     * Es idempotente: si ya respondiste la ronda actual, no la vuelve a
     * mostrar.
     */
    public function fetchQuestion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => [
                'required',
                'integer',
                'exists:game_sessions,id',
            ],
        ]);

        $session = GameSession::findOrFail((int) $validated['session_id']);

        Gate::authorize('play', $session);

        $participation = $this->gameSessionService->participationOf(
            $session,
            $request->user()
        );

        abort_if($participation === null, 403);
        $session = $this->gameSessionService->closeRoundIfDue($session);

        if ($session->status !== GameSession::STATUS_PLAYING) {
            return response()->json([
                'completed' => true,
                'finished' => true,
                'participation_score' => $participation->fresh()->score,
                'max_score' => $session->activity->max_score,
            ]);
        }

        $questionId = $this->gameSessionService->currentKahootQuestionId($session);

        if ($questionId === null) {
            return response()->json([
                'completed' => false,
                'finished' => false,
                'waiting' => true,
                'participation_score' => $participation->fresh()->score,
                'max_score' => $session->activity->max_score,
            ]);
        }

        if ($this->gameSessionService->hasAnsweredCurrentRound($session, $participation)) {
            return response()->json([
                'completed' => false,
                'finished' => false,
                'already_answered' => true,
                'question_id' => $questionId,
                'participation_score' => $participation->fresh()->score,
                'max_score' => $session->activity->max_score,
            ]);
        }

        $state = $this->gameSessionService->getState($session);

        return response()->json([
            'completed' => false,
            'finished' => false,
            'item' => $state['current_item'],
            'answered' => $participation->kahootAnswers()->count(),
            'total' => $session->activity->kahoot->questions()->count(),
            'participation_score' => $participation->fresh()->score,
            'max_score' => $session->activity->max_score,
        ]);
    }

    /**
     * Registra la respuesta de un jugador a la pregunta de la ronda.
     *
     * La ronda avanza cuando todos responden o cuando se agota el tiempo.
     */
    public function answerSession(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => [
                'required',
                'integer',
                'exists:game_sessions,id',
            ],
            'question_id' => [
                'required',
                'integer',
                'exists:questions,id',
            ],
            'option_id' => [
                'required',
                'integer',
                'exists:options,id',
            ],
        ]);

        return DB::transaction(function () use ($request, $validated): JsonResponse {
            $session = GameSession::query()
                ->whereKey((int) $validated['session_id'])
                ->lockForUpdate()
                ->firstOrFail();

            Gate::authorize('play', $session);

            $participation = $this->gameSessionService->participationOf(
                $session,
                $request->user()
            );

            abort_if($participation === null, 403);
            $participation = Participation::query()
                ->whereKey($participation->id)
                ->lockForUpdate()
                ->firstOrFail();

            $session = $this->gameSessionService->closeRoundIfDue($session);

            if ($session->status !== GameSession::STATUS_PLAYING) {
                return response()->json([
                    'completed' => true,
                    'finished' => true,
                    'participation_score' => $participation->fresh()->score,
                ]);
            }

            $currentQuestionId = $this->gameSessionService->currentKahootQuestionId($session);

            if ($currentQuestionId === null) {
                return response()->json([
                    'error' => 'La pregunta terminó. Espera a que el profesor avance.',
                ], 422);
            }

            if ($currentQuestionId !== (int) $validated['question_id']) {
                return response()->json([
                    'error' => 'Responde la pregunta actual.',
                ], 422);
            }

            if ($this->gameSessionService->hasAnsweredCurrentRound($session, $participation)) {
                return response()->json([
                    'error' => 'Ya respondiste esta pregunta.',
                ], 422);
            }

            $question = Question::query()
                ->where('kahoot_id', $session->activity->kahoot->id)
                ->find((int) $validated['question_id']);

            if ($question === null) {
                return response()->json([
                    'error' => 'La pregunta no pertenece a esta actividad.',
                ], 422);
            }

            $result = $this->kahootService->submitAnswer(
                $participation,
                $question,
                (int) $validated['option_id']
            );

            if (isset($result['error'])) {
                return response()->json(
                    ['error' => $result['error']],
                    (int) ($result['status'] ?? 422)
                );
            }

            $this->gameSessionService->notifyScore(
                $session,
                $participation->fresh()
            );

            $session = $this->gameSessionService->closeRoundIfDue($session->fresh());
            $finished = $session->status === GameSession::STATUS_FINISHED;

            return response()->json(array_merge($result, [
                'finished' => $finished,
                'waiting_for_others' => ! $finished
                    && $this->gameSessionService->currentKahootQuestionId($session) === $currentQuestionId,
                'participation_score' => $participation->fresh()->score,
                'session_status' => $finished ? 'finished' : $session->status,
            ]));
        });
    }
}
