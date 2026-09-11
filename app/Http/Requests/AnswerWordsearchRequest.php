<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerWordsearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'wordsearch_id' => ['required', 'integer', 'exists:worksearches,id'],
            'start_row' => ['required', 'integer'],
            'start_column' => ['required', 'integer'],
            'end_row' => ['required', 'integer'],
            'end_column' => ['required', 'integer'],
        ];
    }
}