<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            'Cardiologia',
            'Dermatologia',
            'Ortopedia',
            'Ginecologia',
            'Pediatria',
            'Psiquiatria',
            'Neurologia',
            'Endocrinologia',
            'Oftalmologia',
            'Otorrinolaringologia',
        ];

        foreach ($specialties as $name) {
            Specialty::updateOrCreate(['name' => $name]);
        }
    }
}
