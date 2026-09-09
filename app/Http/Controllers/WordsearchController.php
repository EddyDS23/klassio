<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerWordsearchRequest;
use App\Http\Requests\StoreWordsearchRequest;
use App\Http\Requests\UpdateWordsearchRequest;
use App\Models\Activity;
use App\Models\Participation;
use App\Models\Wordsearch;
use App\Services\WordSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class WordsearchController extends Controller
{
    public function __construct(
        protected WordSearchService $wordSearchService
    ) {
    }

    public function configure(int $id): View
    {
        $activity = Activity::findOrFail($id);

        return view('teacher.activities.wordsearch.form', [
            'activity' => $activity,
            'wordsearch' => null,
            'editing' => false,
        ]);
    }

    public function store(StoreWordsearchRequest $request, int $id): RedirectResponse
    {
        $activity = Activity::findOrFail($id);

        $data = $request->validated();

        $this->wordSearchService->buildWordsearch(
            $activity,
            (int) $data['rows'],
            (int) $data['columns'],
            $data['words']
        );

        return redirect()
            ->route('teacher.word-search.edit', $id)
            ->with('status', 'Sopa de letras guardada correctamente.');
    }

    public function edit(int $id): View
    {
        $activity = Activity::findOrFail($id);

        $wordsearch = $activity->wordsearch;

        if (! $wordsearch) {
            return view('teacher.activities.wordsearch.form', [
                'activity' => $activity,
                'wordsearch' => null,
                'editing' => false,
            ]);
        }

        return view('teacher.activities.wordsearch.form', [
            'activity' => $activity,
            'wordsearch' => $wordsearch,
            'editing' => true,
        ]);
    }

    public function update(UpdateWordsearchRequest $request, int $id): RedirectResponse
    {
        $activity = Activity::findOrFail($id);

        $data = $request->validated();

        $this->wordSearchService->buildWordsearch(
            $activity,
            (int) $data['rows'],
            (int) $data['columns'],
            $data['words']
        );

        return redirect()
            ->route('teacher.word-search.edit', $id)
            ->with('status', 'Sopa de letras actualizada correctamente.');
    }

    public function play(int $id): View
    {
        $activity = Activity::findOrFail($id);

        $wordsearch = $activity->wordsearch;

        abort_unless($wordsearch, 404, 'Esta actividad aún no tiene una sopa de letras.');

        $participation = $this->resolveParticipation($wordsearch);

        $found = $this->wordSearchService->foundWords($wordsearch, $participation);

        $foundWords = $wordsearch->words()->whereIn('id', $found)->get();

        return view('student.activities.wordsearch.play', [
            'wordsearch' => $wordsearch,
            'participation' => $participation,
            'grid' => $wordsearch->grid,
            'words' => $wordsearch->words()->orderBy('word')->get(),
            'foundIds' => $found,
            'foundCount' => $foundWords->count(),
            'earnedPoints' => $foundWords->sum('score'),
            'maxScore' => $wordsearch->words()->sum('score'),
        ]);
    }

    public function answer(AnswerWordsearchRequest $request): JsonResponse
    {
        $data = $request->validated();

        $wordsearch = Wordsearch::findOrFail((int) $data['wordsearch_id']);

        $participation = $this->resolveParticipation($wordsearch);

        $result = $this->wordSearchService->checkAnswer(
            $wordsearch,
            $participation,
            (int) $data['start_row'],
            (int) $data['start_column'],
            (int) $data['end_row'],
            (int) $data['end_column']
        );

        $result['total'] = $this->wordSearchService->foundWords($wordsearch, $participation);

        return response()->json($result);
    }

    /**
     * Resuelve la participación de la sesión (sin autenticación por el momento).
     * Posteriormente la integración con el sistema de participaciones la reemplazará.
     */
    protected function resolveParticipation(Wordsearch $wordsearch): Participation
    {
        $key = "wordsearch_participation_{$wordsearch->id}";

        if ($sessionId = Session::get($key)) {
            $participation = Participation::find($sessionId);

            if ($participation) {
                return $participation;
            }
        }

        $participation = Participation::create([
            'activity_id' => $wordsearch->activity_id,
            'attempt' => 1,
            'status' => 'started',
            'score' => 0,
        ]);

        Session::put($key, $participation->id);

        return $participation;
    }
}