<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'name' => 'Estudiante 1',
                'email' => 'student1@aulaplay.com',
            ],
            [
                'name' => 'Estudiante 2',
                'email' => 'student2@aulaplay.com',
            ],
            [
                'name' => 'Estudiante 3',
                'email' => 'student3@aulaplay.com',
            ],
            [
                'name' => 'Estudiante 4',
                'email' => 'student4@aulaplay.com',
            ],
            [
                'name' => 'Estudiante 5',
                'email' => 'student5@aulaplay.com',
            ],
        ];

        foreach ($students as $student) {
            User::updateOrCreate(
                ['email' => $student['email']],
                [
                    'name' => $student['name'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'status' => 'active',
                ]
            );
        }
    }
}