<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\GameSession;
use App\Models\Participation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use RuntimeException;

class GameSessionAnswerService
{
    public function __construct(
        protected GameSessionService $gameSessionService,
        protected ParticipationService $participationService,
    ) {}

    /**
     * Resuelve la participación que debe recibir una respuesta.
     *
     * Si se proporciona participation_id, se valida que pertenezca
     * al usuario y a la actividad solicitada.
     *
     * Si no se proporciona, se busca exclusivamente una participación
     * convencional, sin GameSession asociada.
     *
     * @throws RuntimeException
     */
    public function resolve(
        Activity $activity,
        ?int $participationId = null,
    ): Participation {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user === null || $user->role !== 'student') {
            abort(403, 'Solo un estudiante puede responder.');
        }

        if ($participationId !== null) {
            $participation = Participation::query()
                ->whereKey($participationId)
                ->where('activity_id', $activity->id)
                ->firstOrFail();

            if ($participation->game_session_id === null) {
                return $this->resolveRegularParticipation(
                    $activity,
                    $user,
                    $participationId,
                );
            }

            return $this->resolveSessionParticipation(
                $activity,
                $user,
                $participationId,
            );
        }

        return $this->resolveRegularParticipation(
            $activity,
            $user,
            $participationId,
        );
    }

    /**
     * Autoriza y ejecuta una respuesta mientras mantiene bloqueadas la
     * sesión y la participación. Esto cierra la ventana entre validar el
     * estado y guardar una respuesta.
     */
    public function answer(
        Activity $activity,
        ?int $participationId,
        ?int $expectedSessionId,
        callable $callback,
    ): mixed {
        $rejection = null;
        $result = DB::transaction(function () use ($activity, $participationId, $expectedSessionId, $callback, &$rejection) {
            /** @var User|null $user */
            $user = Auth::user();
            abort_unless($user !== null && $user->role === 'student', 403);

            $participationQuery = Participation::query()
                ->where('activity_id', $activity->id);

            $candidate = null;
            if ($participationId !== null) {
                $candidate = Participation::query()
                    ->whereKey($participationId)
                    ->firstOrFail();

                abort_unless(
                    $candidate->activity_id === $activity->id,
                    $expectedSessionId === null ? 403 : 404,
                );

                if ($candidate->game_session_id !== null) {
                    $participationQuery->whereKey($candidate->id);
                } else {
                    $participationQuery
                        ->whereKey($candidate->id)
                        ->whereNull('game_session_id');
                }
            } else {
                $participationQuery
                    ->whereNull('game_session_id')
                    ->where('status', 'started');
            }

            if ($participationId === null) {
                if ($activity->mode === 'team') {
                    $team = Team::query()
                        ->where('activity_id', $activity->id)
                        ->whereHas('members', fn ($query) => $query->where('student_id', $user->id))
                        ->first();

                    abort_if($team === null, 404, 'No perteneces a un equipo de esta actividad.');
                    $participationQuery->where('team_id', $team->id);
                } else {
                    $participationQuery->where('student_id', $user->id);
                }
            }

            $participation = $participationQuery->firstOrFail();
            $session = null;

            if ($participation->game_session_id !== null) {
                abort_unless(
                    $expectedSessionId !== null
                    && $participation->game_session_id === $expectedSessionId,
                    409,
                    'La participación no pertenece a la sala solicitada.',
                );

                $session = GameSession::query()
                    ->whereKey($participation->game_session_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_unless($session->activity_id === $activity->id, 403);

                /*
                 * Cierra una carrera vencida o procesa el cierre de una ronda.
                 * La comprobación temporal se realiza en el servidor.
                 */
                $session = $this->gameSessionService->closeRoundIfDue($session);
                $session->refresh();

                if ($session->status !== GameSession::STATUS_PLAYING) {
                    $rejection = [409, 'La partida ya no acepta respuestas.'];

                    return null;
                }

                Gate::authorize('play', $session);

                if ($this->gameSessionService->isExpired($session)) {
                    $this->gameSessionService->finish($session);
                    $rejection = [409, 'El tiempo de la partida ha terminado.'];

                    return null;
                }
            } elseif ($expectedSessionId !== null) {
                abort(409, 'La participación no pertenece a una sala.');
            }

            $participation = Participation::query()
                ->whereKey($participation->id)
                ->lockForUpdate()
                ->firstOrFail();

            Gate::authorize('answer', $participation);

            if ($session === null && $this->participationService->hasExpired($participation, $activity)) {
                $this->participationService->expire($participation);
                $rejection = [410, 'El tiempo de la actividad ha terminado.'];

                return null;
            }

            $scoreBefore = $participation->score;

            $result = $callback($participation);

            if ($session !== null) {
                $this->gameSessionService->closeRoundIfDue($session->fresh());

                $participation->refresh();

                if ($participation->score !== $scoreBefore) {
                    // Mantiene el panel del profesor en sincronía: los juegos
                    // de carrera solo notifican cuando la puntuación cambió.
                    $this->gameSessionService->notifyScore($session, $participation);
                }
            }

            return $result;
        });

        if ($rejection !== null) {
            abort($rejection[0], $rejection[1]);
        }

        return $result;
    }

    /**
     * Resuelve una participación perteneciente a una sala multiplayer.
     */
    private function resolveSessionParticipation(
        Activity $activity,
        User $user,
        int $participationId,
    ): Participation {
        $participation = Participation::query()
            ->whereKey($participationId)
            ->where('activity_id', $activity->id)
            ->whereNotNull('game_session_id')
            ->firstOrFail();

        Gate::authorize('answer', $participation);

        $session = GameSession::query()
            ->whereKey($participation->game_session_id)
            ->firstOrFail();

        abort_unless(
            $session->activity_id === $activity->id,
            403,
            'La sala no corresponde a esta actividad.',
        );

        /*
         * Cierra una carrera vencida o procesa el cierre de una ronda.
         * La comprobación temporal se realiza en el servidor.
         */
        $session = $this->gameSessionService
            ->closeRoundIfDue($session);

        abort_unless(
            $session->status === GameSession::STATUS_PLAYING,
            409,
            'La partida ya no acepta respuestas.',
        );

        Gate::authorize('play', $session);

        /*
         * Comprobación explícita adicional del límite global.
         */
        if ($this->gameSessionService->isExpired($session)) {
            $this->gameSessionService->finish($session);

            abort(409, 'El tiempo de la partida ha terminado.');
        }

        /*
         * Volver a consultar la participación evita utilizar una
         * instancia cuyo estado pudo cambiar durante el cierre.
         */
        $participation->refresh();

        abort_unless(
            $participation->game_session_id === $session->id
            && $participation->activity_id === $activity->id,
            409,
            'La participación ya no pertenece a esta partida.',
        );

        Gate::authorize('answer', $participation);

        return $participation;
    }

    /**
     * Resuelve una participación individual o por equipos.
     *
     * Excluye expresamente las participaciones multiplayer.
     */
    private function resolveRegularParticipation(
        Activity $activity,
        User $user,
        ?int $participationId = null,
    ): Participation {
        $query = Participation::query()
            ->where('activity_id', $activity->id)
            ->where('status', 'started')
            ->whereNull('game_session_id');

        if ($participationId !== null) {
            $query->whereKey($participationId);
        }

        if ($activity->mode === 'team') {
            $team = Team::query()
                ->where('activity_id', $activity->id)
                ->whereHas(
                    'members',
                    fn ($query) => $query->where(
                        'student_id',
                        $user->id,
                    ),
                )
                ->first();

            if ($team === null) {
                abort(
                    404,
                    'No perteneces a un equipo de esta actividad.',
                );
            }

            $query->where('team_id', $team->id);
        } else {
            $query->where('student_id', $user->id);
        }

        $participation = $query
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->first();

        if ($participation === null) {
            abort(
                404,
                'No tienes una participación convencional activa.',
            );
        }

        if ($this->participationService->hasExpired(
            $participation,
            $activity,
        )) {
            $this->participationService->expire($participation);

            abort(410, 'El tiempo de la actividad ha terminado.');
        }

        Gate::authorize('answer', $participation);

        return $participation;
    }
}
