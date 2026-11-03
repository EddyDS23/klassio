<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\GameSession;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Historial de juego para las actividades publicadas: 1 sesión terminada por
 * actividad (con participaciones y puntajes deterministas) + 1 sesión
 * abierta en espera por clase activa. Así rankings y reportes tienen
 * información de a cuerdo con un uso real.
 */
class DemoGameHistorySeeder extends Seeder
{
    public function run(): void
    {
        SchoolClass::with('activities')->get()->each(function (SchoolClass $class): void {
            $enrollments = Enrollment::query()
                ->where('class_id', $class->id)
                ->where('status', 'active')
                ->pluck('student_id');

            if ($enrollments->isEmpty()) {
                return;
            }

            $finished = $class->activities->filter(fn (Activity $activity): bool => $activity->status === 'published')->values();

            foreach ($finished as $activityIndex => $activity) {
                // Solo 2 historiales por clase publicada (semestre en curso);
                // en clases antiguas, 1 (simula que solo se jugó una vez).
                $sessionsPerActivity = $class->status === 'active' ? 2 : 1;

                if ($activityIndex >= $sessionsPerActivity) {
                    continue;
                }

                $this->seedFinishedSession($class, $activity, $enrollments, $activityIndex);
            }

            // Sesión en espera para jugar en vivo con la actividad principal kahoot.
            $live = $class->activities
                ->first(fn (Activity $activity): bool => $activity->status === 'published' && $activity->type === 'kahoot' && $activity->mode === 'individual');

            if ($live !== null && $class->status === 'active') {
                GameSession::firstOrCreate(
                    [
                        'activity_id' => $live->id,
                        'code' => strtoupper(substr(md5('live'.$live->id), 0, 6)),
                    ],
                    [
                        'status' => GameSession::STATUS_WAITING,
                        'max_players' => max(30, $enrollments->count()),
                        'created_by' => $class->teacher_id,
                    ]
                );
            }
        });
    }

    /**
     * @param  Collection<int, int>  $studentIds
     */
    private function seedFinishedSession(SchoolClass $class, Activity $activity, $studentIds, int $offset): void
    {
        $started = now()->subDays(5 + $offset * 9)->startOfDay()->addHours(9);

        $session = GameSession::firstOrCreate(
            [
                'activity_id' => $activity->id,
                'code' => strtoupper(substr(md5('fin'.$activity->id.'#'.$offset), 0, 6)),
            ],
            [
                'status' => GameSession::STATUS_FINISHED,
                'max_players' => max(30, $studentIds->count()),
                'created_by' => $class->teacher_id,
                'started_at' => $started,
                'finished_at' => $started->copy()->addMinutes(45),
                'result_type' => GameSession::RESULT_WINNER,
            ]
        );

        if ($session->participations()->exists()) {
            return;
        }

        $teams = [];

        if ($activity->mode === 'team') {
            $members = TeamMember::query()
                ->whereIn('student_id', $studentIds->all())
                ->whereHas('team', fn ($query) => $query->where('activity_id', $activity->id))
                ->with('team')
                ->get()
                ->keyBy('student_id');
        }

        $top = null;

        foreach ($studentIds as $index => $studentId) {
            // 1 de cada 6 abandona a mitad de la partida.
            $abandoned = $index % 6 === 5;

            $score = $abandoned
                ? random_int(0, 20)
                : 55 + (($index * 37 + $offset * 11) % 46);

            $participation = Participation::create([
                'game_session_id' => $session->id,
                'activity_id' => $activity->id,
                'student_id' => $studentId,
                'team_id' => $activity->mode === 'team' ? ($members[$studentId]->team_id ?? null) : null,
                'attempt' => 1,
                'status' => $abandoned ? 'abandoned' : 'completed',
                'score' => $score,
                'completed_at' => $abandoned ? null : $started->copy()->addMinutes(10 + ($index % 20)),
                'elapsed_seconds' => ($index * 13 + 90) % max($activity->time_limit ?? 300, 120),
            ]);

            if (! $abandoned && ($top === null || $participation->score > $top->score)) {
                $top = $participation;
            }
        }

        if ($top !== null) {
            $session->update([
                'winner_participation_id' => $top->id,
                'current_turn_participation_id' => null,
            ]);
        }
    }
}
