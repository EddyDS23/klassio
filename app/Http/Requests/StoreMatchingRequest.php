<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activity_id' => ['required', 'integer', 'exists:activities,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.left' => ['required', 'string', 'min:1'],
            'items.*.right' => ['required', 'string', 'min:1'],
            'items.*.score' => ['required', 'integer', 'min:1'],
        ];
    }
}