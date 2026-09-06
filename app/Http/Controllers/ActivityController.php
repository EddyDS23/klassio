<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\SchoolClass;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __construct(private ActivityService $activityService) {}
    public function teacherIndex(int $id): View
    {
        $class = SchoolClass::findOrFail($id);

        Gate::authorize('create', [Activity::class, $class]);

        $query = $class->activities();

        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }

        if (request()->filled('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }

        $activities = $query->latest()->get();

        return view('teacher.activities.index', compact('class', 'activities'));
    }

    public function teacherCreate(int $id): View
    {
        $class = SchoolClass::findOrFail($id);
        Gate::authorize('create', [Activity::class, $class]);
        return view('teacher.activities.create', compact('class'));
    }

    public function teacherStore(StoreActivityRequest $request, int $id): RedirectResponse
    {
        $class = SchoolClass::findOrFail($id);

        Gate::authorize('create', [Activity::class, $class]);

        $activity = $this->activityService->create(
            $class,
            $request->validated()
        );

        $configureRoutes = [
            'crossword' => 'teacher.crossword.configure',
            'kahoot'    => 'teacher.kahoot.configure',
            'wordsearch' => 'teacher.wordsearch.configure',
            'matching'  => 'teacher.matching.configure',
        ];

        $route = $configureRoutes[$activity->type] ?? 'teacher.activities.edit';

        return redirect()
            ->route($route, $activity->id)
            ->with(
                'success',
                'Actividad creada correctamente.'
                    . (isset($configureRoutes[$activity->type])
                        ? ' Ahora configura el juego.'
                        : '')
            );
    }

    public function teacherShow(int $id): View
    {
        $activity = Activity::findOrFail($id);
        Gate::authorize('view', $activity);
        return view('teacher.activities.show', compact('activity'));
    }

    public function teacherEdit(int $id): View
    {
        $activity = Activity::findOrFail($id);
        Gate::authorize('update', $activity);
        return view('teacher.activities.edit', compact('activity'));
    }

    public function teacherUpdate(UpdateActivityRequest $request, int $id): RedirectResponse
    {
        $activity = Activity::findOrFail($id);
        Gate::authorize('update', $activity);
        $activity->update($request->validated());
        return redirect()->route('teacher.activities.edit', $activity->id)->with('success', 'Actividad actualizada correctamente.');
    }

    public function publish(int $id): RedirectResponse
    {
        $activity = Activity::findOrFail($id);
        Gate::authorize('publish', $activity);
        $this->activityService->publish($activity);
        return redirect()->back()->with('success', 'Actividad publicada correctamente.');
    }

    public function close(int $id): RedirectResponse
    {
        $activity = Activity::findOrFail($id);
        Gate::authorize('close', $activity);
        $this->activityService->close($activity);
        return redirect()->back()->with('success', 'Actividad cerrada correctamente.');
    }

    public function studentIndex(int $id): View
    {
        $class = SchoolClass::findOrFail($id);

        $query = $class->activities()
            ->where('status', 'published');

        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }

        if (request()->filled('search')) {
            $query->where('title', 'like', '%' . request('search') . '%');
        }

        $activities = $query->latest()->get();

        return view('student.activities.index', compact('class', 'activities'));
    }

    public function studentShow(int $id): View
    {
        $activity = Activity::findOrFail($id);
        Gate::authorize('view', $activity);
        return view('student.activities.show', compact('activity'));
    }
}
