<?php

namespace App\Console\Commands;

use App\Models\GameSession;
use App\Services\GameSessionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('game-sessions:process-timeouts')]
#[Description('Cierra las rondas y partidas multijugador vencidas')]
class ProcessGameSessionTimeouts extends Command
{
    public function handle(GameSessionService $gameSessionService): int
    {
        GameSession::query()
            ->whereIn('status', [
                GameSession::STATUS_PLAYING,
                GameSession::STATUS_WAITING,
            ])
            ->select('id')
            ->chunkById(100, function ($sessions) use ($gameSessionService): void {
                foreach ($sessions as $session) {
                    $gameSessionService->expireStalePresences($session);
                    $gameSessionService->closeRoundIfDue($session);
                }
            });

        return self::SUCCESS;
    }
}
