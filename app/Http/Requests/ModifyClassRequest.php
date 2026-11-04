<?php

namespace App\Http\Requests;

use App\Models\SchoolClass;
use App\Models\Unit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ModifyClassRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:150'],
            'description' => ['sometimes', 'string', 'max:255'],
            'units' => ['nullable', 'array', 'max:8'],
            'units.*.id' => ['nullable', 'integer'],
            'units.*.name' => ['nullable', 'string', 'max:150'],
        ];
    }

    /**
     * Las unidades del update se validan contra la clase que llega en la
     * ruta: los ids deben pertenecer a ella y no se permite eliminar una
     * unidad con actividades asociadas.
     *
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('units') || $validator->errors()->has('units.*')) {
                    return;
                }

                $class = SchoolClass::find($this->route('id'));

                if ($class === null) {
                    return;
                }

                $submitted = collect($this->input('units', []))
                    ->filter(fn ($unit): bool => is_array($unit))
                    ->values();

                $submittedIds = collect($submitted)
                    ->map(fn (array $unit) => $unit['id'] ?? null)
                    ->filter(fn ($id): bool => (int) $id > 0)
                    ->map(fn ($id) => (int) $id);

                $class->units->each(function (Unit $unit) use ($submittedIds, $validator): void {
                    if ($submittedIds->contains($unit->id) || $unit->activities()->doesntExist()) {
                        return;
                    }

                    $validator->errors()->add(
                        'units',
                        'No puedes eliminar la unidad «'.($unit->name ?: 'Unidad '.$unit->number).
                        '» porque tiene actividades asociadas.'
                    );
                });

                foreach ($submittedIds->unique() as $id) {
                    if (! $class->units()->whereKey($id)->exists()) {
                        $validator->errors()->add('units', 'Una de las unidades no pertenece a esta clase.');

                        break;
                    }
                }
            },
        ];
    }

    /**
     * Filas en forma {id?, name}; el orden define la numeración dentro
     * del servicio.
     *
     * @return list<array{id?: int, name: ?string}>
     */
    public function submittedUnits(): array
    {
        return collect($this->input('units', []))
            ->filter(fn ($unit): bool => is_array($unit))
            ->values()
            ->map(function (array $unit): array {
                $row = ['name' => isset($unit['name']) && is_string($unit['name']) && trim($unit['name']) !== '' ? trim($unit['name']) : null];

                if (isset($unit['id']) && (int) $unit['id'] > 0) {
                    $row['id'] = (int) $unit['id'];
                }

                return $row;
            })
            ->all();
    }
}
