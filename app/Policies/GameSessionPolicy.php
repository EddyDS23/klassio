<?php

namespace App\Policies;

use App\Models\GameSession;
use App\Models\User;

class GameSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, GameSession $session): bool
    {
        return $this->isParticipant($user, $session)
            || $session->activity->teacher_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'student';
    }

    public function update(User $user, GameSession $session): bool
    {
        return false;
    }

    public function delete(User $user, GameSession $session): bool
    {
        return false;
    }

    public function restore(User $user, GameSession $session): bool
    {
        return false;
    }

    public function forceDelete(User $user, GameSession $session): bool
    {
        return false;
    }

    /**
     * Reglas de negocio de la sala (cupo, duplicados, estado) las evalúa
     * GameSessionService, que emite el mensaje de error amigable.
     */
    public function join(User $user, GameSession $session): bool
    {
        return $user->role === 'student';
    }

    /**
     * Solo el creador (host) de la sala puede iniciar la partida.
     */
    public function start(User $user, GameSession $session): bool
    {
        if ($user->role !== 'student') {
            return false;
        }

        if (! in_array($session->status, ['waiting', 'starting'], true)) {
            return false;
        }

        return $session->created_by === $user->id
            && $this->isParticipant($user, $session);
    }

    public function leave(User $user, GameSession $session): bool
    {
        if (in_array($session->status, ['finished', 'cancelled'], true)) {
            return false;
        }

        return $this->isParticipant($user, $session);
    }

    /**
     * Solo un participante puede jugar mientras la partida está activa.
     */
    public function play(User $user, GameSession $session): bool
    {
        return $session->status === 'playing'
            && $this->isParticipant($user, $session);
    }

    public function result(User $user, GameSession $session): bool
    {
        return $this->isParticipant($user, $session)
            || $session->activity->teacher_id === $user->id;
    }

    /**
     * Participante: estudiante dentro de la sesión con participación
     * en waiting, started o completed.
     */
    private function isParticipant(User $user, GameSession $session): bool
    {
        return $session->participations()
            ->where('student_id', $user->id)
            ->whereIn('status', ['waiting', 'started', 'completed'])
            ->exists();
    }
}
