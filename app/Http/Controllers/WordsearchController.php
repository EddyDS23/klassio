<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerWordsearchRequest;
use App\Http\Requests\StoreWordsearchRequest;
use App\Http\Requests\UpdateWordsearchRequest;
use App\Models\Activity;
use App\Models\Wordsearch;
use App\Services\ParticipationService;
use App\Services\WordsearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WordsearchController extends Controller
{
    public function __construct(
        protected WordsearchService $wordSearchService,
        protected ParticipationService $participationService
    ) {}

    // -------------------------------------------------------------------------
    // Teacher — Configuración
    // -------------------------------------------------------------------------

    public function configure(int $id): View
    {
        $activity = Activity::findOrFail($id);

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

        $participation = $this->participationService->getActive(
            $wordsearch->activity
        );

        $result = $this->wordSearchService->checkAnswer(
            $wordsearch,
            $participation,
            (int) $data['start_row'],
            (int) $data['start_column'],
            (int) $data['end_row'],
            (int) $data['end_column']
        );

        $foundIds = $this->wordSearchService->foundWords(
            $wordsearch,
            $participation
        );

        $totalFound = count($foundIds);
        $totalWords = $wordsearch->words()->count();

        $result['total'] = $totalFound;
        $result['total_words'] = $totalWords;
        $result['finished'] = false;

        /*
     * Si encontró todas las palabras, finalizar automáticamente
     */
        if (
            $result['correct']
            && ! $result['already_found']
            && $totalFound >= $totalWords
        ) {
            $this->participationService->finish($participation);

            $result['finished'] = true;
            $result['redirect'] = route(
                'student.participation.result',
                $wordsearch->activity->id
            );
        }

        return response()->json($result);
    }
}
