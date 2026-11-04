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
use App\Models\Unit;
use App\Models\User;
use App\Models\Word;
use App\Models\Wordsearch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed demo "escuela real": 3 profesores, 24 alumnos, 9 clases repartidas en
 * distintos periodos (su created_at define el periodo inferido), unidades,
 * actividades de los 5 juegos con contenido por materia, equipos, sesiones
 * terminadas con participaciones y puntajes, y sesiones abiertas.
 *
 * Idempotente: todo se genera con updateOrCreate/firstOrCreate. Las claves
 * de cuenta son siempre los mismos emails/códigos.
 */
class DemoSchoolSeeder extends Seeder
{
    private const DRAFTS_PER_CLASS = 2;

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->seedTeachers();
            $students = $this->seedStudents();

            foreach ($this->classDefinitions() as $definition) {
                $class = $this->seedClass($definition);
                $this->seedActivities($class, $definition, $this->enroll($class, $definition, $students));
            }
        });
    }

    /**
     * Definición de las 9 clases: profesor, materia, fecha (que define el
     * periodo), estado y bloque de alumnos inscritos.
     *
     * @return list<array{teacher_email: string, subject: string, name: string, code: string, created: string, status: string, students: int, from: int}>
     */
    private function classDefinitions(): array
    {
        return [
            // Periodo en curso (2026-H2)
            ['teacher_email' => 'teacher@aulaplay.com', 'subject' => 'matematicas', 'name' => 'Matemáticas 6° A', 'code' => 'MATE6A01', 'created' => '2026-09-08', 'status' => 'active', 'students' => 22, 'from' => 1],
            ['teacher_email' => 'teacher@aulaplay.com', 'subject' => 'lengua', 'name' => 'Lengua y Literatura 6° A', 'code' => 'LENG6A01', 'created' => '2026-09-10', 'status' => 'active', 'students' => 20, 'from' => 3],
            ['teacher_email' => 'ana.torres@aulaplay.com', 'subject' => 'geografia', 'name' => 'Geografía 6° C', 'code' => 'GEOG6C01', 'created' => '2026-09-05', 'status' => 'active', 'students' => 18, 'from' => 7],
            ['teacher_email' => 'luis.ramirez@aulaplay.com', 'subject' => 'programacion', 'name' => 'Programación Web', 'code' => 'PROGWEB01', 'created' => '2026-08-20', 'status' => 'active', 'students' => 24, 'from' => 1],

            // Periodo pasado (2026-H1)
            ['teacher_email' => 'teacher@aulaplay.com', 'subject' => 'ciencias', 'name' => 'Ciencias Naturales 5° B', 'code' => 'CIEN5B01', 'created' => '2026-02-15', 'status' => 'active', 'students' => 16, 'from' => 5],
            ['teacher_email' => 'ana.torres@aulaplay.com', 'subject' => 'civica', 'name' => 'Educación Cívica 6° B', 'code' => 'CIVI6B01', 'created' => '2026-02-02', 'status' => 'active', 'students' => 14, 'from' => 9],
            ['teacher_email' => 'luis.ramirez@aulaplay.com', 'subject' => 'matematicas', 'name' => 'Matemáticas 5° C', 'code' => 'MATE5C01', 'created' => '2026-01-20', 'status' => 'archived', 'students' => 12, 'from' => 11],

            // Periodos anteriores (2025)
            ['teacher_email' => 'ana.torres@aulaplay.com', 'subject' => 'historia', 'name' => 'Historia Universal 5° A', 'code' => 'HIST5A01', 'created' => '2025-08-25', 'status' => 'archived', 'students' => 10, 'from' => 13],
            ['teacher_email' => 'luis.ramirez@aulaplay.com', 'subject' => 'ingles', 'name' => 'Inglés Básico 5° B', 'code' => 'INGL5B01', 'created' => '2025-02-10', 'status' => 'archived', 'students' => 10, 'from' => 15],
        ];
    }

    /**
     * Profesores del demo (3) + admin y estudiantes existen en seeders base.
     */
    private function seedTeachers(): void
    {
        foreach ([
            ['name' => 'Maestro Demo', 'email' => 'teacher@aulaplay.com'],
            ['name' => 'Ana Torres', 'email' => 'ana.torres@aulaplay.com'],
            ['name' => 'Luis Ramírez', 'email' => 'luis.ramirez@aulaplay.com'],
        ] as $teacher) {
            User::updateOrCreate(
                ['email' => $teacher['email']],
                [
                    'name' => $teacher['name'],
                    'password' => bcrypt('password'),
                    'role' => 'teacher',
                    'status' => 'active',
                ]
            );
        }
    }

    /**
     * 24 alumnos: student1..5@aulaplay.com (para multi+tests) + 19 nuevos
     * con nombre real en formato alumno.N.nombredeslido@aulaplay.com.
     *
     * @return list<User>
     */
    private function seedStudents(): array
    {
        $students = [];

        for ($i = 1; $i <= 5; $i++) {
            $students[] = User::updateOrCreate(
                ['email' => "student{$i}@aulaplay.com"],
                [
                    'name' => 'Estudiante '.$i,
                    'password' => bcrypt('password'),
                    'role' => 'student',
                    'status' => 'active',
                ]
            );
        }

        foreach ([
            'Kevin Morales', 'Sofía Herrera', 'Diego Castillo', 'Valentina Ríos',
            'Mateo Vargas', 'Isabella Torres', 'Santiago Pérez', 'Camila Flores',
            'Nicolás Aguilar', 'Regina Castro', 'Emiliano Luna', 'Renata Soto',
            'Gabriel Ortiz', 'María José Vega', 'Andrés Pineda', 'Fernanda Silva',
            'Adrián Rojas', 'Lucía Medina', 'Bruno Cárdenas',
        ] as $index => $name) {
            $slug = strtolower((string) preg_replace('/[áéíóúÁÉÍÓÚ]/', 'a', $name));

            $students[] = User::updateOrCreate(
                ['email' => 'alumno'.($index + 1).'@aulaplay.com'],
                [
                    'name' => $name,
                    'password' => bcrypt('password'),
                    'role' => 'student',
                    'status' => 'active',
                ]
            );
        }

        return $students;
    }

    /**
     * Crea la clase con su fecha (periodo inferido por created_at) y unidades.
     *
     * @param  array{teacher_email: string, subject: string, name: string, code: string, created: string, status: string, students: int, from: int}  $definition
     */
    private function seedClass(array $definition): SchoolClass
    {
        $teacher = User::where('email', $definition['teacher_email'])->firstOrFail();
        $subject = DemoGameCatalog::subject($definition['subject']);

        $class = SchoolClass::updateOrCreate(
            ['code' => $definition['code']],
            [
                'teacher_id' => $teacher->id,
                'name' => $definition['name'],
                'description' => $subject['description'],
                'status' => $definition['status'],
            ]
        );

        SchoolClass::whereKey($class->id)->update([
            'created_at' => $definition['created'].' 10:30:00',
        ]);
        $class->refresh();

        foreach ($subject['units'] as $number => $unitName) {
            Unit::updateOrCreate(
                ['class_id' => $class->id, 'number' => $number + 1],
                ['name' => $unitName]
            );
        }

        return $class;
    }

    /**
     * Inscribe un bloque rotado de alumnos; cada clase queda con distinto
     * conjunto y cada alumno en varias clases.
     *
     * @param  array{teacher_email: string, subject: string, name: string, code: string, created: string, status: string, students: int, from: int}  $definition
     * @param  list<User>  $students
     * @return list<User>
     */
    private function enroll(SchoolClass $class, array $definition, array $students): array
    {
        $offset = ($definition['from'] - 1) % count($students);
        $rotated = array_values(array_merge(
            array_slice($students, $offset),
            array_slice($students, 0, $offset)
        ));

        $picked = array_slice($rotated, 0, $definition['students']);

        foreach ($picked as $student) {
            Enrollment::updateOrCreate(
                ['class_id' => $class->id, 'student_id' => $student->id],
                ['status' => 'active']
            );
        }

        return $picked;
    }

    /**
     * Por unidad: 1 actividad publicada con su juego (tipo rotativo por
     * materia) + 2 borradores al final; unidades cada 2 una actividad en
     * equipo (kahoot).
     *
     * @param  array{teacher_email: string, subject: string, name: string, code: string, created: string, status: string, students: int, from: int}  $definition
     * @param  list<User>  $enrolled
     */
    private function seedActivities(SchoolClass $class, array $definition, array $enrolled): void
    {
        $subject = DemoGameCatalog::subject($definition['subject']);
        $rotation = ['kahoot', 'crossword', 'word_search', 'matching', 'roulette'];
        $units = $class->units;

        foreach (array_values($units->all()) as $unitIndex => $unit) {
            $type = $rotation[($unitIndex + $subject['rotation']) % count($rotation)];
            $team = $type === 'kahoot' && $unitIndex % 2 === 0;

            $activity = Activity::updateOrCreate(
                [
                    'class_id' => $class->id,
                    'title' => $unit->name.' — '.$this->typeLabel($type),
                ],
                [
                    'teacher_id' => $class->teacher_id,
                    'description' => 'Actividad de la unidad «'.$unit->name.'» · '.$class->name.'.',
                    'type' => $type,
                    'mode' => $team ? 'team' : 'individual',
                    'max_score' => 100,
                    'time_limit' => 300 + ($unitIndex % 4) * 90,
                    'attempts' => $unitIndex % 2 === 0 ? 2 : 1,
                    'due_at' => $class->status === 'active' ? now()->addDays(7 + $unitIndex * 7) : null,
                    'status' => 'published',
                    'unit_id' => $unit->id,
                ]
            );

            $this->fillGame($activity, $definition['subject']);

            if ($team && $enrolled !== []) {
                $this->assignTeams($activity, $enrolled);
            }
        }

        for ($i = 0; $i < self::DRAFTS_PER_CLASS; $i++) {
            $type = $rotation[($units->count() + $i + $subject['rotation']) % count($rotation)];

            Activity::updateOrCreate(
                [
                    'class_id' => $class->id,
                    'title' => $this->typeLabel($type).' extra — edición '.($i + 1),
                ],
                [
                    'teacher_id' => $class->teacher_id,
                    'description' => 'Borrador pendiente de revisión para '.$class->name.'.',
                    'type' => $type,
                    'mode' => 'individual',
                    'max_score' => 100,
                    'time_limit' => 600,
                    'attempts' => 2,
                    'due_at' => null,
                    'status' => 'draft',
                    'unit_id' => $units->first()?->id,
                ]
            );
        }
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            'kahoot' => 'Kahoot',
            'crossword' => 'Crucigrama',
            'word_search' => 'Sopa de letras',
            'matching' => 'Relaciones',
            'roulette' => 'Ruleta',
            default => $type,
        };
    }

    /**
     * Crea el contenido del juego si aún no lo tiene; idempotente por
     * diseño (cada tabla lleva la clave activity_id / identificadores).
     */
    private function fillGame(Activity $activity, string $subjectKey): void
    {
        $subject = DemoGameCatalog::subject($subjectKey);

        match ($activity->type) {
            'kahoot' => $this->fillKahoot($activity, $subject['kahoot']),
            'crossword' => $this->fillCrossword($activity, $subject['crossword']),
            'word_search' => $this->fillWordsearch($activity, $subject['wordsearch']),
            'matching' => $this->fillMatching($activity, $subject['matching']),
            'roulette' => $this->fillRoulette($activity, $subject['roulette']),
        };
    }

    /**
     * @param  list<array{q: string, a: string, wrong: list<string>}>  $questions
     */
    private function fillKahoot(Activity $activity, array $questions): void
    {
        $kahoot = Kahoot::firstOrCreate(['activity_id' => $activity->id]);

        if ($kahoot->questions()->exists()) {
            return;
        }

        foreach ($questions as $index => $data) {
            $question = Question::create([
                'kahoot_id' => $kahoot->id,
                'position' => $index + 1,
                'question' => $data['q'],
                'time_limit' => 30,
                'score' => (int) floor(100 / count($questions)),
            ]);

            foreach (array_values(array_unique([$data['a'], ...$data['wrong']])) as $position => $text) {
                Option::create([
                    'question_id' => $question->id,
                    'position' => $position + 1,
                    'text' => $text,
                    'is_correct' => $position === 0,
                ]);
            }
        }
    }

    /**
     * @param  list<array{0: string, 1: string}>  $pairs
     */
    private function fillCrossword(Activity $activity, array $pairs): void
    {
        $crossword = Crossword::firstOrCreate(
            ['activity_id' => $activity->id],
            ['rows' => count($pairs) + 1, 'columns' => 12, 'grid' => array_fill(0, count($pairs) + 1, array_fill(0, 12, null))]
        );

        if ($crossword->words()->exists()) {
            return;
        }

        $grid = array_fill(0, count($pairs) + 1, array_fill(0, 12, null));

        foreach ($pairs as $index => [$word, $clue]) {
            foreach (str_split($word) as $column => $letter) {
                $grid[$index + 1][$column] = $letter;
            }

            CrosswordWord::create([
                'crossword_id' => $crossword->id,
                'word' => $word,
                'direction' => 'horizontal',
                'clue' => $clue,
                'row' => $index + 1,
                'column' => 0,
                'score' => (int) floor(100 / count($pairs)),
            ]);
        }

        $crossword->update(['grid' => $grid]);
    }

    /**
     * @param  list<string>  $words
     */
    private function fillWordsearch(Activity $activity, array $words): void
    {
        $rows = count($words) + 2;
        $columns = 12;
        $grid = [];

        for ($row = 0; $row < $rows; $row++) {
            for ($column = 0; $column < $columns; $column++) {
                $grid[$row][$column] = chr(65 + ($row * 7 + $column * 13) % 26);
            }
        }

        foreach ($words as $index => $word) {
            foreach (str_split($word) as $column => $letter) {
                $grid[$index + 1][$column] = $letter;
            }
        }

        $wordsearch = Wordsearch::firstOrCreate(
            ['activity_id' => $activity->id],
            ['rows' => $rows, 'columns' => $columns, 'grid' => $grid]
        );

        if ($wordsearch->words()->exists()) {
            return;
        }

        foreach ($words as $index => $word) {
            Word::create([
                'wordsearch_id' => $wordsearch->id,
                'word' => $word,
                'row' => $index + 1,
                'column' => 0,
                'direction' => 'horizontal',
                'score' => (int) floor(100 / count($words)),
            ]);
        }
    }

    /**
     * @param  list<array{0: string, 1: string}>  $pairs
     */
    private function fillMatching(Activity $activity, array $pairs): void
    {
        $matching = Matching::firstOrCreate(['activity_id' => $activity->id]);

        if ($matching->items()->exists()) {
            return;
        }

        foreach ($pairs as [$left, $right]) {
            MatchingItem::create([
                'matching_id' => $matching->id,
                'left_text' => $left,
                'right_text' => $right,
                'score' => (int) floor(100 / count($pairs)),
            ]);
        }
    }

    /**
     * @param  list<array{q: string, options: array{0: string, 1: string, 2: string, 3: string}, correct: string}>  $items
     */
    private function fillRoulette(Activity $activity, array $items): void
    {
        $roulette = Roulette::firstOrCreate(['activity_id' => $activity->id]);

        if ($roulette->items()->exists()) {
            return;
        }

        foreach ($items as $item) {
            RouletteItem::create([
                'roulette_id' => $roulette->id,
                'question' => $item['q'],
                'option_a' => $item['options'][0],
                'option_b' => $item['options'][1],
                'option_c' => $item['options'][2],
                'option_d' => $item['options'][3],
                'correct_option' => $item['correct'],
                'points' => (int) floor(100 / count($items)),
            ]);
        }
    }

    /**
     * Equipos equilibrados (2-3) repartidos entre los alumnos inscritos.
     *
     * @param  list<User>  $students
     */
    private function assignTeams(Activity $activity, array $students): void
    {
        $teamNames = ['Equipo Sol', 'Equipo Luna', 'Equipo Estrella'];
        $teams = [];

        foreach (array_slice($teamNames, 0, count($students) >= 12 ? 3 : 2) as $name) {
            $teams[] = Team::updateOrCreate(['activity_id' => $activity->id, 'name' => $name], []);
        }

        $teamIds = array_map(static fn (Team $team): int => $team->id, $teams);
        TeamMember::whereIn('team_id', $teamIds)->delete();

        foreach ($students as $index => $student) {
            TeamMember::create([
                'team_id' => $teams[$index % count($teams)]->id,
                'student_id' => $student->id,
            ]);
        }
    }
}
