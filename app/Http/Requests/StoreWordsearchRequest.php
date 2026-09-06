<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWordsearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activity_id' => ['required', 'integer', 'exists:activities,id'],
            'rows' => ['required', 'integer', 'min:2', 'max:30'],
            'columns' => ['required', 'integer', 'min:2', 'max:30'],
            'words' => ['required', 'array', 'min:1'],
            'words.*.word' => ['required', 'string', 'min:1'],
            'words.*.score' => ['required', 'integer', 'min:1'],
        ];
    }
}