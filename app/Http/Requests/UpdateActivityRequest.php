<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        ];
    }
}
