<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[Fillable(['activity_id', 'code', 'status', 'max_players', 'created_by', 'started_at', 'finished_at', 'current_turn_participation_id', 'result_type', 'winner_participation_id', 'state'])]
#[Hidden(['created_at', 'updated_at'])]
class GameSession extends Model
{
    public const STATUS_WAITING = 'waiting';

    public const STATUS_STARTING = 'starting';

    public const STATUS_PLAYING = 'playing';

    public const STATUS_FINISHED = 'finished';

    public const STATUS_CANCELLED = 'cancelled';

    public const RESULT_WINNER = 'winner';

    public const RESULT_DRAW = 'draw';

    public const RESULT_COLLABORATIVE = 'collaborative_success';

    public const CODE_PREFIX = 'KLS';

    public $timestamps = true;

    #[Override]
    protected function casts()
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'state' => 'array',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participations(): HasMany
    {
        return $this->hasMany(Participation::class, 'game_session_id');
    }

    public function currentTurnParticipation(): BelongsTo
    {
        return $this->belongsTo(Participation::class, 'current_turn_participation_id');
    }

    public function winnerParticipation(): BelongsTo
    {
        return $this->belongsTo(Participation::class, 'winner_participation_id');
    }
}
