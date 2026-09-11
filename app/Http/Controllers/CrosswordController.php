<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCrosswordRequest;
use App\Http\Requests\UpdateCrosswordRequest;
use App\Models\Activity;
use App\Models\CrosswordAnswer;
use App\Models\CrosswordWord;
use App\Models\Participation;
use App\Services\CrosswordService;
use App\Services\ParticipationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrosswordController extends Controller
{
    public function __construct(private CrosswordService $crosswordService, private ParticipationService $participationService) {}

    /**
     * Mostrar formulario de configuración inicial del crucigrama.
     * Solo si la actividad aún no tiene crossword creado.
     */
    public function configure(int $id)
    {
        $activity = Activity::findOrFail($id);

        abort_if(Auth::id() !== $activity->teacher_id, 403);
        abort_if($activity->type !== 'crossword', 404);

        $crossword = $activity->crossword;

        abort_if(
            $crossword === null,
            404
        );

        // Si ya está configurado, debe utilizarse la ruta de edición.
        if ($crossword->words()->exists()) {
            return redirect()->route('teacher.crossword.edit', $activity->id);
        }

        return view('teacher.crossword.configure', compact('activity'));
    }

    /**
     * Guardar la configuración del crucigrama por primera vez.
     */
    public function store(StoreCrosswordRequest $request, int $id)
    {
        $activity = Activity::findOrFail($id);

        abort_if($activity->type !== 'crossword', 404);

        $crossword = $activity->crossword;

        abort_if($crossword === null, 404);
        abort_if($crossword->words()->exists(), 409);

        $result = $this->crosswordService->store(
            $activity,
            $request->validated()
        );

        $message = 'Crucigrama guardado correctamente.';

        if (! empty($result['skipped'])) {
            $skipped = implode(', ', $result['skipped']);

            $message .= " Las siguientes palabras no pudieron colocarse porque no comparten letras con el resto: {$skipped}.";
        }

        return redirect()
            ->route('teacher.activities.show', $activity->id)
            ->with('success', $message);
    }

    /**
     * Mostrar formulario de edición del crucigrama existente.
     */
    public function edit(int $id)
    {
        $activity = Activity::findOrFail($id);

        abort_if(Auth::id() !== $activity->teacher_id, 403);
        abort_if($activity->type !== 'crossword', 404);

        $crossword = $activity->crossword;
        abort_if($crossword === null, 404); // no existe aún, usar configure

        $words = $crossword->words;

        return view('teacher.crossword.configure', compact('activity', 'crossword', 'words'));
    }

    /**
     * Actualizar la configuración del crucigrama regenerando el grid.
     */
    public function update(UpdateCrosswordRequest $request, int $id)
    {
        $activity = Activity::findOrFail($id);

        abort_if($activity->type !== 'crossword', 404);

        $crossword = $activity->crossword;
        abort_if($crossword === null, 404);

        $result = $this->crosswordService->update($crossword, $request->validated());

        $message = 'Crucigrama actualizado correctamente.';

        if (! empty($result['skipped'])) {
            $skipped  = implode(', ', $result['skipped']);
            $message .= " Las siguientes palabras no pudieron colocarse: {$skipped}.";
        }

        return redirect()
            ->route('teacher.activities.show', $activity)
            ->with('success', $message);
    }

    // -------------------------------------------------------------------------
    // Estudiante
    // -------------------------------------------------------------------------

    /**
     * Vista del juego para el estudiante.
     * Requiere que exista una participación activa (started) para este estudiante.
     */
    public function play(int $id)
    {
        $activity = Activity::findOrFail($id);

        abort_if($activity->type !== 'crossword', 404);
        abort_if($activity->status !== 'published', 403);

        $crossword = $activity->crossword;
        abort_if($crossword === null, 404);

        // Buscar participación activa del estudiante
        $participation = $this->participationService->getForPlay($activity);

        if ($participation->status === 'expired') {
            return redirect()->route(
                'student.participation.result',
                $activity->id
            );
        }
        $words = $crossword->words;

        return view('student.crossword.play', compact(
            'activity',
            'crossword',
            'words',
            'participation'
        ));
    }

    /**
     * Recibe la respuesta del estudiante para una palabra del crucigrama.
     * Retorna JSON con is_correct, score y (si incorrecta) la palabra correcta.
     */
    public function answer(Request $request)
    {
        $validated = $request->validate([
            'participation_id' => [
                'required',
                'integer',
                'exists:participations,id'
            ],
            'crossword_word_id' => [
                'required',
                'integer',
                'exists:crossword_words,id'
            ],
            'response' => [
                'required',
                'string',
                'max:100'
            ],
        ]);

        $participation = Participation::findOrFail(
            $validated['participation_id']
        );

        // Verificar que la participación pertenece al estudiante
        abort_if(
            $participation->student_id !== Auth::id(),
            403
        );

        // Solo se puede responder mientras está activa
        abort_if(
            $participation->status !== 'started',
            409
        );

        $crosswordWord = CrosswordWord::findOrFail(
            $validated['crossword_word_id']
        );

        // Verificar que la palabra pertenece a la actividad
        abort_if(
            $crosswordWord->crossword->activity_id !==
                $participation->activity_id,
            403
        );

        // Verificar que no haya sido respondida anteriormente
        $alreadyAnswered = CrosswordAnswer::where(
            'participation_id',
            $participation->id
        )
            ->where(
                'crossword_word_id',
                $crosswordWord->id
            )
            ->exists();

        if ($alreadyAnswered) {
            return response()->json([
                'error' => 'Esta palabra ya fue respondida.'
            ], 409);
        }

        // Validar respuesta
        $isCorrect = $this->crosswordService->validateAnswer(
            $crosswordWord,
            $validated['response']
        );

        $score = $isCorrect
            ? $crosswordWord->score
            : 0;

        // Guardar respuesta
        CrosswordAnswer::create([
            'participation_id'  => $participation->id,
            'crossword_word_id' => $crosswordWord->id,
            'response'          => $validated['response'],
            'is_correct'        => $isCorrect,
            'score'             => $score,
        ]);

        // Actualizar score
        if ($isCorrect) {
            $participation->increment('score', $score);
        }

        /*
     * Comprobar si ya se respondieron
     * todas las palabras.
     */
        $totalWords = $crosswordWord
            ->crossword
            ->words()
            ->count();

        $answeredWords = CrosswordAnswer::where(
            'participation_id',
            $participation->id
        )->count();

        $completed =
            $totalWords > 0 &&
            $answeredWords >= $totalWords;

        /*
     * Finalizar Participation.
     */
        if ($completed) {
            $this->participationService->finish(
                $participation->fresh()
            );
        }

        return response()->json([
            'is_correct'   => $isCorrect,
            'score'        => $score,
            'correct_word' => $isCorrect
                ? null
                : $crosswordWord->word,
            'completed'    => $completed,
        ]);
    }
}
