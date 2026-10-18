<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): mixed
    {
        return view('admin.dashboard');
    }

    public function teacher(): View
    {
        $activeGameSessions = GameSession::query()
            ->with('activity:id,title,type')
            ->withCount([
                'participations as joined_participations_count' => fn ($query) => $query
                    ->whereIn('status', ['waiting', 'started', 'completed']),
            ])
            ->whereHas('activity', fn ($query) => $query->where('teacher_id', Auth::id()))
            ->whereIn('status', [
                GameSession::STATUS_WAITING,
                GameSession::STATUS_STARTING,
                GameSession::STATUS_PLAYING,
            ])
            ->latest()
            ->get();

        return view('teacher.dashboard', compact('activeGameSessions'));
    }

    public function student(): mixed
    {
        return view('student.dashboard');
    }
}
