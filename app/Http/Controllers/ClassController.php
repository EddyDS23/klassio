<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClassRequest;
use App\Http\Requests\JoinClassRequest;
use App\Http\Requests\ModifyClassRequest;
use App\Models\SchoolClass;
use App\Services\ClassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function __construct(public ClassService $classService) {}

    public function index(): mixed
    {
        $classes = $this->classService->getClassesTeacher();

        return view('teacher.classes.index', [
            'classes' => $classes
        ]);
    }

    public function show(Request $request, int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('view', $class);

        return view('teacher.classes.show', [
            'class' => $class
        ]);
    }

    public function create(): mixed
    {
        return view('teacher.classes.create');
    }

    public function store(CreateClassRequest $request): mixed
    {
        Gate::authorize('create', SchoolClass::class);

        $this->classService->create($request->validated());

        return redirect()
            ->route('teacher.classes.index')
            ->with('success', 'Clase creada correctamente');
    }

    public function edit(int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('update', $class);

        return view('teacher.classes.edit', [
            'class' => $class
        ]);
    }

    public function update(ModifyClassRequest $request, int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('update', $class);

        $this->classService->update(
            $request->validated(),
            $id
        );

        return redirect()
            ->route('teacher.classes.index')
            ->with('success', 'Clase actualizada correctamente');
    }

    public function archive(int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('archive', $class);

        $this->classService->archive($class);

        return redirect()
            ->route('teacher.classes.index')
            ->with('success', 'Clase archivada correctamente');
    }

    public function unarchive(int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('unarchive', $class);

        $this->classService->unarchive($class);

        return redirect()
            ->route('teacher.classes.index')
            ->with('success', 'Clase desarchivada correctamente');
    }

    public function regenerateCode(int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('regenerateCode', $class);

        $this->classService->regenerateCode($class);

        return redirect()
            ->route('teacher.classes.show', $id)
            ->with('success', 'Código regenerado correctamente');
    }

    public function students(int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('viewStudents', $class);

        $students = $this->classService->getStudents($class);

        return view('teacher.classes.students', [
            'class' => $class,
            'students' => $students
        ]);
    }


    public function join(JoinClassRequest $request)
    {

        $class = $this->classService->join($request->validated('code'));

        return redirect()->route('student.class.show', $class->id)
            ->with('success', 'Te has unido a la clase correctamente');
    }

    public function studentShow(int $id): View
    {
        $class = SchoolClass::findOrFail($id);

        $activity = $class->activities()
            ->where('status', 'published')
            ->latest()
            ->first();

        return view('student.classes.show', compact('class', 'activity'));
    }

    public function studentIndex(): mixed
    {
        $classes = $this->classService->getClassesStudent();

        return view('student.classes.index', [
            'classes' => $classes
        ]);
    }

    public function studentStudents(int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('viewClassmates', $class);

        $students = $this->classService->getClassmates($class);

        return view('student.classes.students', [
            'class' => $class,
            'students' => $students
        ]);
    }

    public function removeStudent(int $id, int $studentId): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('removeStudent', $class);

        $this->classService->removeStudent($class, $studentId);

        return redirect()
            ->route('teacher.classes.students', $id)
            ->with('success', 'Alumno removido de la clase correctamente');
    }
}
