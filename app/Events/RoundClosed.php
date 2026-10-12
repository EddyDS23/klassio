<?php

namespace App\Events;

use App\Models\GameSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoundClosed implements ShouldBroadcast
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
            'roulette_item_id' => $this->payload['roulette_item_id'] ?? null,
            'question_id' => $this->payload['question_id'] ?? null,
            'finished' => $this->session->status === GameSession::STATUS_FINISHED,
        ];
    }
}
