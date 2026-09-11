<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerMatchingRequest;
use App\Http\Requests\StoreMatchingRequest;
use App\Http\Requests\UpdateMatchingRequest;
use App\Models\Activity;
use App\Models\Matching;
use App\Models\Participation;
use App\Services\MatchingService;
use App\Services\ParticipationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MatchingController extends Controller
{
    public function __construct(
        protected MatchingService $matchingService,
        protected ParticipationService $participationService
    ) {}

    /**
     * Mostrar formulario para configurar un nuevo Matching.
     */
    public function configure(int $id): View
    {
        $activity = Activity::findOrFail($id);

        return view('teacher.matching.configure', [
            'activity' => $activity,
            'matching' => null,
            'editing' => false,
        ]);
    }

    /**
     * Guardar configuración del Matching.
     */
    public function store(
        StoreMatchingRequest $request,
        int $id
    ): RedirectResponse {
        $activity = Activity::findOrFail($id);

        $data = $request->validated();

        $this->matchingService->buildMatching(
            $activity,
            $data['items']
        );

        return redirect()
            ->route('teacher.matching.edit', $id)
            ->with(
                'status',
                'Actividad de unir conceptos guardada correctamente.'
            );
    }

    /**
     * Mostrar formulario para editar un Matching existente.
     */
    public function edit(int $id): View
    {
        $activity = Activity::findOrFail($id);

        $matching = $activity->matching;

        return view('teacher.matching.configure', [
            'activity' => $activity,
            'matching' => $matching,
            'editing' => (bool) $matching,
        ]);
    }

    /**
     * Actualizar configuración del Matching.
     */
    public function update(
        UpdateMatchingRequest $request,
        int $id
    ): RedirectResponse {
        $activity = Activity::findOrFail($id);

        $data = $request->validated();

        $this->matchingService->buildMatching(
            $activity,
            $data['items']
        );

        return redirect()
            ->route('teacher.matching.edit', $id)
            ->with(
                'status',
                'Actividad de unir conceptos actualizada correctamente.'
            );
    }

    /**
     * Mostrar el juego de Matching.
     *
     * La participación NO se crea aquí.
     * Debe haber sido creada previamente por:
     *
     * ParticipationController::start()
     *        ↓
     * ParticipationService::start()
     *
     * Aquí solamente recuperamos la participación activa.
     */
    public function play(int $id)
    {
        $activity = Activity::findOrFail($id);

        $matching = $activity->matching;

        abort_unless(
            $matching,
            404,
            'Esta actividad aún no tiene una actividad de unir conceptos.'
        );

        $participation = $this->participationService->getForPlay($activity);

        if ($participation->status === 'expired') {
            return redirect()->route(
                'student.participation.result',
                $activity->id
            );
        }

        $items = $matching->items()->get();

        return view('student.matching.play', [
            'matching' => $matching,
            'participation' => $participation,
            'items' => $items,
            'rightOptions' => $items->shuffle(),
            'correctIds' => $this->matchingService->correctItemIds(
                $matching,
                $participation
            ),
            'earnedPoints' => $this->matchingService->earnedScore(
                $matching,
                $participation
            ),
            'maxScore' => $this->matchingService->maxScore(
                $matching
            ),
        ]);
    }

    /**
     * Procesar una respuesta de Matching.
     */
    public function answer(
        AnswerMatchingRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $matching = Matching::findOrFail(
            (int) $data['matching_id']
        );

        $participation = $this->resolveParticipation(
            $matching->activity
        );

        return response()->json(
            $this->matchingService->checkAnswer(
                $matching,
                $participation,
                (int) $data['matching_item_id'],
                (string) $data['response']
            )
        );
    }

    /**
     * Obtener la participación activa del estudiante/equipo.
     *
     * IMPORTANTE:
     * No crea una participación.
     *
     * ParticipationController::start() es el único responsable
     * de iniciar una nueva participación.
     */
    protected function resolveParticipation(
        Activity $activity
    ): Participation {
        return $this->participationService->getActive($activity);
    }
}
