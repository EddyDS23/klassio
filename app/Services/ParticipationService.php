<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Enrollment;
use App\Models\Participation;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class ParticipationService
{
    // -------------------------------------------------------------------------
    // 6.2 — Iniciar participación
    // -------------------------------------------------------------------------

    /**
     * Inicia un nuevo intento para el estudiante en la actividad dada.
     * Soporta modo individual y modo equipo.
     * Retorna la Participation creada.
     */
    public function start(Activity $activity): Participation
    {
        /** @var User $student */
        $student = Auth::user();

        // Actividad debe estar publicada
        if ($activity->status !== 'published') {
            throw new RuntimeException('La actividad no está disponible.');
        }

        // Verificar due_at
        if ($activity->due_at !== null && now()->isAfter($activity->due_at)) {
            throw new RuntimeException('La fecha límite de esta actividad ya pasó.');
        }

        // Verificar inscripción activa en la clase
        $enrolled = Enrollment::where('class_id', $activity->class_id)
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->exists();

        if (! $enrolled) {
            throw new RuntimeException('No estás inscrito en la clase de esta actividad.');
        }

        // Bloquear si ya tiene una participación activa (sin importar time_limit)
        $hasActive = Participation::where('activity_id', $activity->id)
            ->where('student_id', $student->id)
            ->where('status', 'started')
            ->exists();

        if ($hasActive) {
            throw new RuntimeException('Ya tienes un intento activo en esta actividad.');
        }

        // Expirar participaciones anteriores que quedaron en started y ya vencieron
        $this->expireStale($activity, $student->id);

        return $activity->mode === 'team'
            ? $this->startTeam($activity, $student)
            : $this->startIndividual($activity, $student);
    }

    /**
     * Crea participación individual.
     */
    private function startIndividual(Activity $activity, User $student): Participation
    {
        $attempt = Participation::where('activity_id', $activity->id)
            ->where('student_id', $student->id)
            ->max('attempt') ?? 0;

        // started_at lo pone la BD por DEFAULT current_timestamp()
        return Participation::create([
            'activity_id' => $activity->id,
            'student_id'  => $student->id,
            'team_id'     => null,
            'attempt'     => $attempt + 1,
            'status'      => 'started',
            'score'       => 0,
        ]);
    }

    /**
     * Crea participación por equipo.
     * Verifica que el estudiante pertenezca a un equipo de la actividad.
     */
    private function startTeam(Activity $activity, User $student): Participation
    {
        // Buscar el equipo del estudiante para esta actividad
        $team = Team::where('activity_id', $activity->id)
            ->whereHas('members', fn($q) => $q->where('student_id', $student->id))
            ->first();

        if ($team === null) {
            throw new RuntimeException('No perteneces a ningún equipo en esta actividad.');
        }

        // Verificar que el equipo no tenga ya una participación activa
        $teamHasActive = Participation::where('activity_id', $activity->id)
            ->where('team_id', $team->id)
            ->where('status', 'started')
            ->exists();

        if ($teamHasActive) {
            throw new RuntimeException('Tu equipo ya tiene un intento activo en esta actividad.');
        }

        $attempt = Participation::where('activity_id', $activity->id)
            ->where('team_id', $team->id)
            ->max('attempt') ?? 0;

        return Participation::create([
            'activity_id' => $activity->id,
            'student_id'  => null,
            'team_id'     => $team->id,
            'attempt'     => $attempt + 1,
            'status'      => 'started',
            'score'       => 0,
        ]);
    }

    // -------------------------------------------------------------------------
    // 6.3 — Obtener participación activa verificando propietario
    // -------------------------------------------------------------------------

    /**
     * Retorna la participación activa (started) del estudiante para la actividad.
     * Funciona para modo individual y equipo.
     * Aborta con 404 si no existe, 410 si expiró.
     */
    public function getActive(Activity $activity): Participation
    {
        /** @var User $student */
        $student = Auth::user();

        $query = Participation::where('activity_id', $activity->id)
            ->where('status', 'started')
            ->latest();

        if ($activity->mode === 'team') {
            // Buscar el equipo del estudiante para esta actividad
            $team = Team::where('activity_id', $activity->id)
                ->whereHas('members', fn($q) => $q->where('student_id', $student->id))
                ->first();

            if ($team === null) {
                abort(404, 'No tienes participación activa en esta actividad.');
            }

            $query->where('team_id', $team->id);
        } else {
            $query->where('student_id', $student->id);
        }

        $participation = $query->first();

        if ($participation === null) {
            abort(404, 'No tienes una participación activa en esta actividad.');
        }

        // Verificar expiración por time_limit
        if ($this->hasExpired($participation, $activity)) {
            $this->expire($participation);
            abort(410, 'Tu participación ha expirado.');
        }

        return $participation;
    }

    // -------------------------------------------------------------------------
    // 6.5 — Finalizar participación
    // -------------------------------------------------------------------------

    /**
     * Marca la participación como completada y calcula elapsed_seconds.
     */
    public function finish(Participation $participation): Participation
    {
        if ($participation->status !== 'started') {
            throw new RuntimeException('Solo se puede finalizar una participación activa.');
        }

        $elapsed = (int) $participation->started_at->diffInSeconds(now());

        $participation->update([
            'status'          => 'completed',
            'completed_at'    => now(),
            'elapsed_seconds' => $elapsed,
        ]);

        return $participation->fresh();
    }

    // -------------------------------------------------------------------------
    // 6.6 — Abandonar y expirar
    // -------------------------------------------------------------------------

    /**
     * Marca la participación como abandonada.
     */
    public function abandon(Participation $participation): void
    {
        if ($participation->status !== 'started') {
            throw new RuntimeException('Solo se puede abandonar una participación activa.');
        }

        $participation->update(['status' => 'abandoned']);
    }

    /**
     * Marca la participación como expirada.
     */
    public function expire(Participation $participation): void
    {
        $participation->update(['status' => 'expired']);
    }

    /**
     * Determina si una participación superó el time_limit de la actividad.
     */
    public function hasExpired(Participation $participation, Activity $activity): bool
    {
        if ($activity->time_limit === null || $activity->time_limit <= 0) {
            return false;
        }

        $elapsed = (int) $participation->started_at->diffInSeconds(now());

        return $elapsed > $activity->time_limit;
    }

    /**
     * Expira participaciones que quedaron en 'started' y ya superaron el time_limit.
     * Se llama al iniciar una nueva participación para limpiar estados colgados.
     */
    private function expireStale(Activity $activity, int $studentId): void
    {
        $stale = Participation::where('activity_id', $activity->id)
            ->where('student_id', $studentId)
            ->where('status', 'started')
            ->get();

        foreach ($stale as $p) {
            if ($this->hasExpired($p, $activity)) {
                $p->update(['status' => 'expired']);
            }
        }
    }

    // -------------------------------------------------------------------------
    // 6.7 — Resultado individual
    // -------------------------------------------------------------------------

    /**
     * Retorna los datos del resultado de una participación.
     * Incluye respuestas según el tipo de actividad.
     */
    public function getResult(Participation $participation): array
    {
        $activity = $participation->activity;

        $elapsed = $participation->elapsed_seconds !== null
            ? $this->formatElapsed($participation->elapsed_seconds)
            : null;

        $answers = match ($activity->type) {
            'crossword'   => $this->crosswordAnswers($participation),
            'kahoot'      => $this->kahootAnswers($participation),
            'word_search' => $this->wordsearchAnswers($participation),
            'matching'    => $this->matchingAnswers($participation),
            default       => [],
        };

        return [
            'activity'      => $activity,
            'participation' => $participation,
            'elapsed'       => $elapsed,
            'answers'       => $answers,
        ];
    }

    private function formatElapsed(int $seconds): string
    {
        $m = intdiv($seconds, 60);
        $s = $seconds % 60;
        return sprintf('%02d:%02d', $m, $s);
    }

    private function crosswordAnswers(Participation $participation): array
    {
        return $participation->crosswordAnswers()
            ->with('crosswordWord')
            ->get()
            ->map(fn($a) => [
                'clue'       => $a->crosswordWord->clue,
                'response'   => $a->response,
                'correct'    => $a->crosswordWord->word,
                'is_correct' => $a->is_correct,
                'score'      => $a->score,
            ])
            ->toArray();
    }

    private function kahootAnswers(Participation $participation): array
    {
        return $participation->kahootAnswers()
            ->with(['question', 'option'])
            ->get()
            ->map(fn($a) => [
                'question'   => $a->question->question,
                'response'   => $a->option->text,
                'is_correct' => $a->is_correct,
                'score'      => $a->score,
            ])
            ->toArray();
    }

    private function wordsearchAnswers(Participation $participation): array
    {
        return $participation->searchwordAnswers()
            ->with('word')
            ->get()
            ->map(fn($a) => [
                'word'  => $a->word->word,
                'score' => $a->score,
            ])
            ->toArray();
    }

    private function matchingAnswers(Participation $participation): array
    {
        return $participation->matchingAnswers()
            ->with('matchingItem')
            ->get()
            ->map(fn($a) => [
                'left'       => $a->matchingItem->left_text,
                'right'      => $a->matchingItem->right_text,
                'response'   => $a->response,
                'is_correct' => $a->is_correct,
                'score'      => $a->score,
            ])
            ->toArray();
    }
}