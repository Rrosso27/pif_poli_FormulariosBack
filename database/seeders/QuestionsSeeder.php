<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Questions;

class QuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample questions
        Questions::create([
            "survey_id" => 1,
            "question_text" => "¿Cuál es tu framework favorito y por qué?",
            "question_type" => "text",
            "options" => null
        ]);

        Questions::create([
            "survey_id" => 1,
            "question_text" => "¿Cuál es tu nivel de experiencia en Laravel?",
            "question_type" => "single",
            "options" => ["Junior", "Mid", "Senior"]
        ]);

        Questions::create([
            "survey_id" => 1,
            "question_text" => "¿Cuál es tu nivel de experiencia en React?",
            "question_type" => "single",
            "options" => ["Junior", "Mid", "Senior"]
        ]);


        Questions::create([
            "survey_id" => 1,
            "question_text" => "¿Qué lenguajes de programación conoces?",
            "question_type" => "multiple",
            "options" => ["JavaScript", "C#", "Python", "Java"]
        ]);

        Questions::create([
            "survey_id" => 1,
            "question_text" => "¿Qué gestor de base de datos conoces ?",
            "question_type" => "multiple",
            "options" => ["Sql", "Mysql", "Oracle", "Postgresql"]
        ]);

        Questions::create([
            "survey_id" => 1,
            "question_text" => "En una escala del 1 al 5, ¿qué tanto te gusta trabajar en equipo?",
            "question_type" => "scale",
            "options" => [1, 2, 3, 4, 5]
        ]);

        Questions::create([
            "survey_id" => 1,
            "question_text" => "¿Has trabajado con metodologías ágiles?",
            "question_type" => "boolean",
            "options" => ["Sí", "No"]
        ]);
    }
}
