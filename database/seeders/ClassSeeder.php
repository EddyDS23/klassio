<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('email', 'teacher@aulaplay.com')->firstOrFail();

        SchoolClass::updateOrCreate(
            ['code' => 'PROGWEB01'],
            [
                'teacher_id' => $teacher->id,
                'name' => 'Programación Web',
                'description' => 'Clase demo de Programación Web.',
                'status' => 'active',
            ]
        );
    }
}