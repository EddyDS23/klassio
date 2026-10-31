<?php

namespace App\Services;

use App\Events\GameFinished;
use App\Events\GameStarted;
use App\Events\PlayerJoined;
use App\Events\PlayerLeft;
use App\Events\RoundClosed;
use App\Events\RoundStarted;
use App\Events\ScoreUpdated;
use App\Events\TurnChanged;
use App\Models\Activity;
use App\Models\CrosswordAnswer;
use App\Models\Enrollment;
use App\Models\GameSession;
use App\Models\GameSessionPresence;
use App\Models\KahootAnswer;
use App\Models\MatchingAnswer;
use App\Models\Participation;
use App\Models\Question;
use App\Models\RouletteAnswer;
use App\Models\RouletteItem;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\WordsearchAnswer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GameSessionService
{
    /**
     * Minimo de jugadores para iniciar una partida.
     */
    public const MIN_PLAYERS = 2;

    public const MAX_PLAYERS = 16;

    /**
     * Segundos sin presencia registrada antes de considerar que un
     * jugador se desconectó y liberar su lugar en la partida.
     */
    public const PRESENCE_TIMEOUT_SECONDS = 120;

    /**
     * Segundos por defecto para responder cada ronda de la ruleta.
     *
     * El profesor elige el tiempo antes de girar; este valor se usa
     * cuando no envía ninguno.
     */
    public const ROULETTE_ROUND_SECONDS = 30;

    /**
     * Límites del tiempo configurable por ronda.
     */
    public const MIN_ROUND_SECONDS = 5;

    public const MAX_ROUND_SECONDS = 300;

    /**
     * Texto con el que se registra una respuesta no enviada al agotarse
     * el tiempo de la ronda (fallo).
     */
    public const MISSED_RESPONSE = '(sin respuesta)';

    /**
     * Tipos de actividad que se juegan por rondas controladas por el
     * profesor (el profesor abre cada ronda).
     */
    public const ROUND_TYPES = ['roulette', 'kahoot'];

    /**
     * Tipos de actividad que se juegan en "carrera libre": el profesor
     * solo inicia y fija el time_limit; cada jugador resuelve todo el
     * juego a su ritmo y gana quien más puntaje obtiene.
     */
    public const RACE_TYPES = ['crossword', 'word_search', 'matching'];

    /**
     * Crea una sala en estado waiting y asigna un código único.
     */
    public function create(
        Activity $activity,
        int $maxPlayers,
        int $creatorId
    ): GameSession {
        return GameSession::create([
            'activity_id' => $activity->id,
            'code' => $this->generateUniqueCode(),
            'status' => GameSession::STATUS_WAITING,
            'max_players' => max(
                self::MIN_PLAYERS,
                min($maxPlayers, self::MAX_PLAYERS)
            ),
            'created_by' => $creatorId,
            'state' => [],
        ]);
    }

    /**
     * Cierra una sala que sigue esperando y nunca ha tenido participantes.
     */
    public function cancelWaitingSession(GameSession $session): GameSession
    {
        return DB::transaction(function () use ($session): GameSession {
            $session = GameSession::query()
                ->whereKey($session->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($session->status !== GameSession::STATUS_WAITING) {
                throw new RuntimeException('Solo se pueden cerrar salas que siguen en espera.');
            }

            if ($session->participations()->exists()) {
                throw new RuntimeException('No se puede cerrar la sala porque ya tuvo participantes.');
            }

            $session->update(['status' => GameSession::STATUS_CANCELLED]);

            return $session->fresh();
        });
    }

    public function findByCode(string $code): ?GameSession
    {
        return GameSession::where('code', mb_strtoupper(trim($code)))
            ->first();
    }

    public function isParticipant(GameSession $session, User $user): bool
    {
        $query = $session->participations()
            ->whereIn('status', ['waiting', 'started']);

        if ($session->activity->mode === 'team') {
            return false;
        }

        return $query->where('student_id', $user->id)->exists();
    }

    public function participationOf(
        GameSession $session,
        User $user
    ): ?Participation {
        $query = $session->participations()
            ->whereIn('status', ['waiting', 'started', 'completed'])
            ->orderByDesc('id');

        if ($session->activity->mode === 'team') {
            $teamId = Team::query()
                ->where('activity_id', $session->activity_id)
                ->whereHas('members', fn ($members) => $members->where('student_id', $user->id))
                ->value('id');

            if ($teamId === null) {
                return null;
            }

            return $query->where('team_id', $teamId)->orderBy('id')->first();
        }

        return $query->where('student_id', $user->id)
            ->orderBy('id')
            ->first();
    }

    public function canAnswerCurrentRound(
        GameSession $session,
        Participation $participation,
        User $user
    ): bool {
        if ($session->activity?->mode !== 'team') {
            return true;
        }

        return $this->currentRoundResponderId($session, $participation) === $user->id
            && GameSessionPresence::query()
                ->where('game_session_id', $session->id)
                ->where('participation_id', $participation->id)
                ->where('student_id', $user->id)
                ->where('status', GameSessionPresence::STATUS_ACTIVE)
                ->exists();
    }

    public function currentRoundResponderId(
        GameSession $session,
        Participation $participation
    ): ?int {
        if ($this->currentRoundItemId($session) === null) {
            return null;
        }

        $responders = $session->state['current_round_responders'] ?? [];
        $responderId = $responders[(string) $participation->id]
            ?? $responders[$participation->id]
            ?? null;

        return $responderId === null ? null : (int) $responderId;
    }

    public function currentRoundResponderName(
        GameSession $session,
        Participation $participation
    ): ?string {
        $responderId = $this->currentRoundResponderId($session, $participation);

        if ($responderId === null) {
            return null;
        }

        return User::query()->whereKey($responderId)->value('name');
    }

    /**
     * Un jugador entra a la sala.
     *
     * @return Participation Participación en estado waiting.
     */
    public function join(GameSession $session, User $student): Participation
    {
        return DB::transaction(function () use ($session, $student): Participation {
            $session = GameSession::query()
                ->whereKey($session->id)
                ->lockForUpdate()
                ->firstOrFail();
            $activity = $session->activity;
            $team = null;

            if ($activity->status !== 'published') {
                throw new RuntimeException('La actividad no está disponible.');
            }

            if ($activity->due_at !== null && now()->isAfter($activity->due_at)) {
                throw new RuntimeException('La fecha límite de esta actividad ya pasó.');
            }

            $enrolled = Enrollment::where('class_id', $activity->class_id)
                ->where('student_id', $student->id)
                ->where('status', 'active')
                ->exists();

            if (! $enrolled) {
                throw new RuntimeException('No estás inscrito en la clase de esta actividad.');
            }

            if ($activity->mode === 'team') {
                $team = Team::query()
                    ->where('activity_id', $activity->id)
                    ->whereHas('members', fn ($members) => $members->where('student_id', $student->id))
                    ->first();

                if ($team === null) {
                    throw new RuntimeException('No perteneces a un equipo de esta actividad.');
                }

                $teamParticipation = $session->participations()
                    ->where('team_id', $team->id)
                    ->whereIn('status', ['waiting', 'started', 'completed'])
                    ->lockForUpdate()
                    ->first();

                if (
                    $teamParticipation === null &&
                    in_array($session->status, [GameSession::STATUS_WAITING, GameSession::STATUS_STARTING], true)
                ) {
                    $teamParticipation = $session->participations()
                        ->where('team_id', $team->id)
                        ->where('status', 'abandoned')
                        ->latest('id')
                        ->lockForUpdate()
                        ->first();
                }

                $presence = GameSessionPresence::query()
                    ->where('game_session_id', $session->id)
                    ->where('student_id', $student->id)
                    ->lockForUpdate()
                    ->first();

                if ($presence?->status === GameSessionPresence::STATUS_ACTIVE) {
                    throw new RuntimeException('Ya estás en esta sala.');
                }

                $isOpenForNewTeam = in_array(
                    $session->status,
                    [GameSession::STATUS_WAITING, GameSession::STATUS_STARTING],
                    true
                );
                $isOpenForExistingTeamMember = $session->status === GameSession::STATUS_PLAYING
                    && in_array($teamParticipation?->status, ['started', 'completed'], true);

                if (! $isOpenForNewTeam && ! $isOpenForExistingTeamMember) {
                    throw new RuntimeException('Esta sala ya no acepta jugadores.');
                }

                $isNewTeamSeat = $teamParticipation === null;

                if ($isNewTeamSeat) {
                    $this->ensureSeatAvailable($session);

                    $teamParticipation = Participation::create([
                        'game_session_id' => $session->id,
                        'activity_id' => $activity->id,
                        'student_id' => null,
                        'team_id' => $team->id,
                        'attempt' => 1,
                        'status' => 'waiting',
                        'score' => 0,
                    ]);

                } elseif ($teamParticipation->status === 'abandoned') {
                    $this->ensureSeatAvailable($session);

                    $teamParticipation->update([
                        'status' => 'waiting',
                        'completed_at' => null,
                        'elapsed_seconds' => null,
                        'score' => 0,
                    ]);
                }

                if ($isNewTeamSeat || $teamParticipation->wasChanged('status')) {
                    DB::afterCommit(fn () => event(new PlayerJoined($session, [
                        'participation_id' => $teamParticipation->id,
                        'name' => $team->name,
                    ])));
                }

                if ($presence === null) {
                    GameSessionPresence::create([
                        'game_session_id' => $session->id,
                        'participation_id' => $teamParticipation->id,
                        'student_id' => $student->id,
                        'status' => GameSessionPresence::STATUS_ACTIVE,
                        'joined_at' => now(),
                    ]);
                } else {
                    $presence->update([
                        'participation_id' => $teamParticipation->id,
                        'status' => GameSessionPresence::STATUS_ACTIVE,
                        'joined_at' => now(),
                        'left_at' => null,
                    ]);
                }

                $this->assignResponderAfterJoin($session, $teamParticipation, $student->id);

                return $teamParticipation;
            }

            if (! in_array($session->status, [GameSession::STATUS_WAITING, GameSession::STATUS_STARTING], true)) {
                throw new RuntimeException('Esta sala ya no acepta jugadores.');
            }

            if ($this->isParticipant($session, $student)) {
                throw new RuntimeException('Ya estás en esta sala.');
            }

            $this->ensureSeatAvailable($session);

            $participation = Participation::create([
                'game_session_id' => $session->id,
                'activity_id' => $activity->id,
                'student_id' => $team === null ? $student->id : null,
                'team_id' => $team?->id,
                'attempt' => 1,
                'status' => 'waiting',
                'score' => 0,
            ]);

            DB::afterCommit(fn () => event(new PlayerJoined($session, [
                'participation_id' => $participation->id,
                'name' => $team?->name ?? $student->name,
            ])));

            return $participation;
        });
    }

    /**
     * Un jugador abandona la sala o la partida.
     */
    public function leave(GameSession $session, User $user): GameSession
    {
        return DB::transaction(function () use ($session, $user): GameSession {
            $session = GameSession::query()->whereKey($session->id)->lockForUpdate()->firstOrFail();
            $participation = $this->participationOf($session, $user);

            if ($participation === null) {
                throw new RuntimeException('No perteneces a esta sala.');
            }

            $participation = Participation::query()
                ->whereKey($participation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($session->status, [GameSession::STATUS_FINISHED, GameSession::STATUS_CANCELLED], true)) {
                throw new RuntimeException('Esta partida ya finalizó.');
            }

            if ($participation->team_id !== null) {
                $hasTrackedPresence = GameSessionPresence::query()
                    ->where('participation_id', $participation->id)
                    ->exists();

                if (! $hasTrackedPresence) {
                    TeamMember::query()
                        ->where('team_id', $participation->team_id)
                        ->get()
                        ->each(function (TeamMember $member) use ($session, $participation): void {
                            GameSessionPresence::create([
                                'game_session_id' => $session->id,
                                'participation_id' => $participation->id,
                                'student_id' => $member->student_id,
                                'status' => GameSessionPresence::STATUS_ACTIVE,
                                'joined_at' => now(),
                            ]);
                        });
                }

                $presence = GameSessionPresence::query()
                    ->where('game_session_id', $session->id)
                    ->where('participation_id', $participation->id)
                    ->where('student_id', $user->id)
                    ->where('status', GameSessionPresence::STATUS_ACTIVE)
                    ->lockForUpdate()
                    ->first();

                if ($presence === null) {
                    throw new RuntimeException('No tienes una presencia activa en esta sala.');
                }

                $presence->update([
                    'status' => GameSessionPresence::STATUS_LEFT,
                    'left_at' => now(),
                ]);

                $this->reassignResponderAfterLeave($session, $participation, $user->id);

                if ($participation->status === 'completed') {
                    return $session->fresh();
                }

                $hasActiveTeammate = GameSessionPresence::query()
                    ->where('participation_id', $participation->id)
                    ->where('status', GameSessionPresence::STATUS_ACTIVE)
                    ->exists();

                if ($hasActiveTeammate) {
                    return $session->fresh();
                }
            }

            if (in_array($participation->status, ['completed', 'abandoned', 'expired'], true)) {
                return $session;
            }

            $participation->status = 'abandoned';
            $participation->completed_at = now();
            $participation->elapsed_seconds = $this->elapsedSeconds($session);
            $participation->save();

            $wasTurn = $session->fresh()->current_turn_participation_id === $participation->id;

            if ($wasTurn) {
                $this->nextTurn($session->fresh());
            }

            if ($session->status === GameSession::STATUS_PLAYING) {
                $activePlayers = $session->participations()
                    ->where('status', 'started')
                    ->count();

                if ($activePlayers < self::MIN_PLAYERS) {
                    return $this->finish($session->fresh());
                }
            }

            if (
                $session->status === GameSession::STATUS_WAITING &&
                $session->participations()
                    ->whereIn('status', ['waiting', 'started'])
                    ->count() === 0
            ) {
                $session->fresh()->update([
                    'status' => GameSession::STATUS_CANCELLED,
                ]);
            }

            DB::afterCommit(fn () => event(new PlayerLeft($session->fresh(), [
                'participation_id' => $participation->id,
                'name' => $participation->team?->name ?? $user->name,
            ])));

            return $session->fresh();
        });
    }

    /**
     * Expira las presencias activas que llevan demasiado tiempo sin
     * actualizarse y reasigna el turno cuando el respondedor expiró,
     * de esta forma un jugador con desconexión abrupta puede volver a
     * entrar a la partida con el código de la sala.
     *
     * @return list<GameSessionPresence>
     */
    public function expireStalePresences(GameSession $session, int $seconds = self::PRESENCE_TIMEOUT_SECONDS): array
    {
        $stalePresences = GameSessionPresence::query()
            ->where('game_session_id', $session->id)
            ->where('status', GameSessionPresence::STATUS_ACTIVE)
            ->where('joined_at', '<', now()->subSeconds($seconds))
            ->get();

        foreach ($stalePresences as $presence) {
            $presence->update([
                'status' => GameSessionPresence::STATUS_LEFT,
                'left_at' => now(),
            ]);

            $this->reassignResponderAfterExpired($session, $presence);
        }

        return $stalePresences->all();
    }

    private function reassignResponderAfterExpired(GameSession $session, GameSessionPresence $presence): void
    {
        if ($this->currentRoundItemId($session) === null) {
            return;
        }

        $participation = $presence->participation;

        if ($participation === null) {
            return;
        }

        $responderId = $this->currentRoundResponderId($session, $participation);

        if ($responderId === null || (int) $responderId !== (int) $presence->student_id) {
            return;
        }

        $this->reassignResponderAfterLeave($session, $participation, (int) $presence->student_id);
    }

    private function ensureSeatAvailable(GameSession $session): void
    {
        $count = $session->participations()
            ->whereIn('status', ['waiting', 'started'])
            ->count();

        if ($count >= $session->max_players) {
            throw new RuntimeException('La sala alcanzó su máximo de jugadores.');
        }
    }

    private function assignTeamRespondersForNewRound(GameSession $session, array &$state): void
    {
        if ($session->activity?->mode !== 'team') {
            unset($state['current_round_responders']);

            return;
        }

        $lastResponders = $state['last_round_responders'] ?? [];
        $currentResponders = [];
        $participations = $session->participations()
            ->where('status', 'started')
            ->whereNotNull('team_id')
            ->orderBy('id')
            ->get(['id']);

        foreach ($participations as $participation) {
            $activeStudentIds = $this->activeTeamPresenceStudentIds($session, $participation);
            $lastResponderId = $lastResponders[(string) $participation->id]
                ?? $lastResponders[$participation->id]
                ?? null;
            $nextResponderId = $this->nextStudentId($activeStudentIds, $lastResponderId);

            if ($nextResponderId === null) {
                continue;
            }

            $currentResponders[(string) $participation->id] = $nextResponderId;
            $lastResponders[(string) $participation->id] = $nextResponderId;
        }

        $state['current_round_responders'] = $currentResponders;
        $state['last_round_responders'] = $lastResponders;
    }

    private function assignResponderAfterJoin(
        GameSession $session,
        Participation $participation,
        int $studentId
    ): void {
        if (
            $session->activity?->mode !== 'team'
            || $participation->status !== 'started'
            || $this->currentRoundItemId($session) === null
        ) {
            return;
        }

        $state = $session->state ?? [];
        $currentResponders = $state['current_round_responders'] ?? [];
        $participationId = (string) $participation->id;

        if (isset($currentResponders[$participationId])) {
            return;
        }

        $currentResponders[$participationId] = $studentId;
        $state['current_round_responders'] = $currentResponders;
        $state['last_round_responders'][$participationId] = $studentId;
        $session->update(['state' => $state]);

        DB::afterCommit(fn () => event(new TurnChanged($session->fresh(), [
            'participation_id' => $participation->id,
            'responder_id' => $studentId,
        ])));
    }

    /**
     * @return list<int>
     */
    private function activeTeamPresenceStudentIds(
        GameSession $session,
        Participation $participation
    ): array {
        return GameSessionPresence::query()
            ->where('game_session_id', $session->id)
            ->where('participation_id', $participation->id)
            ->where('status', GameSessionPresence::STATUS_ACTIVE)
            ->orderBy('joined_at')
            ->orderBy('student_id')
            ->pluck('student_id')
            ->map(static fn (int|string $studentId): int => (int) $studentId)
            ->all();
    }

    /**
     * @param  list<int>  $studentIds
     */
    private function nextStudentId(array $studentIds, int|string|null $lastStudentId): ?int
    {
        if ($studentIds === []) {
            return null;
        }

        if ($lastStudentId === null) {
            return $studentIds[0];
        }

        $lastIndex = array_search((int) $lastStudentId, $studentIds, true);

        if ($lastIndex === false) {
            return $studentIds[0];
        }

        return $studentIds[($lastIndex + 1) % count($studentIds)];
    }

    private function reassignResponderAfterLeave(
        GameSession $session,
        Participation $participation,
        int $studentId
    ): void {
        if (
            $session->activity?->mode !== 'team'
            || $this->currentRoundItemId($session) === null
        ) {
            return;
        }

        $state = $session->state ?? [];
        $currentResponders = $state['current_round_responders'] ?? [];
        $participationId = (string) $participation->id;

        if ((int) ($currentResponders[$participationId] ?? 0) !== $studentId) {
            return;
        }

        $activeStudentIds = $this->activeTeamPresenceStudentIds($session, $participation);
        $nextResponderId = $this->nextStudentId($activeStudentIds, $studentId);

        if ($nextResponderId === null) {
            unset($currentResponders[$participationId]);
        } else {
            $currentResponders[$participationId] = $nextResponderId;
            $state['last_round_responders'][$participationId] = $nextResponderId;
        }

        $state['current_round_responders'] = $currentResponders;
        $session->update(['state' => $state]);

        DB::afterCommit(fn () => event(new TurnChanged($session->fresh(), [
            'participation_id' => $participation->id,
            'responder_id' => $nextResponderId,
        ])));
    }

    /**
     * Jugadores dentro de la sala.
     */
    public function players(GameSession $session): Collection
    {
        return $session->participations()
            ->with('student')
            ->whereIn('status', ['waiting', 'started'])
            ->orderBy('id')
            ->get();
    }

    public function canStart(GameSession $session): bool
    {
        $count = $session->participations()
            ->whereIn('status', ['waiting', 'started'])
            ->count();

        return in_array($session->status, [GameSession::STATUS_WAITING, GameSession::STATUS_STARTING], true)
            && $count >= self::MIN_PLAYERS;
    }

    /**
     * Inicia la partida: cambia el estado de la sala, inicia las
     * participaciones y asigna el primer turno.
     */
    public function start(GameSession $session): GameSession
    {
        return DB::transaction(function () use ($session): GameSession {
            $session = GameSession::query()->whereKey($session->id)->lockForUpdate()->firstOrFail();

            if (! $this->canStart($session)) {
                throw new RuntimeException('Se necesitan al menos 2 jugadores para iniciar.');
            }

            $session->update([
                'status' => GameSession::STATUS_PLAYING,
                'started_at' => now(),
            ]);

            $startedAt = now();

            $startedParticipationIds = [];

            foreach ($this->players($session) as $participation) {
                $participation = Participation::query()->whereKey($participation->id)->lockForUpdate()->firstOrFail();
                $participation->status = 'started';
                $participation->started_at = $startedAt;
                $participation->save();
                $startedParticipationIds[] = $participation->id;
            }

            GameSessionPresence::query()
                ->whereIn('participation_id', $startedParticipationIds)
                ->where('status', GameSessionPresence::STATUS_ACTIVE)
                ->update(['joined_at' => now()]);

            $first = $session->participations()
                ->where('status', 'started')
                ->orderBy('id')
                ->first();

            $session->update([
                'current_turn_participation_id' => $first?->id,
            ]);

            DB::afterCommit(fn () => event(new GameStarted($session->fresh())));

            return $session->fresh();
        });
    }

    public function currentPlayer(GameSession $session): ?Participation
    {
        if ($session->current_turn_participation_id === null) {
            return null;
        }

        return $session->participations()
            ->find($session->current_turn_participation_id);
    }

    /**
     * Avanza el turno al siguiente jugador activo.
     *
     * @return Participation|null null si la partida terminó (menos de 2 activos)
     */
    public function nextTurn(GameSession $session): ?Participation
    {
        if ($session->status !== GameSession::STATUS_PLAYING) {
            return null;
        }

        $players = $session->participations()
            ->where('status', 'started')
            ->orderBy('id')
            ->get();

        if ($players->count() < self::MIN_PLAYERS) {
            $this->finish($session);

            return null;
        }

        $currentId = $session->current_turn_participation_id;

        $index = 0;

        if ($currentId !== null) {
            $currentIndex = $players->search(
                fn (Participation $p) => $p->id === $currentId
            );

            if ($currentIndex !== false) {
                $index = ($currentIndex + 1) % $players->count();
            }
        }

        $next = $players->get($index);

        $session->update([
            'current_turn_participation_id' => $next->id,
        ]);

        DB::afterCommit(fn () => event(new TurnChanged($session->fresh(), [
            'participation_id' => $next->id,
            'name' => $next->team?->name ?? $next->student?->name,
        ])));

        return $next->fresh();
    }

    /**
     * Inicia una nueva ronda de la ruleta. Solo el profesor gira.
     *
     * - Cierra la ronda anterior si ya terminó (marca fallos por tiempo).
     * - Selecciona un ítem aleatorio no usado.
     * - Si no quedan ítems, finaliza la partida.
     *
     * Es segura ante peticiones concurrentes.
     */
    public function startRound(
        GameSession $session,
        int $seconds = self::ROULETTE_ROUND_SECONDS
    ): GameSession {
        return DB::transaction(function () use ($session, $seconds) {
            $locked = GameSession::whereKey($session->getKey())
                ->lockForUpdate()
                ->first();

            if ($locked === null || $locked->status !== GameSession::STATUS_PLAYING) {
                throw new RuntimeException('La partida no está en curso.');
            }

            $roulette = $locked->activity?->roulette;

            if ($roulette === null) {
                throw new RuntimeException('Esta actividad no tiene una ruleta configurada.');
            }

            if ($this->isExpired($locked)) {
                return $this->finish($locked);
            }

            $locked = $this->closeRoundIfDueLocked($locked);

            if ($locked->status !== GameSession::STATUS_PLAYING) {
                return $locked;
            }

            if ($this->currentRouletteItemId($locked) !== null) {
                throw new RuntimeException('Espera a que termine la ronda actual.');
            }

            $state = $locked->state ?? [];
            $used = $state['round_item_ids'] ?? [];
            unset($state['last_closed_round_item_id']);

            $candidates = $roulette->items()
                ->when($used, fn ($query) => $query->whereNotIn('id', $used))
                ->get();

            if ($candidates->isEmpty()) {
                return $this->finish($locked);
            }

            $next = $candidates->random();

            $safeSeconds = max(
                self::MIN_ROUND_SECONDS,
                min($seconds, self::MAX_ROUND_SECONDS)
            );

            $state['roulette_item_id'] = $next->id;
            $state['round_started_at'] = now()->toIso8601String();
            $state['round_seconds'] = $safeSeconds;
            $state['round_item_ids'] = array_merge($used, [$next->id]);
            $this->assignTeamRespondersForNewRound($locked, $state);

            $locked->update(['state' => $state]);

            DB::afterCommit(fn () => event(new RoundStarted($locked->fresh(), [
                'roulette_item_id' => $next->id,
                'round_seconds' => $safeSeconds,
            ])));

            return $locked->fresh();
        });
    }

    /**
     * Inicia una nueva ronda de kahoot. Solo el profesor avanza.
     *
     * Igual que la ruleta: cierra la ronda anterior si terminó (marca
     * fallos por tiempo), elige una pregunta aleatoria no usada y, si no
     * quedan, finaliza la partida.
     *
     * Es segura ante peticiones concurrentes.
     */
    public function startKahootRound(
        GameSession $session,
        int $seconds = self::ROULETTE_ROUND_SECONDS
    ): GameSession {
        return DB::transaction(function () use ($session, $seconds) {
            $locked = GameSession::whereKey($session->getKey())
                ->lockForUpdate()
                ->first();

            if ($locked === null || $locked->status !== GameSession::STATUS_PLAYING) {
                throw new RuntimeException('La partida no está en curso.');
            }

            $kahoot = $locked->activity?->kahoot;

            if ($kahoot === null) {
                throw new RuntimeException('Esta actividad no tiene un kahoot configurado.');
            }

            if ($this->isExpired($locked)) {
                return $this->finish($locked);
            }

            $locked = $this->closeRoundIfDueLocked($locked);

            if ($locked->status !== GameSession::STATUS_PLAYING) {
                return $locked;
            }

            if ($this->currentKahootQuestionId($locked) !== null) {
                throw new RuntimeException('Espera a que termine la pregunta actual.');
            }

            $state = $locked->state ?? [];
            $used = $state['round_question_ids'] ?? [];
            unset($state['last_closed_round_item_id']);

            $candidates = $kahoot->questions()
                ->when($used, fn ($query) => $query->whereNotIn('id', $used))
                ->get();

            if ($candidates->isEmpty()) {
                return $this->finish($locked);
            }

            $next = $candidates->random();

            $safeSeconds = max(
                self::MIN_ROUND_SECONDS,
                min($seconds, self::MAX_ROUND_SECONDS)
            );

            $state['kahoot_question_id'] = $next->id;
            $state['round_started_at'] = now()->toIso8601String();
            $state['round_seconds'] = $safeSeconds;
            $state['round_question_ids'] = array_merge($used, [$next->id]);
            $this->assignTeamRespondersForNewRound($locked, $state);

            $locked->update(['state' => $state]);

            DB::afterCommit(fn () => event(new RoundStarted($locked->fresh(), [
                'question_id' => $next->id,
                'round_seconds' => $safeSeconds,
            ])));

            return $locked->fresh();
        });
    }

    /**
     * Cierra la ronda actual si ya se agotó el tiempo o todos
     * respondieron. Registra como fallo a quien no respondió y, si no
     * quedan ítems, finaliza la partida.
     *
     * Es idempotente y segura ante peticiones concurrentes.
     */
    public function closeRoundIfDue(GameSession $session): GameSession
    {
        return DB::transaction(function () use ($session) {
            $locked = GameSession::whereKey($session->getKey())
                ->lockForUpdate()
                ->first();

            if ($locked === null || $locked->status !== GameSession::STATUS_PLAYING) {
                return $locked ?? $session;
            }

            if ($this->isRace($locked)) {
                return $this->closeRaceIfDueLocked($locked);
            }

            return $this->closeRoundIfDueLocked($locked);
        });
    }

    /**
     * Cierra la carrera si ya terminó: se agotó el tiempo de la actividad
     * o todos los jugadores activos completaron el juego. El llamador ya
     * bloqueó la sesión dentro de una transacción.
     */
    private function closeRaceIfDueLocked(GameSession $locked): GameSession
    {
        if ($this->isExpired($locked)) {
            return $this->finish($locked);
        }

        $stillPlaying = $locked->participations()
            ->where('status', 'started')
            ->count();

        if ($stillPlaying === 0 && $locked->participations()->exists()) {
            return $this->finish($locked);
        }

        return $locked;
    }

    /**
     * Cierra la ronda si corresponde. El llamador ya bloqueó la sesión
     * dentro de una transacción.
     */
    private function closeRoundIfDueLocked(GameSession $locked): GameSession
    {
        $type = $locked->activity?->type;

        if (! in_array($type, ['roulette', 'kahoot'], true)) {
            return $locked;
        }

        if ($this->isExpired($locked)) {
            return $this->finish($locked);
        }

        $itemId = $this->currentRoundItemId($locked);

        if ($itemId === null) {
            return $locked;
        }

        $due = $this->roundTimedOut($locked)
            || $this->roundFullyAnswered($locked, $itemId);

        if (! $due) {
            return $locked;
        }

        $this->recordMisses($locked, $itemId);

        $state = $locked->state ?? [];
        $state['last_closed_round_item_id'] = $itemId;

        if ($type === 'kahoot') {
            unset($state['kahoot_question_id'], $state['round_seconds']);
        } else {
            unset($state['roulette_item_id'], $state['round_seconds']);
        }

        unset($state['current_round_responders']);

        $state['round_started_at'] = null;

        $locked->update(['state' => $state]);
        $locked = $locked->fresh();

        DB::afterCommit(fn () => event(new RoundClosed($locked->fresh(), [
            'roulette_item_id' => $type === 'roulette' ? $itemId : null,
            'question_id' => $type === 'kahoot' ? $itemId : null,
        ])));

        if ($this->noItemsRemain($locked)) {
            return $this->finish($locked);
        }

        return $locked;
    }

    /**
     * Registra un fallo (sin respuesta) para cada jugador activo que no
     * respondió el ítem al cerrarse la ronda.
     */
    private function recordMisses(GameSession $session, int $itemId): void
    {
        if ($this->isKahoot($session)) {
            $this->recordKahootMisses($session, $itemId);

            return;
        }

        $players = $session->participations()
            ->where('status', 'started')
            ->pluck('id');

        $answered = RouletteAnswer::where('roulette_item_id', $itemId)
            ->whereIn('participation_id', $players)
            ->pluck('participation_id');

        foreach ($players->diff($answered) as $participationId) {
            RouletteAnswer::create([
                'participation_id' => $participationId,
                'roulette_item_id' => $itemId,
                'response' => self::MISSED_RESPONSE,
                'is_correct' => false,
                'score' => 0,
                'answered_at' => now(),
            ]);
        }
    }

    /**
     * Registra un fallo (opción nula) para cada jugador activo que no
     * respondió la pregunta al cerrarse la ronda de kahoot.
     */
    private function recordKahootMisses(GameSession $session, int $questionId): void
    {
        $players = $session->participations()
            ->where('status', 'started')
            ->pluck('id');

        $answered = KahootAnswer::where('question_id', $questionId)
            ->whereIn('participation_id', $players)
            ->pluck('participation_id');

        foreach ($players->diff($answered) as $participationId) {
            KahootAnswer::create([
                'participation_id' => $participationId,
                'question_id' => $questionId,
                'option_id' => null,
                'is_correct' => false,
                'score' => 0,
                'answered_at' => now(),
            ]);
        }
    }

    /**
     * Indica si ya no quedan ítems/preguntas sin usar en la partida.
     */
    private function noItemsRemain(GameSession $session): bool
    {
        if ($this->isKahoot($session)) {
            $kahoot = $session->activity?->kahoot;

            if ($kahoot === null) {
                return true;
            }

            $used = $session->state['round_question_ids'] ?? [];

            return $kahoot->questions()
                ->when($used, fn ($query) => $query->whereNotIn('id', $used))
                ->count() === 0;
        }

        $roulette = $session->activity?->roulette;

        if ($roulette === null) {
            return true;
        }

        $used = $session->state['round_item_ids'] ?? [];

        return $roulette->items()
            ->when($used, fn ($query) => $query->whereNotIn('id', $used))
            ->count() === 0;
    }

    /**
     * Indica si la sesión pertenece a una actividad de kahoot.
     */
    public function isKahoot(GameSession $session): bool
    {
        return $session->activity?->type === 'kahoot';
    }

    /**
     * Indica si la sesión pertenece a un juego de "carrera libre".
     */
    public function isRace(GameSession $session): bool
    {
        return in_array(
            $session->activity?->type,
            self::RACE_TYPES,
            true
        );
    }

    /**
     * Id del ítem (ruleta) o pregunta (kahoot) de la ronda actual, o null.
     */
    public function currentRoundItemId(GameSession $session): ?int
    {
        return $this->isKahoot($session)
            ? $this->currentKahootQuestionId($session)
            : $this->currentRouletteItemId($session);
    }

    /**
     * Id de la pregunta de kahoot de la ronda actual, o null.
     */
    public function currentKahootQuestionId(GameSession $session): ?int
    {
        $id = $session->state['kahoot_question_id'] ?? null;

        return $id === null ? null : (int) $id;
    }

    /**
     * Id del ítem de la ronda actual de la ruleta, o null.
     */
    public function currentRouletteItemId(GameSession $session): ?int
    {
        $id = $session->state['roulette_item_id'] ?? null;

        return $id === null ? null : (int) $id;
    }

    /**
     * Segundos restantes de la ronda actual, o null si no hay ronda.
     */
    public function roundTimeRemaining(GameSession $session): ?int
    {
        $state = $session->state ?? [];

        if ($this->currentRoundItemId($session) === null) {
            return null;
        }

        $seconds = (int) ($state['round_seconds'] ?? self::ROULETTE_ROUND_SECONDS);
        $startedAt = $state['round_started_at'] ?? null;

        if ($startedAt === null) {
            return $seconds;
        }

        $elapsed = (int) abs(now()->diffInSeconds(Carbon::parse($startedAt)));

        return max(0, $seconds - $elapsed);
    }

    /**
     * Indica si la ronda actual superó su tiempo.
     */
    public function roundTimedOut(GameSession $session): bool
    {
        $remaining = $this->roundTimeRemaining($session);

        return $remaining !== null && $remaining <= 0;
    }

    /**
     * Indica si todos los jugadores activos respondieron el ítem dado.
     */
    public function roundFullyAnswered(GameSession $session, int $itemId): bool
    {
        $players = $session->participations()
            ->where('status', 'started')
            ->pluck('id');

        if ($players->isEmpty()) {
            return true;
        }

        $answered = $this->isKahoot($session)
            ? KahootAnswer::whereIn('participation_id', $players)
                ->where('question_id', $itemId)
                ->distinct()
                ->pluck('participation_id')
            : RouletteAnswer::whereIn('participation_id', $players)
                ->where('roulette_item_id', $itemId)
                ->distinct()
                ->pluck('participation_id');

        return $players->diff($answered)->isEmpty();
    }

    /**
     * Indica si la participación ya respondió el ítem de la ronda actual.
     */
    public function hasAnsweredCurrentRound(
        GameSession $session,
        Participation $participation
    ): bool {
        $itemId = $this->currentRoundItemId($session);

        if ($itemId === null) {
            return false;
        }

        return $this->isKahoot($session)
            ? KahootAnswer::where('participation_id', $participation->id)
                ->where('question_id', $itemId)
                ->exists()
            : RouletteAnswer::where('participation_id', $participation->id)
                ->where('roulette_item_id', $itemId)
                ->exists();
    }

    /**
     * Finaliza la partida y determina resultado general e individual.
     */
    public function finish(GameSession $session): GameSession
    {
        return DB::transaction(function () use ($session): GameSession {
            $session = GameSession::query()->whereKey($session->id)->lockForUpdate()->firstOrFail();

            if ($session->status === GameSession::STATUS_FINISHED) {
                return $session;
            }

            $participations = $session->participations()
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $finishedAt = now();
            foreach ($participations as $participation) {
                if ($participation->status === 'started') {
                    $elapsedSeconds = $session->activity->mode === 'individual'
                        ? $this->individualAnswerElapsedSeconds($session, $participation)
                        : null;

                    $participation->update([
                        'status' => 'completed',
                        'completed_at' => $finishedAt,
                        'elapsed_seconds' => $elapsedSeconds ?? $this->elapsedSeconds($session),
                    ]);
                } elseif ($participation->status === 'waiting') {
                    $participation->update(['status' => 'abandoned']);
                }
            }

            $eligible = $participations->whereIn('status', ['started', 'completed', 'expired']);
            $winner = null;
            $resultType = GameSession::RESULT_COLLABORATIVE;

            if ($eligible->isNotEmpty()) {
                $max = $eligible->max('score');
                $top = $eligible->filter(fn (Participation $p) => $p->score === $max);

                if ($top->count() === 1) {
                    $winner = $top->first();
                    $resultType = GameSession::RESULT_WINNER;
                } elseif ($session->activity->mode === 'individual'
                    && $top->every(fn (Participation $p) => $p->elapsed_seconds !== null)) {
                    $fastestTime = $top->min('elapsed_seconds');
                    $fastest = $top->filter(
                        fn (Participation $p) => (int) $p->elapsed_seconds === (int) $fastestTime
                    );

                    if ($fastest->count() === 1) {
                        $winner = $fastest->first();
                        $resultType = GameSession::RESULT_WINNER;
                    } else {
                        $resultType = GameSession::RESULT_DRAW;
                    }
                } else {
                    $resultType = GameSession::RESULT_DRAW;
                }
            }

            $session->update([
                'status' => GameSession::STATUS_FINISHED,
                'finished_at' => $finishedAt,
                'result_type' => $resultType,
                'winner_participation_id' => $winner?->id,
            ]);

            DB::afterCommit(fn () => event(new GameFinished($session->fresh())));

            return $session->fresh();
        });
    }

    private function individualAnswerElapsedSeconds(
        GameSession $session,
        Participation $participation
    ): ?int {
        if ($session->started_at === null) {
            return null;
        }

        $lastAnswerAt = match ($session->activity?->type) {
            'kahoot' => KahootAnswer::query()
                ->where('participation_id', $participation->id)
                ->max('answered_at'),
            'roulette' => RouletteAnswer::query()
                ->where('participation_id', $participation->id)
                ->max('answered_at'),
            'crossword' => CrosswordAnswer::query()
                ->where('participation_id', $participation->id)
                ->max('created_at'),
            'word_search' => WordsearchAnswer::query()
                ->where('participation_id', $participation->id)
                ->max('found_at'),
            'matching' => MatchingAnswer::query()
                ->where('participation_id', $participation->id)
                ->max('answered_at'),
            default => null,
        };

        if ($lastAnswerAt === null) {
            return null;
        }

        return max(0, Carbon::parse($lastAnswerAt)->timestamp - $session->started_at->timestamp);
    }

    /**
     * Notifica un cambio de puntuación a la sala.
     */
    public function notifyScore(GameSession $session, Participation $participation): void
    {
        DB::afterCommit(fn () => event(new ScoreUpdated($session->fresh(), [
            'participation_id' => $participation->id,
            'name' => $participation->team?->name ?? $participation->student?->name,
            'score' => $participation->score,
        ])));
    }

    /**
     * Indica si la partida superó el límite de tiempo de la actividad.
     */
    public function isExpired(GameSession $session): bool
    {
        $timeLimit = $session->activity->time_limit;

        if ($timeLimit === null || $session->started_at === null) {
            return false;
        }

        return $this->elapsedSeconds($session) >= (int) $timeLimit;
    }

    public function elapsedSeconds(GameSession $session): int
    {
        return (int) $session->started_at?->diffInSeconds(now());
    }

    public function remainingSeconds(GameSession $session): ?int
    {
        $timeLimit = $session->activity->time_limit;

        if ($timeLimit === null || $session->started_at === null) {
            return null;
        }

        return max(0, (int) $timeLimit - $this->elapsedSeconds($session));
    }

    /**
     * Returns the caller's team's solved items without exposing another team's
     * answers through the shared session state endpoint.
     *
     * @return array{participation_id:int,status:string,score:int,completed_item_ids:list<int>,incorrect_item_ids:list<int>}
     */
    public function teamProgress(Participation $participation): array
    {
        $completedItemIds = [];
        $incorrectItemIds = [];

        if ($participation->activity->type === 'crossword') {
            $answers = $participation->crosswordAnswers()
                ->get(['crossword_word_id', 'is_correct']);

            $completedItemIds = $answers
                ->pluck('crossword_word_id')
                ->map(fn ($id): int => (int) $id)
                ->all();
            $incorrectItemIds = $answers
                ->where('is_correct', false)
                ->pluck('crossword_word_id')
                ->map(fn ($id): int => (int) $id)
                ->all();
        } elseif ($participation->activity->type === 'matching') {
            $completedItemIds = $participation->matchingAnswers()
                ->where('is_correct', true)
                ->pluck('matching_item_id')
                ->map(fn ($id): int => (int) $id)
                ->all();
        } elseif ($participation->activity->type === 'word_search') {
            $completedItemIds = $participation->wordsearchAnswers()
                ->pluck('word_id')
                ->map(fn ($id): int => (int) $id)
                ->all();
        }

        return [
            'participation_id' => (int) $participation->id,
            'status' => $participation->status,
            'score' => (int) $participation->score,
            'completed_item_ids' => $completedItemIds,
            'incorrect_item_ids' => $incorrectItemIds,
        ];
    }

    /**
     * Estado compartido de la partida para el frontend.
     */
    public function getState(GameSession $session, ?Participation $viewer = null): array
    {
        $activity = $session->activity;

        $isKahoot = $this->isKahoot($session);

        $currentRouletteItemId = $isKahoot ? null : $this->currentRouletteItemId($session);
        $currentQuestionId = $isKahoot ? $this->currentKahootQuestionId($session) : null;
        $currentItemId = $isKahoot ? $currentQuestionId : $currentRouletteItemId;
        $currentAnswers = collect();
        $storedLastClosedItemId = $currentItemId === null
            ? ($session->state['last_closed_round_item_id'] ?? null)
            : null;
        $lastClosedItemId = $storedLastClosedItemId === null
            ? null
            : (int) $storedLastClosedItemId;
        $lastRoundAnswers = collect();

        if ($currentItemId !== null) {
            $currentAnswers = $isKahoot
                ? KahootAnswer::query()
                    ->where('question_id', $currentItemId)
                    ->get(['participation_id', 'is_correct'])
                    ->keyBy('participation_id')
                : RouletteAnswer::query()
                    ->where('roulette_item_id', $currentItemId)
                    ->get(['participation_id', 'is_correct'])
                    ->keyBy('participation_id');
        } elseif ($lastClosedItemId !== null) {
            $lastRoundAnswers = $isKahoot
                ? KahootAnswer::query()
                    ->where('question_id', $lastClosedItemId)
                    ->get(['participation_id', 'is_correct'])
                    ->keyBy('participation_id')
                : RouletteAnswer::query()
                    ->where('roulette_item_id', $lastClosedItemId)
                    ->get(['participation_id', 'is_correct'])
                    ->keyBy('participation_id');
        }

        $currentResponders = $session->state['current_round_responders'] ?? [];
        $responderIds = array_values(array_unique(array_map('intval', $currentResponders)));
        $responderNames = $responderIds === []
            ? collect()
            : User::query()->whereIn('id', $responderIds)->pluck('name', 'id');

        $players = $session->participations()
            ->with('student', 'team')
            ->whereIn('status', ['waiting', 'started', 'completed'])
            ->orderBy('id')
            ->get();

        return [
            'session_id' => $session->id,
            'code' => $session->code,
            'status' => $session->status,
            'activity' => [
                'id' => $activity->id,
                'title' => $activity->title,
                'type' => $activity->type,
                'max_score' => $activity->max_score,
            ],
            'max_players' => $session->max_players,
            'current_turn_participation_id' => $session->current_turn_participation_id,
            'result_type' => $session->result_type,
            'winner_participation_id' => $session->winner_participation_id,
            'time_remaining' => $this->remainingSeconds($session),
            'time_elapsed' => $session->started_at !== null ? $this->elapsedSeconds($session) : 0,
            'roulette_item_id' => $currentRouletteItemId,
            'question_id' => $currentQuestionId,
            'round_time_remaining' => $this->roundTimeRemaining($session),
            'round_seconds' => $session->state['round_seconds'] ?? self::ROULETTE_ROUND_SECONDS,
            'current_item' => $isKahoot
                ? $this->currentKahootQuestion($session, $currentItemId)
                : $this->currentRoundItem($session, $currentItemId),
            'last_round_item' => $isKahoot
                ? $this->currentKahootQuestion($session, $lastClosedItemId)
                : $this->currentRoundItem($session, $lastClosedItemId),
            'players' => $players
                ->map(function (Participation $p) use ($activity, $currentAnswers, $lastRoundAnswers, $currentResponders, $responderNames, $viewer): array {
                    $responderId = $currentResponders[(string) $p->id]
                        ?? $currentResponders[$p->id]
                        ?? null;
                    $answer = $currentAnswers->get($p->id);
                    $lastRoundAnswer = $lastRoundAnswers->get($p->id);

                    return [
                        'participation_id' => $p->id,
                        'name' => $p->team?->name ?? $p->student?->name,
                        'score' => $p->score,
                        'status' => $p->status,
                        'answered' => $this->answeredCount($activity, $p),
                        'answered_current' => $answer !== null,
                        'answer_correct_current' => $p->id !== $viewer?->id || $answer === null
                            ? null
                            : (bool) $answer->is_correct,
                        'last_round_answered' => $lastRoundAnswer !== null,
                        'last_round_answer_correct' => $p->id !== $viewer?->id || $lastRoundAnswer === null
                            ? null
                            : (bool) $lastRoundAnswer->is_correct,
                        'current_responder_id' => $responderId === null ? null : (int) $responderId,
                        'current_responder_name' => $responderId === null
                            ? null
                            : $responderNames->get((int) $responderId),
                    ];
                })
                ->all(),
        ];
    }

    /**
     * Pregunta de kahoot de la ronda actual serializada para mostrar
     * (sin opción correcta), o null si no hay ronda activa.
     */
    private function currentKahootQuestion(GameSession $session, ?int $questionId): ?array
    {
        if ($questionId === null) {
            return null;
        }

        $kahoot = $session->activity?->kahoot;

        if ($kahoot === null) {
            return null;
        }

        $question = Question::query()
            ->where('kahoot_id', $kahoot->id)
            ->with(['options' => fn ($query) => $query->orderBy('position')])
            ->find($questionId);

        return $question === null ? null : [
            'id' => $question->id,
            'question' => $question->question,
            'position' => $question->position,
            'time_limit' => $question->time_limit,
            'score' => $question->score,
            'options' => $question->options
                ->map(fn ($option) => [
                    'id' => $option->id,
                    'text' => $option->text,
                    'position' => $option->position,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * Ítem de la ronda actual serializado para mostrar (sin opción correcta),
     * o null si no hay ronda activa.
     */
    private function currentRoundItem(GameSession $session, ?int $itemId): ?array
    {
        if ($itemId === null) {
            return null;
        }

        $roulette = $session->activity?->roulette;

        if ($roulette === null) {
            return null;
        }

        $item = RouletteItem::query()
            ->where('roulette_id', $roulette->id)
            ->find($itemId);

        return $item === null ? null : [
            'id' => $item->id,
            'question' => $item->question,
            'points' => $item->points,
            'options' => collect(['a', 'b', 'c', 'd'])
                ->map(fn ($key) => [
                    'key' => $key,
                    'text' => $item->{'option_'.$key},
                ])
                ->values()
                ->all(),
        ];
    }

    private function answeredCount(Activity $activity, Participation $participation): int
    {
        return match ($activity->type) {
            'word_search' => $participation->wordsearchAnswers()->count(),
            'crossword' => $participation->crosswordAnswers()->count(),
            'matching' => $participation->matchingAnswers()->count(),
            'kahoot' => $participation->kahootAnswers()->count(),
            'roulette' => $participation->rouletteAnswers()->count(),
            default => 0,
        };
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = GameSession::CODE_PREFIX.random_int(100, 999);
        } while (GameSession::where('code', $code)->exists());

        return $code;
    }
}
