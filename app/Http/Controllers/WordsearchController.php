<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerWordsearchRequest;
use App\Http\Requests\StoreWordsearchRequest;
use App\Models\Activity;
use App\Models\Participation;
use App\Models\Wordsearch;
use App\Services\WordSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class WordsearchController extends Controller
{
    public function __construct(
        protected WordSearchService $service
    ) {
    }

    public function create(): View
    {
        $activities = Activity::query()
            ->where('type', 'word_search')
            ->orderBy('id')
            ->get();

        return view('teacher.activities.wordsearch.create', compact('activities'));
    }

    public function store(StoreWordsearchRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();

        $activity = Activity::findOrFail($data['activity_id']);

        $this->service->buildWordsearch(
            $activity,
            (int) $data['rows'],
            (int) $data['columns'],
            $data['words']
        );

        return redirect()
            ->route('wordsearches.show', $activity->wordsearch)
            ->with('status', 'Sopa de letras generada correctamente.');
    }

    public function show(Wordsearch $wordsearch): View
    {
        return view('teacher.activities.wordsearch.show', [
            'wordsearch' => $wordsearch,
            'grid' => $wordsearch->grid,
            'words' => $wordsearch->words()->orderBy('word')->get(),
        ]);
    }

    public function play(Wordsearch $wordsearch): View
    {
        $participation = $this->resolveParticipation($wordsearch);

        $found = $this->service->foundWords($wordsearch, $participation);

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

    public function answer(
        AnswerWordsearchRequest $request,
        Wordsearch $wordsearch
    ): JsonResponse {
        $data = $request->validated();

        $participation = $this->resolveParticipation($wordsearch);

        $result = $this->service->checkAnswer(
            $wordsearch,
            $participation,
            (int) $data['start_row'],
            (int) $data['start_column'],
            (int) $data['end_row'],
            (int) $data['end_column']
        );

        $result['total'] = $this->service->foundWords($wordsearch, $participation);

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