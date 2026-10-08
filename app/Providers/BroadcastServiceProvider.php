<?php

namespace App\Providers;

use App\Models\GameSession;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Broadcast::routes();

        /*
         * Solo los participantes de la sala (o el maestro de la
         * actividad) pueden escuchar el canal privado de la partida.
         */
        Broadcast::channel('game-session.{id}', function ($user, $id) {
            $session = GameSession::with('activity')->find($id);

            if ($session === null) {
                return false;
            }

            $isTeacher = $session->activity->teacher_id === $user->id;

            $isParticipant = $session->participations()
                ->where('student_id', $user->id)
                ->whereIn('status', ['waiting', 'started', 'completed'])
                ->exists();

            $name = $user->name ?? $user->email;

            return $isTeacher || $isParticipant
                ? ['id' => $user->id, 'name' => $name]
                : false;
        });
    }
}
