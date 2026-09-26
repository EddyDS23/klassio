<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRouletteRequest;
use App\Http\Requests\StoreRouletteRequest;
use App\Http\Requests\UpdateRouletteRequest;
use App\Models\Activity;
use App\Models\Roulette;
use App\Models\RouletteAnswer;
use App\Services\ParticipationService;
use App\Services\RouletteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RouletteController extends Controller
{
    public function __construct(
        protected RouletteService $rouletteService,
        protected ParticipationService $participationService
    ) {}

    /**
     * Mostrar formulario para configurar una nueva Ruleta.
     */
    public function configure(int $id): View
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $roulette = $activity->roulette;

        return view('teacher.roulette.configure', [
            'activity' => $activity,
            'roulette' => $roulette,
            'editing' => (bool) $roulette,
        ]);
    }

    /**
     * Guardar configuración de la Ruleta.
     */
    public function store(
        StoreRouletteRequest $request,
        int $id
    ): RedirectResponse {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $this->rouletteService->buildRoulette(
            $activity,
            $request->validated()['items']
        );

        return redirect()
            ->route('teacher.roulette.edit', $id)
            ->with('success', 'Actividad de ruleta guardada correctamente.');
    }

    /**
     * Mostrar formulario para editar una Ruleta existente.
     */
    public function edit(int $id): View
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $roulette = $activity->roulette;

        return view('teacher.roulette.configure', [
            'activity' => $activity,
            'roulette' => $roulette,
            'editing' => (bool) $roulette,
        ]);
    }

    /**
     * Actualizar configuración de la Ruleta.
     */
    public function update(
        UpdateRouletteRequest $request,
        int $id
    ): RedirectResponse {
        $activity = Activity::findOrFail($id);

        Gate::authorize('update', $activity);

        $this->rouletteService->buildRoulette(
            $activity,
            $request->validated()['items']
        );

        return redirect()
            ->route('teacher.roulette.edit', $id)
            ->with('success', 'Actividad de ruleta actualizada correctamente.');
    }

    /**
     * Mostrar el juego de la Ruleta.
     *
     * La participación NO se crea aquí; debe haber sido creada
     * previamente por ParticipationController::start().
     */
    public function play(int $id)
    {
        $activity = Activity::findOrFail($id);

        $roulette = $activity->roulette;

        abort_unless(
            $roulette,
            404,
            'Esta actividad aún no tiene una ruleta configurada.'
        );

        $participation = $this->participationService->getForPlay($activity);

        if ($participation->status === 'expired') {
            return redirect()->route(
                'student.participation.result',
                $activity->id
            );
        }

        $remainingSeconds = $this->participationService->remainingSeconds(
            $participation,
            $activity
        );

        $totalItems = $roulette->items()->count();

        $answeredIds = RouletteAnswer::where(
            'participation_id',
            $participation->id
        )->pluck('roulette_item_id')->toArray();

        return view('student.roulette.play', [
            'activity' => $activity,
            'roulette' => $roulette,
            'participation' => $participation,
            'answeredIds' => $answeredIds,
            'answeredCount' => count($answeredIds),
            'totalItems' => $totalItems,
            'earnedPoints' => $this->rouletteService->earnedScore(
                $roulette,
                $participation
            ),
            'maxScore' => $this->rouletteService->maxScore($roulette),
            'remainingSeconds' => $remainingSeconds,
        ]);
    }

    /**
     * Endpoint de giro: devuelve un casillero aleatorio pendiente.
     */
    public function spin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activity_id' => ['required', 'integer', 'exists:activities,id'],
        ]);

        $activity = Activity::findOrFail((int) $validated['activity_id']);

        $roulette = $activity->roulette;

        abort_if($roulette === null, 404);

        $participation = $this->participationService->getActive($activity);

        $answered = count(
            $this->rouletteService->answeredItemIds($roulette, $participation)
        );

        $total = $roulette->items()->count();

        $item = $this->rouletteService->getRandomItem(
            $roulette,
            $participation
        );

        if ($item === null) {
            return response()->json([
                'completed' => true,
                'item' => null,
                'answered' => $answered,
                'total' => $total,
            ]);
        }

        return response()->json([
            'completed' => false,
            'item' => $this->rouletteService->serializeItem($item),
            'answered' => $answered,
            'total' => $total,
        ]);
    }

    /**
     * Procesar una respuesta de la Ruleta.
     */
    public function answer(AnswerRouletteRequest $request): JsonResponse
    {
        $data = $request->validated();

        $roulette = Roulette::findOrFail((int) $data['roulette_id']);

        $participation = $this->participationService->getActive(
            $roulette->activity
        );

        return response()->json(
            $this->rouletteService->checkAnswer(
                $roulette,
                $participation,
                (int) $data['roulette_item_id'],
                (string) $data['response']
            )
        );
    }
}
