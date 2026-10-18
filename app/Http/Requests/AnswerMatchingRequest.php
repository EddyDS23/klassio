<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerMatchingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'matching_id' => ['required', 'integer', 'exists:matchings,id'],
            'participation_id' => ['sometimes', 'nullable', 'integer', 'exists:participations,id'],
            'session_id' => ['sometimes', 'nullable', 'integer', 'exists:game_sessions,id'],
            'matching_item_id' => ['required', 'integer'],
            'response' => ['required', 'string', 'min:1'],
        ];
    }
}
