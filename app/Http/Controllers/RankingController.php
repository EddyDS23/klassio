<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Services\ParticipationService;
use App\Services\RankingService;
use App\Services\ReportService;
use Illuminate\Support\Facades\Gate;

class RankingController extends Controller
{
    public function __construct(
        protected RankingService $rankingService,
        protected ParticipationService $participationService,
        protected ReportService $reportService
    ) {}

    /**
     * Muestra el ranking de una actividad.
     */
    public function ranking(int $id)
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('view', $activity);

        $ranking = $this->rankingService->getRanking($activity);
        $topThree = $this->rankingService->getTopThree($activity);

        return view('teacher.rankings.ranking', [
            'activity' => $activity,
            'ranking' => $ranking,
            'topThree' => $topThree,
        ]);
    }

    /**
     * Muestra el reporte de participación.
     */
    public function report(int $id)
    {
        $activity = Activity::findOrFail($id);

        Gate::authorize('view', $activity);

        $summary = $this->reportService
            ->getActivitySummary($activity);

        return view('teacher.rankings.report', [
            'activity' => $activity,
            'summary' => $summary,
        ]);
    }
}
