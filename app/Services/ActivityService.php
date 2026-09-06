<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ActivityService
{
    /** * Crear una actividad y su configuración inicial. */
    public function create(SchoolClass $class, array $data): Activity
    {
        return DB::transaction(function () use ($class, $data) {
            $activity = Activity::create(['class_id' => $class->id, 'teacher_id' => $class->teacher_id, 'title' => $data['title'], 'description' => $data['description'] ?? null, 'type' => $data['type'], 'mode' => $data['mode'], 'max_score' => $data['max_score'], 'time_limit' => $data['time_limit'], 'due_at' => $data['due_at'] ?? null, 'status' => 'draft',]);
            $this->createConfiguration($activity);
            return $activity;
        });
    }

    /** * Crear la configuración correspondiente al tipo de actividad. */
    private function createConfiguration(Activity $activity): void
    {
        match ($activity->type) {
            'word_search' => $activity->wordsearch()->create(['rows' => 0, 'columns' => 0, 'grid' => [],]),
            'crossword' => $activity->crossword()->create(['rows' => 0, 'columns' => 0, 'grid' => [],]),
            'matching' => $activity->matching()->create(),
            'kahoot' => $activity->kahoot()->create(),
            default => throw new RuntimeException("Tipo de actividad no soportado: {$activity->type}"),
        };
    }

    /** * Publicar una actividad. */
    public function publish(Activity $activity): void
    {
        if ($activity->status !== 'draft') {
            throw new RuntimeException('Solo se pueden publicar actividades en estado draft.');
        }
        if (!$this->validateContent($activity)) {
            throw new RuntimeException('La actividad no tiene el contenido mínimo requerido para ser publicada.');
        }
        $activity->update(['status' => 'published',]);
    }

    /** * Validar el contenido mínimo según el tipo de actividad. */
    private function validateContent(Activity $activity): bool
    {
        return match ($activity->type) {
            'word_search' => $this->validateWordsearch($activity),
            'crossword' => $this->validateCrossword($activity),
            'matching' => $this->validateMatching($activity),
            'kahoot' => $this->validateKahoot($activity),
            default => false,
        };
    }

    /** * Validar Word Search. */
    private function validateWordsearch(Activity $activity): bool
    {
        $wordsearch = $activity->wordsearch;
        if (!$wordsearch) {
            return false;
        }
        return $wordsearch->rows > 0 && $wordsearch->columns > 0 && !empty($wordsearch->grid) && $wordsearch->words()->exists();
    }

    /** * Validar Crossword. */
    private function validateCrossword(Activity $activity): bool
    {
        $crossword = $activity->crossword;
        if (!$crossword) {
            return false;
        }
        return $crossword->rows > 0 && $crossword->columns > 0 && !empty($crossword->grid) && $crossword->words()->exists();
    }

    /** * Validar Matching. */
    private function validateMatching(Activity $activity): bool
    {
        $matching = $activity->matching;
        if (!$matching) {
            return false;
        }
        return $matching->items()->exists();
    }

    /** * Validar Kahoot. */
    private function validateKahoot(Activity $activity): bool
    {
        $kahoot = $activity->kahoot;

        if (!$kahoot) {
            return false;
        }

        $questions = $kahoot->questions;

        if ($questions->isEmpty()) {
            return false;
        }

        foreach ($questions as $question) {

            if ($question->options->count() < 2 || $question->options->count() > 4) {
                return false;
            }

            if ($question->options->where('is_correct', true)->count() !== 1) {
                return false;
            }
        }

        return true;
    }

    /** * Cerrar una actividad. */
    public function close(Activity $activity): void
    {
        if ($activity->status !== 'published') {
            throw new RuntimeException('Solo se pueden cerrar actividades publicadas.');
        }
        $activity->update(['status' => 'closed',]);
    }
}
