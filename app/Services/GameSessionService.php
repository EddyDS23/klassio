<?php

namespace App\Services;

use App\Events\GameFinished;
use App\Events\GameStarted;
use App\Events\PlayerJoined;
use App\Events\PlayerLeft;
use App\Events\ScoreUpdated;
use App\Events\TurnChanged;
use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\GameSession;
use App\Models\Participation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

class GameSessionService
{
    /**
     * Minimo de jugadores para iniciar una partida.
     */
    public const MIN_PLAYERS = 2;

    public const MAX_PLAYERS = 16;

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

    public function findByCode(string $code): ?GameSession
    {
        return GameSession::where('code', mb_strtoupper(trim($code)))
            ->first();
    }

    public function isParticipant(GameSession $session, User $user): bool
    {
        return $session->participations()
            ->where('student_id', $user->id)
            ->whereIn('status', ['waiting', 'started'])
            ->exists();
    }

    public function participationOf(
        GameSession $session,
        User $user
    ): ?Participation {
        return $session->participations()
            ->where('student_id', $user->id)
            ->orderBy('id')
            ->first();
    }

    /**
     * Un jugador entra a la sala.
     *
     * @return Participation Participación en estado waiting.
     */
    public function join(GameSession $session, User $student): Participation
    {
        $activity = $session->activity;

        if ($activity->status !== 'published') {
            throw new RuntimeException('La actividad no está disponible.');
        }

        if ($activity->due_at !== null && now()->isAfter($activity->due_at)) {
            throw new RuntimeException('La fecha límite de esta actividad ya pasó.');
        }

        if (! in_array($session->status, [GameSession::STATUS_WAITING, GameSession::STATUS_STARTING], true)) {
            throw new RuntimeException('Esta sala ya no acepta jugadores.');
        }

        if ($this->isParticipant($session, $student)) {
            throw new RuntimeException('Ya estás en esta sala.');
        }

        $count = $session->participations()
            ->whereIn('status', ['waiting', 'started'])
            ->count();

        if ($count >= $session->max_players) {
            throw new RuntimeException('La sala alcanzó su máximo de jugadores.');
        }

        $enrolled = Enrollment::where('class_id', $activity->class_id)
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->exists();

        if (! $enrolled) {
            throw new RuntimeException('No estás inscrito en la clase de esta actividad.');
        }

        $participation = Participation::create([
            'game_session_id' => $session->id,
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'team_id' => null,
            'attempt' => 1,
            'status' => 'waiting',
            'score' => 0,
        ]);

        event(new PlayerJoined($session, [
            'participation_id' => $participation->id,
            'name' => $student->name,
        ]));

        return $participation;
    }

    /**
     * Un jugador abandona la sala o la partida.
     */
    public function leave(GameSession $session, User $user): GameSession
    {
        $participation = $this->participationOf($session, $user);

        if ($participation === null) {
            throw new RuntimeException('No perteneces a esta sala.');
        }

        if (in_array($session->status, [GameSession::STATUS_FINISHED, GameSession::STATUS_CANCELLED], true)) {
            throw new RuntimeException('Esta partida ya finalizó.');
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

        event(new PlayerLeft($session->fresh(), [
            'participation_id' => $participation->id,
            'name' => $user->name,
        ]));

        return $session->fresh();
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
        if (! $this->canStart($session)) {
            throw new RuntimeException('Se necesitan al menos 2 jugadores para iniciar.');
        }

        $session->update([
            'status' => GameSession::STATUS_PLAYING,
            'started_at' => now(),
        ]);

        $startedAt = now();

        foreach ($this->players($session) as $participation) {
            $participation->status = 'started';
            $participation->started_at = $startedAt;
            $participation->save();
        }

        $first = $session->participations()
            ->where('status', 'started')
            ->orderBy('id')
            ->first();

        $session->update([
            'current_turn_participation_id' => $first?->id,
        ]);

        event(new GameStarted($session->fresh()));

        return $session->fresh();
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

        event(new TurnChanged($session->fresh(), [
            'participation_id' => $next->id,
            'name' => $next->student->name,
        ]));

        return $next->fresh();
    }

    /**
     * Finaliza la partida y determina resultado general e individual.
     */
    public function finish(GameSession $session): GameSession
    {
        if ($session->status === GameSession::STATUS_FINISHED) {
            return $session->fresh();
        }

        $active = $session->participations()
            ->where('status', 'started')
            ->get();

        $winner = null;
        $resultType = GameSession::RESULT_COLLABORATIVE;

        if ($active->isNotEmpty()) {
            $max = $active->max('score');

            $top = $active->filter(
                fn (Participation $p) => $p->score === $max
            );

            $resultType = $top->count() >= 2
                ? GameSession::RESULT_DRAW
                : GameSession::RESULT_WINNER;

            if ($resultType === GameSession::RESULT_WINNER) {
                $winner = $top->first();
            }
        }

        $finishedAt = now();

        foreach ($session->participations as $participation) {
            if ($participation->status === 'started') {
                $participation->status = 'completed';
                $participation->completed_at = $finishedAt;
                $participation->elapsed_seconds = $this->elapsedSeconds($session);
                $participation->save();
            } elseif ($participation->status === 'waiting') {
                $participation->status = 'abandoned';
                $participation->save();
            }
        }

        $session->update([
            'status' => GameSession::STATUS_FINISHED,
            'finished_at' => $finishedAt,
            'result_type' => $resultType,
            'winner_participation_id' => $winner?->id,
        ]);

        event(new GameFinished($session->fresh()));

        return $session->fresh();
    }

    /**
     * Notifica un cambio de puntuación a la sala.
     */
    public function notifyScore(GameSession $session, Participation $participation): void
    {
        event(new ScoreUpdated($session, [
            'participation_id' => $participation->id,
            'name' => $participation->student->name,
            'score' => $participation->score,
        ]));
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
     * Estado compartido de la partida para el frontend.
     */
    public function getState(GameSession $session): array
    {
        $activity = $session->activity;

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
            'players' => $session->participations()
                ->with('student')
                ->whereIn('status', ['waiting', 'started', 'completed'])
                ->orderBy('id')
                ->get()
                ->map(fn (Participation $p) => [
                    'participation_id' => $p->id,
                    'name' => $p->student?->name,
                    'score' => $p->score,
                    'status' => $p->status,
                    'answered' => $this->answeredCount($activity, $p),
                    'connected' => true,
                ])
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
