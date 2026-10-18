<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Crossword;
use App\Models\CrosswordWord;
use App\Models\Enrollment;
use App\Models\Kahoot;
use App\Models\Matching;
use App\Models\MatchingItem;
use App\Models\Option;
use App\Models\Question;
use App\Models\Roulette;
use App\Models\RouletteItem;
use App\Models\SchoolClass;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Word;
use App\Models\Wordsearch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameTestingActivitySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $class = SchoolClass::query()
                ->where('code', 'PROGWEB01')
                ->firstOrFail();

            $teacher = User::query()->findOrFail($class->teacher_id);
            $demoStudents = User::query()
                ->whereIn('email', [
                    'student1@aulaplay.com',
                    'student2@aulaplay.com',
                    'student3@aulaplay.com',
                    'student4@aulaplay.com',
                    'student5@aulaplay.com',
                ])
                ->where('role', 'student')
                ->orderBy('email')
                ->get();

            foreach ($demoStudents as $student) {
                Enrollment::query()->updateOrCreate(
                    [
                        'class_id' => $class->id,
                        'student_id' => $student->id,
                    ],
                    ['status' => 'active']
                );
            }

            $students = Enrollment::query()
                ->where('class_id', $class->id)
                ->where('status', 'active')
                ->with('student')
                ->get()
                ->pluck('student')
                ->filter(static fn (?User $student): bool => $student?->role === 'student')
                ->values();

            $games = [
                'word_search' => 'Sopa de letras',
                'crossword' => 'Crucigrama',
                'matching' => 'Relacionar',
                'kahoot' => 'Kahoot',
                'roulette' => 'Ruleta',
            ];

            foreach ($games as $type => $gameName) {
                foreach (['individual', 'team'] as $mode) {
                    $activity = Activity::query()->updateOrCreate(
                        [
                            'class_id' => $class->id,
                            'title' => 'PRUEBA - '.$gameName.' - '.($mode === 'team' ? 'Equipos' : 'Individual'),
                        ],
                        [
                            'teacher_id' => $teacher->id,
                            'description' => "Actividad de prueba ({$mode}) para {$gameName}.",
                            'type' => $type,
                            'mode' => $mode,
                            'max_score' => 100,
                            'time_limit' => 600,
                            'attempts' => 3,
                            'due_at' => null,
                            'status' => 'published',
                        ]
                    );

                    $this->configureGame($activity);

                    if ($mode === 'team') {
                        $this->assignStudentsToTeams($activity, $students->all());
                    }
                }
            }
        });
    }

    private function configureGame(Activity $activity): void
    {
        match ($activity->type) {
            'word_search' => $this->configureWordsearch($activity),
            'crossword' => $this->configureCrossword($activity),
            'matching' => $this->configureMatching($activity),
            'kahoot' => $this->configureKahoot($activity),
            'roulette' => $this->configureRoulette($activity),
        };
    }

    private function configureWordsearch(Activity $activity): void
    {
        $placements = [
            ['word' => 'LARAVEL', 'row' => 0, 'column' => 0, 'direction' => 'horizontal'],
            ['word' => 'PHP', 'row' => 0, 'column' => 7, 'direction' => 'vertical'],
            ['word' => 'CODIGO', 'row' => 3, 'column' => 0, 'direction' => 'horizontal'],
            ['word' => 'DATOS', 'row' => 0, 'column' => 9, 'direction' => 'vertical'],
        ];
        $grid = array_fill(0, 10, array_fill(0, 10, 'A'));

        foreach ($placements as $placement) {
            foreach (str_split($placement['word']) as $offset => $letter) {
                $row = $placement['row'] + ($placement['direction'] === 'vertical' ? $offset : 0);
                $column = $placement['column'] + ($placement['direction'] === 'horizontal' ? $offset : 0);
                $grid[$row][$column] = $letter;
            }
        }

        $wordsearch = Wordsearch::query()->updateOrCreate(
            ['activity_id' => $activity->id],
            ['rows' => 10, 'columns' => 10, 'grid' => $grid]
        );

        foreach ($placements as $placement) {
            Word::query()->updateOrCreate(
                [
                    'wordsearch_id' => $wordsearch->id,
                    'word' => $placement['word'],
                ],
                [
                    'row' => $placement['row'],
                    'column' => $placement['column'],
                    'direction' => $placement['direction'],
                    'score' => 25,
                ]
            );
        }
    }

    private function configureCrossword(Activity $activity): void
    {
        $placements = [
            ['word' => 'CASA', 'clue' => 'Lugar donde vivimos', 'row' => 1, 'column' => 0, 'direction' => 'horizontal'],
            ['word' => 'SOL', 'clue' => 'Estrella que ilumina la Tierra', 'row' => 1, 'column' => 2, 'direction' => 'vertical'],
            ['word' => 'AVE', 'clue' => 'Animal con plumas', 'row' => 1, 'column' => 3, 'direction' => 'vertical'],
        ];
        $grid = array_fill(0, 5, array_fill(0, 5, null));

        foreach ($placements as $placement) {
            foreach (str_split($placement['word']) as $offset => $letter) {
                $row = $placement['row'] + ($placement['direction'] === 'vertical' ? $offset : 0);
                $column = $placement['column'] + ($placement['direction'] === 'horizontal' ? $offset : 0);
                $grid[$row][$column] = $letter;
            }
        }

        $crossword = Crossword::query()->updateOrCreate(
            ['activity_id' => $activity->id],
            ['rows' => 5, 'columns' => 5, 'grid' => $grid]
        );

        foreach ($placements as $placement) {
            CrosswordWord::query()->updateOrCreate(
                [
                    'crossword_id' => $crossword->id,
                    'word' => $placement['word'],
                    'direction' => $placement['direction'],
                ],
                [
                    'clue' => $placement['clue'],
                    'row' => $placement['row'],
                    'column' => $placement['column'],
                    'score' => match ($placement['word']) {
                        'CASA' => 34,
                        default => 33,
                    },
                ]
            );
        }
    }

    private function configureMatching(Activity $activity): void
    {
        $matching = Matching::query()->updateOrCreate(
            ['activity_id' => $activity->id],
            []
        );

        $items = [
            ['left_text' => 'HTML', 'right_text' => 'Estructura de una página'],
            ['left_text' => 'CSS', 'right_text' => 'Estilos visuales'],
            ['left_text' => 'JavaScript', 'right_text' => 'Interactividad'],
            ['left_text' => 'Laravel', 'right_text' => 'Framework de PHP'],
        ];

        foreach ($items as $item) {
            MatchingItem::query()->updateOrCreate(
                [
                    'matching_id' => $matching->id,
                    'left_text' => $item['left_text'],
                ],
                ['right_text' => $item['right_text'], 'score' => 25]
            );
        }
    }

    private function configureKahoot(Activity $activity): void
    {
        $kahoot = Kahoot::query()->updateOrCreate(
            ['activity_id' => $activity->id],
            []
        );

        $questions = [
            ['question' => '¿Qué lenguaje estructura una página web?', 'correct' => 'HTML'],
            ['question' => '¿Qué lenguaje se usa principalmente para estilos?', 'correct' => 'CSS'],
            ['question' => '¿Qué lenguaje agrega interactividad en el navegador?', 'correct' => 'JavaScript'],
            ['question' => '¿En qué lenguaje está escrito Laravel?', 'correct' => 'PHP'],
        ];

        foreach ($questions as $index => $data) {
            $question = Question::query()->updateOrCreate(
                ['kahoot_id' => $kahoot->id, 'position' => $index + 1],
                [
                    'question' => $data['question'],
                    'time_limit' => 30,
                    'score' => 25,
                ]
            );

            $options = array_values(array_unique([
                $data['correct'],
                'HTML',
                'CSS',
                'JavaScript',
                'PHP',
            ]));

            foreach ($options as $optionIndex => $optionText) {
                Option::query()->updateOrCreate(
                    ['question_id' => $question->id, 'position' => $optionIndex + 1],
                    [
                        'text' => $optionText,
                        'is_correct' => $optionText === $data['correct'],
                    ]
                );
            }
        }
    }

    private function configureRoulette(Activity $activity): void
    {
        $roulette = Roulette::query()->updateOrCreate(
            ['activity_id' => $activity->id],
            []
        );

        $items = [
            ['question' => '¿Qué etiqueta contiene el título principal?', 'options' => ['<h1>', '<p>', '<img>', '<br>'], 'correct' => 'a'],
            ['question' => '¿Qué propiedad CSS cambia el color del texto?', 'options' => ['font-size', 'color', 'margin', 'display'], 'correct' => 'b'],
            ['question' => '¿Qué palabra declara una constante en JavaScript?', 'options' => ['let', 'var', 'const', 'static'], 'correct' => 'c'],
            ['question' => '¿Qué comando inicia el servidor local de Laravel?', 'options' => ['php artisan serve', 'npm run build', 'composer update', 'php artisan migrate'], 'correct' => 'a'],
        ];

        foreach ($items as $item) {
            RouletteItem::query()->updateOrCreate(
                ['roulette_id' => $roulette->id, 'question' => $item['question']],
                [
                    'option_a' => $item['options'][0],
                    'option_b' => $item['options'][1],
                    'option_c' => $item['options'][2],
                    'option_d' => $item['options'][3],
                    'correct_option' => $item['correct'],
                    'points' => 25,
                ]
            );
        }
    }

    /**
     * @param  array<int, User>  $students
     */
    private function assignStudentsToTeams(Activity $activity, array $students): void
    {
        $teams = [];

        foreach (['Equipo Sol', 'Equipo Luna', 'Equipo Estrella'] as $teamName) {
            $teams[] = Team::query()->updateOrCreate(
                ['activity_id' => $activity->id, 'name' => $teamName],
                []
            );
        }

        $teamIds = array_map(static fn (Team $team): int => $team->id, $teams);
        TeamMember::query()->whereIn('team_id', $teamIds)->delete();

        foreach ($students as $index => $student) {
            TeamMember::query()->create([
                'team_id' => $teams[$index % count($teams)]->id,
                'student_id' => $student->id,
            ]);
        }
    }
}
