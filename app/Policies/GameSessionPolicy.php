<?php

namespace App\Policies;

use App\Models\GameSession;
use App\Models\Team;
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
        return $user->role === 'teacher';
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
     * Solo el profesor dueño de la actividad puede iniciar la partida.
     */
    public function start(User $user, GameSession $session): bool
    {
        if ($user->role !== 'teacher') {
            return false;
        }

        if (! in_array($session->status, ['waiting', 'starting'], true)) {
            return false;
        }

        return $session->activity->teacher_id === $user->id;
    }

    /**
     * Solo el profesor dueño de la actividad puede girar la ruleta.
     */
    public function spin(User $user, GameSession $session): bool
    {
        if ($user->role !== 'teacher') {
            return false;
        }

        if ($session->status !== GameSession::STATUS_PLAYING) {
            return false;
        }

        return $session->activity->teacher_id === $user->id;
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
        $query = $session->participations()
            ->whereIn('status', ['waiting', 'started', 'completed']);

        if ($session->activity->mode === 'team') {
            $teamId = Team::query()
                ->where('activity_id', $session->activity_id)
                ->whereHas('members', fn ($members) => $members->where('student_id', $user->id))
                ->value('id');

            return $teamId !== null && $query->where('team_id', $teamId)->exists();
        }

        return $query->where('student_id', $user->id)->exists();
    }
}
