<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMatchingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.left' => ['required', 'string', 'min:1'],
            'items.*.right' => ['required', 'string', 'min:1'],
            'items.*.score' => ['required', 'integer', 'min:1'],
        ];
    }
}