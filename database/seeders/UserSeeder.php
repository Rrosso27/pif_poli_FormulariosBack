<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

//         Datos de prueba:

// Email: nicolas@gmail.com

// Password: 122345678
        // Create a sample user
        User::create([
            'name' => 'users',
            'email' => 'nicolas@gmail.com',
            'password' => bcrypt('122345678'),
        ]);
    }
}
