<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Analgesicos',           'Medicamentos para el alivio del dolor',                '#ef4444'],
            ['Antibioticos',          'Agentes antimicrobianos',                              '#8b5cf6'],
            ['Antiinflamatorios',     'Reduccion de inflamacion y dolor',                     '#f97316'],
            ['Antihistaminicos',      'Tratamiento de alergias',                              '#ec4899'],
            ['Antihipertensivos',     'Control de presion arterial',                          '#06b6d4'],
            ['Antidiabeticos',        'Control de glucosa en sangre',                         '#14b8a6'],
            ['Gastrointestinales',    'Tratamiento del aparato digestivo',                    '#eab308'],
            ['Vitaminas/Suplementos', 'Suplementos vitaminicos y minerales',                  '#22c55e'],
            ['Dermatologicos',        'Tratamiento topico de piel',                           '#a855f7'],
            ['Respiratorios',         'Tratamiento de vias respiratorias',                    '#3b82f6'],
            ['Otros',                 'Medicamentos y productos no clasificados',             '#6b7280'],
        ];

        foreach ($categories as [$name, $description, $color]) {
            Category::firstOrCreate(
                ['name' => $name],
                ['description' => $description, 'color' => $color],
            );
        }
    }
}
