<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private const UNITS = [
        'Introducción al desarrollo web',
        'HTML y CSS',
        'JavaScript',
        'PHP y bases de datos',
    ];

    public function run(): void
    {
        $teacher = User::where('email', 'teacher@aulaplay.com')->firstOrFail();

        $class = SchoolClass::updateOrCreate(
            ['code' => 'PROGWEB01'],
            [
                'teacher_id' => $teacher->id,
                'name' => 'Programación Web',
                'description' => 'Clase demo de Programación Web.',
                'status' => 'active',
            ]
        );

        foreach (self::UNITS as $number => $name) {
            Unit::updateOrCreate(
                ['class_id' => $class->id, 'number' => $number],
                ['name' => $name]
            );
        }
    }
}
