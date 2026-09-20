<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminClassController extends Controller
{
    public function index(Request $request): View
    {
        $query = SchoolClass::query()
            ->with('teacher')
            ->withCount([
                'activities',
                'enrollments',
                'enrollments as active_students_count' => fn ($q) => $q->where('status', 'active'),
            ]);

        if ($request->has('search') && trim((string) $request->input('search')) !== '') {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->integer('teacher_id'));
        }

        $classes = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);

        return view('admin.classes.index', compact('classes', 'teachers'));
    }

    public function show(int $id): View
    {
        $class = SchoolClass::with([
            'teacher',
            'enrollments.student',
            'activities',
        ])->findOrFail($id);

        return view('admin.classes.show', compact('class'));
    }
}