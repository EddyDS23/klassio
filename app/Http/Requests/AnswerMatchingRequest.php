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
            'matching_item_id' => ['required', 'integer'],
            'response' => ['required', 'string', 'min:1'],
        ];
    }
}