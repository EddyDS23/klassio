<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerWordsearchRequest;
use App\Http\Requests\StoreWordsearchRequest;
use App\Http\Requests\UpdateWordsearchRequest;
use App\Models\Activity;
use App\Models\GameSession;
use App\Models\Wordsearch;
use App\Services\GameSessionAnswerService;
use App\Services\GameSessionService;
use App\Services\ParticipationService;
use App\Services\WordsearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class WordsearchController extends Controller
{
    public function __construct(
        protected WordsearchService $wordSearchService,
        protected ParticipationService $participationService,
        protected GameSessionService $gameSessionService,
        protected GameSessionAnswerService $gameSessionAnswerService,
    ) {}

    // -------------------------------------------------------------------------
    // Teacher — Configuración
    // -------------------------------------------------------------------------

    public function configure(int $id): View
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        return view('teacher.wordsearch.configure', [
            'activity' => $activity,
            'wordsearch' => null,
            'editing' => false,
        ]);
    }

    public function store(
        StoreWordsearchRequest $request,
        int $id
    ): RedirectResponse {
        $activity = Activity::findOrFail($id);
        Gate::authorize('update', $activity);

        $data = $request->validated();

        $this->wordSearchService->buildWordsearch(
            $activity,
            (int) $data['rows'],
            (int) $data['columns'],
            $data['words']
        );

        return redirect()
            ->route('teacher.wordsearch.edit', $id)
            ->with('status', 'Sopa de letras guardada correctamente.');
    }

    public function edit(int $id): View
    {
        $activity = Activity::findOrFail($id);
        Gate::authorize('update', $activity);

        $wordsearch = $activity->wordsearch;

        if (! $wordsearch) {
            return view('teacher.wordsearch.configure', [
                'activity' => $activity,
                'wordsearch' => null,
                'editing' => false,
            ]);
        }

        return view('teacher.wordsearch.configure', [
            'activity' => $activity,
            'wordsearch' => $wordsearch,
            'editing' => true,
        ]);
    }

    public function update(
        UpdateWordsearchRequest $request,
        int $id
    ): RedirectResponse {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $data = $request->validated();

        $this->wordSearchService->buildWordsearch(
            $activity,
            (int) $data['rows'],
            (int) $data['columns'],
            $data['words']
        );

        return redirect()
            ->route('teacher.wordsearch.edit', $id)
            ->with('status', 'Sopa de letras actualizada correctamente.');
    }

    // -------------------------------------------------------------------------
    // Student — Juego
    // -------------------------------------------------------------------------

    /**
     * Sala multijugador de sopa de letras (carrera libre).
     */
    public function playSession(int $sessionId): View|RedirectResponse
    {
        $session = GameSession::findOrFail($sessionId);

        Gate::authorize('play', $session);

        $activity = $session->activity;
        $wordsearch = $activity->wordsearch;

        abort_if($wordsearch === null, 404);

        $participation = $this->gameSessionService->participationOf(
            $session,
            request()->user()
        );

        abort_if($participation === null, 403);

        $session = $this->gameSessionService->closeRoundIfDue($session);

        if ($session->status !== GameSession::STATUS_PLAYING) {
            return redirect()->route('student.game-sessions.result', $session->id);
        }

        $found = $this->wordSearchService->foundWords(
            $wordsearch,
            $participation
        );

        $foundWords = $wordsearch
            ->words()
            ->whereIn('id', $found)
            ->get();

        return view('student.wordsearch.multiplayer', [
            'session' => $session,
            'activity' => $activity,
            'wordsearch' => $wordsearch,
            'participation' => $participation,
            'grid' => $wordsearch->grid,
            'remainingSeconds' => $this->gameSessionService->remainingSeconds($session),
            'words' => $wordsearch->words()->orderBy('word')->get(),
            'foundIds' => $found,
            'foundCount' => $foundWords->count(),
            'earnedPoints' => $foundWords->sum('score'),
            'maxScore' => $wordsearch->words()->sum('score'),
        ]);
    }

    public function play(int $id)
    {
        $activity = Activity::findOrFail($id);

        $wordsearch = $activity->wordsearch;

        abort_unless(
            $wordsearch,
            404,
            'Esta actividad aún no tiene una sopa de letras.'
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

        $found = $this->wordSearchService->foundWords(
            $wordsearch,
            $participation
        );

        $foundWords = $wordsearch
            ->words()
            ->whereIn('id', $found)
            ->get();

        return view('student.wordsearch.play', [
            'activity' => $activity,
            'wordsearch' => $wordsearch,
            'participation' => $participation,
            'grid' => $wordsearch->grid,
            'remainingSeconds' => $remainingSeconds,

            'words' => $wordsearch
                ->words()
                ->orderBy('word')
                ->get(),

            'foundIds' => $found,

            'foundCount' => $foundWords->count(),

            'earnedPoints' => $foundWords->sum('score'),

            'maxScore' => $wordsearch
                ->words()
                ->sum('score'),
        ]);
    }

    public function answer(AnswerWordsearchRequest $request): JsonResponse
    {
        $data = $request->validated();

        $wordsearch = Wordsearch::findOrFail(
            (int) $data['wordsearch_id']
        );

        $result = $this->gameSessionAnswerService->answer(
            $wordsearch->activity,
            isset($data['participation_id']) ? (int) $data['participation_id'] : null,
            isset($data['session_id']) ? (int) $data['session_id'] : null,
            function ($participation) use ($data, $wordsearch): array {
                $result = $this->wordSearchService->checkAnswer(
                    $wordsearch,
                    $participation,
                    (int) $data['start_row'],
                    (int) $data['start_column'],
                    (int) $data['end_row'],
                    (int) $data['end_column']
                );

                $totalFound = count($this->wordSearchService->foundWords($wordsearch, $participation));
                $totalWords = $wordsearch->words()->count();

                $result['total'] = $totalFound;
                $result['total_words'] = $totalWords;
                $result['finished'] = false;

                if ($result['correct'] && ! $result['already_found'] && $totalFound >= $totalWords) {
                    $this->participationService->finish($participation);
                    $result['finished'] = true;

                    if ($participation->game_session_id === null) {
                        $result['redirect'] = route('student.participation.result', $wordsearch->activity->id);
                    }
                }

                return $result;
            },
        );

        return response()->json($result);
    }
}
