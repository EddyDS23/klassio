<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Participation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminResultController extends Controller
{
    public function index(Request $request): View
    {
        $query = Participation::query()
            ->with(['activity.schoolClass', 'student', 'team.members.student']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->integer('activity_id'));
        }

        if ($request->filled('type')) {
            $query->whereHas('activity', fn ($q) => $q->where('type', $request->input('type')));
        }

        $results = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $activities = Activity::orderBy('title')->get(['id', 'title']);

        return view('admin.results.index', compact('results', 'activities'));
    }

    public function show(int $id): View
    {
        $result = Participation::with([
            'activity.schoolClass',
            'activity.teacher',
            'student',
            'team.members.student',
        ])->findOrFail($id);

        return view('admin.results.show', compact('result'));
    }
}