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
            // 'nullable' permite enviar el campo sin texto: la opción
            // ConvertEmptyStringsToNull lo entrega como null y una regla
            // 'string' simple lo rechazaría ("must be a string").
            'description' => ['nullable', 'string', 'max:255'],
            'units' => ['nullable', 'array', 'max:8'],
            'units.*.name' => ['nullable', 'string', 'max:150'],
        ];
    }

    /**
     * Mensajes de validación en español para este formulario.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la clase es obligatorio.',
            'name.string' => 'El nombre de la clase debe ser un texto.',
            'name.max' => 'El nombre no puede exceder :max caracteres.',
            'description.string' => 'La descripción debe ser un texto.',
            'description.max' => 'La descripción no puede exceder :max caracteres.',
            'units.array' => 'Las unidades no tienen un formato válido.',
            'units.max' => 'Puedes crear hasta :max unidades.',
            'units.*.name.string' => 'Cada unidad debe ser un nombre de texto.',
            'units.*.name.max' => 'Cada unidad no puede exceder :max caracteres.',
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
