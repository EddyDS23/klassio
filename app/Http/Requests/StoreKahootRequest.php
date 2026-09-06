<?php

namespace App\Http\Requests;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class StoreKahootRequest extends FormRequest
{
    public function authorize(): bool
    {
        $activity = Activity::find($this->route('id'));
        return $activity !== null && Auth::id() === $activity->teacher_id;
    }

    public function rules(): array
    {
        return [
            'questions'                        => ['required', 'array', 'min:1'],
            'questions.*.question'             => ['required', 'string', 'max:255'],
            'questions.*.time_limit'           => ['required', 'integer', 'min:5', 'max:120'],
            'questions.*.score'                => ['required', 'integer', 'min:1', 'max:1000'],
            'questions.*.options'              => ['required', 'array', 'min:2', 'max:4'],
            'questions.*.options.*.text'       => ['required', 'string', 'max:255'],
            'questions.*.options.*.is_correct' => ['sometimes', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'questions.required'                  => 'Debes agregar al menos una pregunta.',
            'questions.min'                        => 'El kahoot necesita al menos una pregunta.',
            'questions.*.question.required'        => 'Cada pregunta necesita un enunciado.',
            'questions.*.question.max'             => 'El enunciado puede tener máximo 255 caracteres.',
            'questions.*.time_limit.required'      => 'Cada pregunta necesita un límite de tiempo.',
            'questions.*.time_limit.min'           => 'El tiempo mínimo por pregunta es 5 segundos.',
            'questions.*.time_limit.max'           => 'El tiempo máximo por pregunta es 120 segundos.',
            'questions.*.score.required'           => 'Cada pregunta necesita un puntaje.',
            'questions.*.score.min'                => 'El puntaje mínimo por pregunta es 1.',
            'questions.*.score.max'                => 'El puntaje máximo por pregunta es 1000.',
            'questions.*.options.required'         => 'Cada pregunta necesita opciones.',
            'questions.*.options.min'              => 'Cada pregunta necesita al menos 2 opciones.',
            'questions.*.options.max'              => 'Cada pregunta puede tener máximo 4 opciones.',
            'questions.*.options.*.text.required'  => 'Cada opción necesita un texto.',
            'questions.*.options.*.text.max'       => 'El texto de cada opción puede tener máximo 255 caracteres.',
        ];
    }

    /**
     * Validación adicional: exactamente una opción correcta por pregunta.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $questions = $this->input('questions', []);

                foreach ($questions as $index => $question) {
                    $options   = $question['options'] ?? [];
                    $corrects  = 0;

                    foreach ($options as $option) {
                        if (isset($option['is_correct']) && $option['is_correct'] == '1') {
                            $corrects++;
                        }
                    }

                    if ($corrects !== 1) {
                        $validator->errors()->add(
                            "questions.$index.options",
                            'Cada pregunta debe tener exactamente una opción correcta.'
                        );
                    }
                }
            },
        ];
    }
}