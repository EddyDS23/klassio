<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerRouletteSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required', 'integer', 'exists:game_sessions,id'],
            'roulette_item_id' => ['required', 'integer'],
            'response' => ['required', 'string', 'min:1', 'max:1'],
        ];
    }
}
