<?php

namespace App\Events;

use App\Models\GameSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TurnChanged implements ShouldBroadcast
{
    public function __construct(
        public GameSession $session,
        public array $payload = []
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('game-session.'.$this->session->id);
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->id,
            'current_turn_participation_id' => $this->session->current_turn_participation_id,
            'payload' => $this->payload,
        ];
    }
}
