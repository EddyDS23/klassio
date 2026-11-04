<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClassRequest;
use App\Http\Requests\JoinClassRequest;
use App\Http\Requests\ModifyClassRequest;
use App\Models\SchoolClass;
use App\Services\ClassService;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClassController extends Controller
{
    /** Periodos por página en los listados agrupados. */
    private const PERIODS_PER_PAGE = 4;

    public function __construct(
        public ClassService $classService,
        public GradingService $gradingService
    ) {}

    public function index(Request $request): mixed
    {
        $period = $this->validatedPeriod($request);

        $groups = $this->classService->periodsForTeacher($request->user());

        return view('teacher.classes.index', [
            'periods' => $this->paginatePeriodGroups($groups, $period),
            'availablePeriods' => $groups->pluck('code')->all(),
            'selectedPeriod' => $period,
        ]);
    }

    /**
     * Acepta solo códigos "AAAA-Hn"; cualquier otro valor se ignora.
     */
    private function validatedPeriod(Request $request): ?string
    {
        $period = (string) $request->query('period');

        return $period !== '' && preg_match('/^\d{4}-H[12]$/', $period) === 1
            ? $period
            : null;
    }

    /**
     * Pagina los grupos de periodos: 4 acordeones por página, conservando el
     * resto de parámetros (ej. el filtro de periodo) en los enlaces.
     */
    private function paginatePeriodGroups(Collection $groups, ?string $period): LengthAwarePaginator
    {
        $groups = $period === null
            ? $groups
            : $groups->filter(fn (array $group) => $group['code'] === $period)->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $groups->forPage($page, self::PERIODS_PER_PAGE)->values();

        return (new LengthAwarePaginator(
            $items,
            $groups->count(),
            self::PERIODS_PER_PAGE,
            $page
        ))->withQueryString();
    }

    public function show(Request $request, int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('view', $class);

        return view('teacher.classes.show', [
            'class' => $class,
        ]);
    }

    public function grading(int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('view', $class);

        $grading = $this->gradingService->forClass($class);

        return view('teacher.classes.grading', [
            'class' => $class,
            'units' => $grading['units'],
            'studentRows' => $grading['students'],
        ]);
    }

    public function create(): mixed
    {
        return view('teacher.classes.create');
    }

    public function store(CreateClassRequest $request): mixed
    {
        Gate::authorize('create', SchoolClass::class);

        $this->classService->create(
            $request->safe()->except(['units']),
            $request->validatedUnits()
        );

        return redirect()
            ->route('teacher.classes.index')
            ->with('success', 'Clase creada correctamente');
    }

    public function edit(int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('update', $class);

        return view('teacher.classes.edit', [
            'class' => $class,
        ]);
    }

    public function update(ModifyClassRequest $request, int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('update', $class);

        $this->classService->update(
            $request->safe()->except(['units']),
            $id,
            $request->submittedUnits()
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
            'students' => $students,
        ]);
    }

    public function join(JoinClassRequest $request)
    {

        $result = $this->classService->join($request->validated('code'));

        return redirect()->route('student.class.show', $result['class']->id)
            ->with('success', $result['alreadyJoined']
                ? 'Ya pertenecías a esta clase.'
                : 'Te has unido a la clase correctamente.');
    }

    public function studentShow(int $id): View
    {
        $class = SchoolClass::findOrFail($id);

        Gate::authorize('view', $class);

        $activity = $class->activities()
            ->where('status', 'published')
            ->latest()
            ->first();

        return view('student.classes.show', compact('class', 'activity'));
    }

    public function studentIndex(Request $request): mixed
    {
        $period = $this->validatedPeriod($request);

        $groups = $this->classService->periodsForStudent($request->user());

        return view('student.classes.index', [
            'periods' => $this->paginatePeriodGroups($groups, $period),
            'availablePeriods' => $groups->pluck('code')->all(),
            'selectedPeriod' => $period,
        ]);
    }

    public function studentStudents(int $id): mixed
    {
        $class = $this->classService->get($id);

        Gate::authorize('viewClassmates', $class);

        $students = $this->classService->getClassmates($class);

        return view('student.classes.students', [
            'class' => $class,
            'students' => $students,
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
