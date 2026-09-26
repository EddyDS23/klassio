<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRouletteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.question' => ['required', 'string', 'min:1'],
            'items.*.option_a' => ['required', 'string', 'min:1'],
            'items.*.option_b' => ['required', 'string', 'min:1'],
            'items.*.option_c' => ['required', 'string', 'min:1'],
            'items.*.option_d' => ['required', 'string', 'min:1'],
            'items.*.correct_option' => ['required', Rule::in(['a', 'b', 'c', 'd'])],
            'items.*.points' => ['required', 'integer', 'min:1'],
        ];
    }
}
