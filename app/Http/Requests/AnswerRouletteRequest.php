<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerRouletteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roulette_id' => ['required', 'integer', 'exists:roulettes,id'],
            'roulette_item_id' => ['required', 'integer'],
            'response' => ['required', 'string', 'min:1', 'max:1'],
        ];
    }
}
