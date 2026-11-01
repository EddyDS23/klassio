<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateClassRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'description' => ['sometimes', 'string', 'max:255'],
            'units' => ['nullable', 'array', 'max:8'],
            'units.*.name' => ['nullable', 'string', 'max:150'],
        ];
    }

    /**
     * Nombres de unidades en blanco se normalizan a null en el servicio;
     * aquí solo se descartan claves no escalares.
     *
     * @return array{units: list<array{name: ?string}>}
     */
    public function validatedUnits(): array
    {
        $units = $this->validated()['units'] ?? [];

        return array_map(
            fn (array $unit): array => ['name' => $unit['name'] ?? null],
            array_values($units)
        );
    }
}
