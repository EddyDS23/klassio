<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('email', 'teacher@aulaplay.com')->firstOrFail();

        $class = SchoolClass::where('code', 'PROGWEB01')->firstOrFail();

        $activities = [
            [
                'title' => 'Sopa de letras - Programación Web',
                'type' => 'word_search',
            ],
            [
                'title' => 'Crucigrama - Programación Web',
                'type' => 'crossword',
            ],
            [
                'title' => 'Relaciona conceptos - Programación Web',
                'type' => 'matching',
            ],
            [
                'title' => 'Kahoot - Programación Web',
                'type' => 'kahoot',
            ],
        ];

        foreach ($activities as $activity) {
            Activity::updateOrCreate(
                [
                    'class_id' => $class->id,
                    'title' => $activity['title'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'description' => 'Actividad demo.',
                    'type' => $activity['type'],
                    'mode' => 'individual',
                    'max_score' => 100,
                    'time_limit' => 300,
                    'due_at' => null,
                    'status' => 'draft',
                ]
            );
        }
    }
}