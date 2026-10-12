<?php

namespace App\Events;

use App\Models\GameSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GameFinished implements ShouldBroadcast
{
    public function __construct(
        public GameSession $session
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('game-session.'.$this->session->id);
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->id,
            'status' => $this->session->status,
            'result_type' => $this->session->result_type,
            'winner_participation_id' => $this->session->winner_participation_id,
        ];
    }
}
