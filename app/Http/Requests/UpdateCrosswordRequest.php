<?php

namespace App\Http\Requests;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCrosswordRequest extends FormRequest
{
    public function authorize(): bool
    {
         $activity = Activity::find($this->route('id'));

        return $activity !== null && Auth::id() === $activity->teacher_id;
    }

    public function rules(): array
    {
        return [
            'words'             => ['required', 'array', 'min:2'],
            'words.*.word'      => ['required', 'string', 'max:20', 'alpha'],
            'words.*.clue'      => ['required', 'string', 'max:255'],
            'words.*.score'     => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'words.required'         => 'Debes ingresar al menos dos palabras.',
            'words.min'              => 'El crucigrama necesita al menos dos palabras.',
            'words.*.word.required'  => 'Cada entrada debe tener una palabra.',
            'words.*.word.alpha'     => 'Las palabras solo pueden contener letras (sin espacios ni acentos).',
            'words.*.word.max'       => 'Cada palabra puede tener máximo 20 letras.',
            'words.*.clue.required'  => 'Cada palabra necesita una pista.',
            'words.*.clue.max'       => 'La pista puede tener máximo 255 caracteres.',
            'words.*.score.required' => 'Cada palabra necesita un puntaje.',
            'words.*.score.min'      => 'El puntaje mínimo por palabra es 1.',
            'words.*.score.max'      => 'El puntaje máximo por palabra es 1000.',
        ];
    }
}