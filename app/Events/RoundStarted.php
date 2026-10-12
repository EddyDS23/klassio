<?php

namespace App\Events;

use App\Models\GameSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoundStarted implements ShouldBroadcast
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
            'roulette_item_id' => $this->session->state['roulette_item_id'] ?? null,
            'question_id' => $this->session->state['kahoot_question_id'] ?? null,
            'round_seconds' => $this->session->state['round_seconds'] ?? null,
            'payload' => $this->payload,
        ];
    }
}
