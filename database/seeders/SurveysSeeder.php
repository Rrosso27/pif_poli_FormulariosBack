<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Surveys;

class SurveysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a sample survey
        Surveys::create([
            'title' => 'Vamos a descubrir en qué destacas',
            'description' => 'Esta encuesta tiene como objetivo identificar tus conocimientos y habilidades en las competencias requeridas.',
        ]);
    }
}
