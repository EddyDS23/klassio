<?php

namespace App\Http\Requests;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreGameSessionRequest extends FormRequest
{
    /**
     * La sala solo puede crearse sobre una actividad publicada del propio
     * profesor. El store revalida el ownership; esto defiende en profundidad
     * antes de llegar al controlador.
     */
    public function authorize(): bool
    {
        $activity = Activity::find($this->integer('activity_id'));

        return $activity !== null
            && $activity->teacher_id === Auth::id()
            && $activity->status === 'published';
    }

    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'activity_id' => ['required', 'integer', 'exists:activities,id'],
            'max_players' => ['required', 'integer', 'min:2', 'max:50'],
        ];
    }
}
