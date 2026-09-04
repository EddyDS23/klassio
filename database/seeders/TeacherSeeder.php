<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'teacher@aulaplay.com'],
            [
                'name' => 'Maestro Demo',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'status' => 'active',
            ]
        );
    }
}