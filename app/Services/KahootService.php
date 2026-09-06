<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Kahoot;
use App\Models\KahootAnswer;
use App\Models\Option;
use App\Models\Participation;
use App\Models\Question;

class KahootService
{
    // -------------------------------------------------------------------------
    // Configuración
    // -------------------------------------------------------------------------

    /**
     * Guarda las preguntas y opciones del kahoot por primera vez.
     * El registro Kahoot ya existe (lo crea ActivityService al crear la actividad).
     */
    public function store(Kahoot $kahoot, array $data): void
    {
        $this->saveQuestions($kahoot, $data['questions']);
    }

    /**
     * Reemplaza todas las preguntas y opciones existentes.
     */
    public function update(Kahoot $kahoot, array $data): void
    {
        // Borrar preguntas anteriores — opciones se borran por cascade
        $kahoot->questions()->delete();

        $this->saveQuestions($kahoot, $data['questions']);
    }

    /**
     * Persiste preguntas y sus opciones.
     */
    private function saveQuestions(Kahoot $kahoot, array $questions): void
    {
        foreach ($questions as $position => $questionData) {
            $question = Question::create([
                'kahoot_id'  => $kahoot->id,
                'question'   => $questionData['question'],
                'position'   => $position + 1,
                'time_limit' => $questionData['time_limit'],
                'score'      => $questionData['score'],
            ]);

            foreach ($questionData['options'] as $optionPosition => $optionData) {
                Option::create([
                    'question_id' => $question->id,
                    'text'        => $optionData['text'],
                    'is_correct'  => isset($optionData['is_correct']) && $optionData['is_correct'] == '1',
                    'position'    => $optionPosition + 1,
                ]);
            }
        }
    }

    // -------------------------------------------------------------------------
    // Gameplay (Fase 5 - sin sesión en vivo)
    // -------------------------------------------------------------------------

    /**
     * Valida la opción elegida por el estudiante y calcula el puntaje.
     * No confía en ningún dato del frontend salvo el option_id.
     *
     * Retorna array con:
     *   is_correct  => bool
     *   score       => int
     *   correct_option_id => int   (para mostrar al estudiante cuál era la correcta)
     */
    public function validateAnswer(Question $question, int $optionId): array
    {
        // Verificar que la opción pertenece a esta pregunta
        $option = Option::where('id', $optionId)
            ->where('question_id', $question->id)
            ->firstOrFail();

        $isCorrect = (bool) $option->is_correct;
        $score     = $isCorrect ? $question->score : 0;

        $correctOption = Option::where('question_id', $question->id)
            ->where('is_correct', true)
            ->first();

        return [
            'is_correct'        => $isCorrect,
            'score'             => $score,
            'correct_option_id' => $correctOption?->id,
        ];
    }

    /**
     * Registra la respuesta del estudiante en kahoot_answers.
     * Verifica que no haya respondido ya esta pregunta.
     */
    public function submitAnswer(
        Participation $participation,
        Question $question,
        int $optionId
    ): array {
        // Verificar que la pregunta pertenece al kahoot de la actividad
        $kahoot = $participation->activity->kahoot;
        abort_if($question->kahoot_id !== $kahoot->id, 403);

        // Verificar que no haya sido respondida ya
        $alreadyAnswered = KahootAnswer::where('participation_id', $participation->id)
            ->where('question_id', $question->id)
            ->exists();

        if ($alreadyAnswered) {
            return ['error' => 'Esta pregunta ya fue respondida.', 'status' => 409];
        }

        $result = $this->validateAnswer($question, $optionId);

        KahootAnswer::create([
            'participation_id' => $participation->id,
            'question_id'      => $question->id,
            'option_id'        => $optionId,
            'is_correct'       => $result['is_correct'],
            'score'            => $result['score'],
        ]);

        if ($result['is_correct']) {
            $participation->increment('score', $result['score']);
        }

        return $result;
    }

    /**
     * Devuelve todas las preguntas con sus opciones ordenadas por position.
     * Las opciones NO incluyen cuál es la correcta (para no exponerlo al frontend).
     */
    public function getQuestionsForPlay(Kahoot $kahoot): array
    {
        return $kahoot->questions()
            ->orderBy('position')
            ->with(['options' => fn($q) => $q->orderBy('position')])
            ->get()
            ->map(fn($q) => [
                'id'         => $q->id,
                'question'   => $q->question,
                'position'   => $q->position,
                'time_limit' => $q->time_limit,
                'score'      => $q->score,
                'options'    => $q->options->map(fn($o) => [
                    'id'       => $o->id,
                    'text'     => $o->text,
                    'position' => $o->position,
                    // is_correct NO se expone al frontend
                ])->toArray(),
            ])
            ->toArray();
    }
}