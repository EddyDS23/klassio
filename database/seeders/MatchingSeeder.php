<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Services\MatchingService;
use Illuminate\Database\Seeder;

class MatchingSeeder extends Seeder
{
    public function run(MatchingService $service): void
    {
        $activity = Activity::query()->where('type', 'matching')->firstOrFail();

        $service->buildMatching(
            $activity,
            items: [
                ['left' => 'HTTP', 'right' => 'Protocolo de transferencia de hipertexto', 'score' => 10],
                ['left' => 'HTML', 'right' => 'Lenguaje de marcado para la web', 'score' => 10],
                ['left' => 'CSS', 'right' => 'Hoja de estilos en cascada', 'score' => 10],
                ['left' => 'Router', 'right' => 'Define las rutas de la aplicación', 'score' => 10],
                ['left' => 'Controller', 'right' => 'Maneja las peticiones HTTP', 'score' => 10],
            ]
        );

        $this->command?->info("Actividad de unir conceptos generada para la actividad #{$activity->id}.");
    }
}