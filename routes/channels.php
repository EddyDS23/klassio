<?php

use App\Models\GameSession;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Gate;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Autorización de los canales privados de broadcasting.
|
*/

Broadcast::channel('game-session.{id}', function ($user, $id) {
    if (! $user instanceof User) {
        return false;
    }

    $session = GameSession::query()->with('activity')->find($id);

    if ($session === null || ! Gate::forUser($user)->allows('view', $session)) {
        return false;
    }

    return true;
});
