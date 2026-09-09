<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Services\WordSearchService;
use Illuminate\Database\Seeder;

class WordsearchSeeder extends Seeder
{
    public function run(WordSearchService $service): void
    {
        $activity = Activity::query()->where('type', 'word_search')->firstOrFail();

        $service->buildWordsearch(
            $activity,
            rows: 10,
            columns: 10,
            words: [
                ['word' => 'HTTP', 'score' => 10],
                ['word' => 'LARAVEL', 'score' => 10],
                ['word' => 'API', 'score' => 10],
                ['word' => 'PHP', 'score' => 10],
                ['word' => 'MYSQL', 'score' => 10],
            ]
        );

        $this->command?->info("Sopa de letras generada para la actividad #{$activity->id}.");
    }
}