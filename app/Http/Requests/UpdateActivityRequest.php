<?php

namespace App\Http\Requests;

use App\Models\Activity;
use App\Models\Unit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'type' => [
                'required',
                Rule::in([
                    'word_search',
                    'crossword',
                    'matching',
                    'kahoot',
                    'roulette',
                ]),
            ],

            'mode' => [
                'required',
                Rule::in([
                    'individual',
                    'team',
                ]),
            ],

            'max_score' => [
                'required',
                'integer',
                'min:1',
                'max:60000',

            ],

            'time_limit' => [
                'required',
                'integer',
                'min:1',
            ],

            'attempts' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],

            'due_at' => [
                'nullable',
                'date',
            ],

            'unit_id' => [
                'nullable',
                'integer',
                'exists:units,id',
            ],
        ];
    }

    /**
     * La unidad del update se valida contra la clase de la actividad que
     * llega en la ruta; si la actividad no existe, el 404 lo resuelve el
     * controlador.
     *
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $unitId = $this->integer('unit_id');

                if ($unitId === 0 || $validator->errors()->has('unit_id')) {
                    return;
                }

                $activity = Activity::find($this->route('id'));

                if ($activity === null) {
                    return;
                }

                if (Unit::whereKey($unitId)->value('class_id') !== $activity->class_id) {
                    $validator->errors()->add(
                        'unit_id',
                        'La unidad seleccionada no pertenece a la clase de la actividad.'
                    );
                }
            },
        ];
    }
}
