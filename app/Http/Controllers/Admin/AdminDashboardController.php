<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'activeUsers' => User::where('status', 'active')->count(),
            'suspendedUsers' => User::where('status', 'suspended')->count(),
            'classes' => SchoolClass::count(),
            'activeClasses' => SchoolClass::where('status', 'active')->count(),
            'activities' => Activity::count(),
            'publishedActivities' => Activity::where('status', 'published')->count(),
            'participations' => Participation::count(),
            'completedParticipants' => Participation::where('status', 'completed')->count(),
        ];

        $activityTypes = ['word_search' => 'Sopa de letras', 'crossword' => 'Crucigrama', 'matching' => 'Conecta', 'kahoot' => 'Quiz'];
        $activityByType = Activity::selectRaw('type, COUNT(*) as total')->groupBy('type')->pluck('total', 'type');
        $activityTypeLabels = array_values($activityTypes);
        $activityTypeTotals = array_map(fn ($type) => $activityByType[$type] ?? 0, array_keys($activityTypes));

        $userStatuses = ['active' => 'Activos', 'inactive' => 'Inactivos', 'suspended' => 'Suspendidos'];
        $usersByStatus = User::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $userStatusLabels = array_values($userStatuses);
        $userStatusTotals = array_map(fn ($status) => $usersByStatus[$status] ?? 0, array_keys($userStatuses));

        return view('admin.dashboard', compact(
            'stats',
            'activityTypeLabels',
            'activityTypeTotals',
            'userStatusLabels',
            'userStatusTotals'
        ));
    }
}