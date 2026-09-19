<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminActivityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Activity::query()
            ->with(['teacher', 'schoolClass'])
            ->withCount('participations');

        if ($request->has('search') && trim((string) $request->input('search')) !== '') {
            $query->where('title', 'like', '%' . trim((string) $request->input('search')) . '%');
        }

        foreach (['type', 'mode', 'status'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->integer('teacher_id'));
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->integer('class_id'));
        }

        $activities = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);
        $classes = SchoolClass::orderBy('name')->get(['id', 'name']);

        return view('admin.activities.index', compact('activities', 'teachers', 'classes'));
    }

    public function show(int $id): View
    {
        $activity = Activity::with([
            'schoolClass',
            'teacher',
            'teams.members.student',
            'participations.student',
            'participations.team.members.student',
        ])->findOrFail($id);

        return view('admin.activities.show', compact('activity'));
    }
}