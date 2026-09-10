<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKahootRequest;
use App\Http\Requests\UpdateKahootRequest;
use App\Models\Activity;
use App\Models\KahootAnswer;
use App\Models\Participation;
use App\Models\Question;
use App\Services\KahootService;
use App\Services\ParticipationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KahootController extends Controller
{
    public function __construct(private KahootService $kahootService, private ParticipationService $participationService) {}

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

        abort_if(Auth::id() !== $activity->teacher_id, 403);
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

        abort_if(Auth::id() !== $activity->teacher_id, 403);
        abort_if($activity->type !== 'kahoot', 404);

        $kahoot = $activity->kahoot;
        abort_if($kahoot === null, 404);
        abort_if(! $kahoot->questions()->exists(), 404);

        $questions = $kahoot->questions()
            ->orderBy('position')
            ->with(['options' => fn($q) => $q->orderBy('position')])
            ->get();

        return view('teacher.kahoot.configure', compact('activity', 'kahoot', 'questions'));
    }

    /**
     * Reemplazar todas las preguntas y opciones.
     */
    public function update(UpdateKahootRequest $request, int $id)
    {
        $activity = Activity::findOrFail($id);

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

        $participation = Participation::where('activity_id', $activity->id)
            ->where('student_id', Auth::id())
            ->where('status', 'started')
            ->latest()
            ->firstOrFail();

        // Preguntas sin revelar is_correct
        $questions = $this->kahootService->getQuestionsForPlay($kahoot);

        // IDs de preguntas ya respondidas por esta participación
        $answeredIds = KahootAnswer::where('participation_id', $participation->id)
            ->pluck('question_id')
            ->toArray();

        return view('student.kahoot.play', compact(
            'activity',
            'participation',
            'questions',
            'answeredIds'
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
                'exists:participations,id'
            ],
            'question_id' => [
                'required',
                'integer',
                'exists:questions,id'
            ],
            'option_id' => [
                'required',
                'integer',
                'exists:options,id'
            ],
        ]);

        $participation = Participation::findOrFail(
            $validated['participation_id']
        );

        abort_if(
            $participation->student_id !== Auth::id(),
            403
        );

        abort_if(
            $participation->status !== 'started',
            409
        );

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
            'is_correct'        => $result['is_correct'],
            'score'             => $result['score'],
            'correct_option_id' => $result['correct_option_id'],
            'completed'         => $completed,
        ]);
    }
}
