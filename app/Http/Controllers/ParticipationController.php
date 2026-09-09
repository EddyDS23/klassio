<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Participation;
use App\Models\Team;
use App\Services\ParticipationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
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

        $playRoute = match ($activity->type) {
            'crossword'   => 'student.crossword.play',
            'kahoot'      => 'student.kahoot.play',
            'word_search' => 'student.wordsearch.play',
            'matching'    => 'student.matching.play',
            default       => null,
        };

        if ($playRoute === null || ! Route::has($playRoute)) {
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

        // Resolver la participación según el rol y el modo de la actividad
        if ($user->role === 'teacher') {
            // El maestro puede acceder si es propietario de la actividad.
            // Necesita que se le pase un participation_id por query string.
            // Esto se amplía en Fase 7 con ranking completo.
            $participationId = request()->query('participation_id');

            abort_if($participationId === null, 400, 'Se requiere participation_id.');

            $participation = Participation::where('id', $participationId)
                ->where('activity_id', $activity->id)
                ->firstOrFail();
        } elseif ($activity->mode === 'team') {
            // Estudiante en modo equipo: buscar por team_id
            $team = Team::where('activity_id', $activity->id)
                ->whereHas('members', fn($q) => $q->where('student_id', $user->id))
                ->first();

            abort_if($team === null, 404, 'No perteneces a ningún equipo en esta actividad.');

            $participation = Participation::where('activity_id', $activity->id)
                ->where('team_id', $team->id)
                ->whereIn('status', ['completed', 'abandoned', 'expired'])
                ->latest()
                ->firstOrFail();
        } else {
            // Estudiante individual
            $participation = Participation::where('activity_id', $activity->id)
                ->where('student_id', $user->id)
                ->whereIn('status', ['completed', 'abandoned', 'expired'])
                ->latest()
                ->firstOrFail();
        }

        Gate::authorize('result', $participation);

        $result = $this->participationService->getResult($participation);

        return view('student.participation.result', $result);
    }
}