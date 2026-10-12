<?php

use App\Models\GameSession;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Autorización de los canales privados de broadcasting.
|
*/

Broadcast::channel('game-session.{id}', function ($user, $id) {
    $session = GameSession::find($id);

    if ($session === null) {
        return false;
    }

    if ($user->role === 'teacher') {
        return $session->activity->teacher_id === $user->id;
    }

    return $session->participations()
        ->where('student_id', $user->id)
        ->whereIn('status', ['waiting', 'started', 'completed'])
        ->exists();
});
