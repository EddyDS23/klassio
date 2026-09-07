<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Participation;
use App\Services\ParticipationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use RuntimeException;

class ParticipationController extends Controller
{
    public function __construct(private ParticipationService $participationService) {}

    // -------------------------------------------------------------------------
    // 6.2 — Iniciar
    // -------------------------------------------------------------------------

    public function start(int $id): RedirectResponse
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('start', [Participation::class, $activity]);

        try {
            $this->participationService->start($activity);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Redirigir al juego correspondiente según el tipo de actividad
        // Solo redirige a juegos implementados; los demás vuelven a la actividad
        $playRoute = match ($activity->type) {
            'crossword'   => 'student.crossword.play',
            'kahoot'      => 'student.kahoot.play',
            'word_search' => 'student.wordsearch.play',
            'matching'    => 'student.matching.play',
            default       => null,
        };

        if ($playRoute === null || ! \Illuminate\Support\Facades\Route::has($playRoute)) {
            return redirect()
                ->route('student.activities.show', $activity->id)
                ->with('error', 'Este tipo de actividad aún no tiene un juego disponible.');
        }

        return redirect()->route($playRoute, $activity->id);
    }

    // -------------------------------------------------------------------------
    // 6.5 — Finalizar
    // -------------------------------------------------------------------------

    public function finish(int $id): RedirectResponse
    {
        $activity = Activity::findOrFail($id);

        try {
            $participation = $this->participationService->getActive($activity);
        } catch (\Exception $e) {
            return redirect()
                ->route('student.activities.show', $activity->id)
                ->with('error', $e->getMessage());
        }

        Gate::authorize('finish', $participation);

        try {
            $this->participationService->finish($participation);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('student.participation.result', $activity->id)
            ->with('success', 'Actividad finalizada correctamente.');
    }

    // -------------------------------------------------------------------------
    // 6.6 — Abandonar
    // -------------------------------------------------------------------------

    public function abandon(int $id): RedirectResponse
    {
        $activity = Activity::findOrFail($id);

        try {
            $participation = $this->participationService->getActive($activity);
        } catch (\Exception $e) {
            return redirect()
                ->route('student.activities.show', $activity->id)
                ->with('error', $e->getMessage());
        }

        Gate::authorize('abandon', $participation);

        try {
            $this->participationService->abandon($participation);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('student.activities.show', $activity->id)
            ->with('success', 'Actividad abandonada.');
    }

    // -------------------------------------------------------------------------
    // 6.7 — Resultado
    // -------------------------------------------------------------------------

    public function result(int $id): View
    {
        $activity = Activity::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // El maestro propietario puede ver el resultado de cualquier estudiante,
        // pero necesita un participation_id específico (futuro: via query param).
        // Por ahora resolvemos el intento según el rol.
        if ($user->role === 'teacher' && $activity->teacher_id === $user->id) {
            // El maestro ve la lista de participaciones desde la vista de actividad,
            // aquí solo soportamos acceso por student autenticado.
            // Esto se ampliará en Fase 7.
            abort(403, 'Acceso no disponible desde esta ruta para maestros.');
        }

        // Estudiante: buscar su último intento no activo
        $participation = Participation::where('activity_id', $activity->id)
            ->where('student_id', $user->id)
            ->whereIn('status', ['completed', 'abandoned', 'expired'])
            ->latest()
            ->firstOrFail();

        Gate::authorize('result', $participation);

        $result = $this->participationService->getResult($participation);

        return view('student.participation.result', $result);
    }
}