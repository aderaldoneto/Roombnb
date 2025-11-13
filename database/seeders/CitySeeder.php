<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'Salvador', 'state' => 'BA'],
            ['name' => 'Juazeiro', 'state' => 'BA'],
            ['name' => 'Feira de Santana', 'state' => 'BA'],
            ['name' => 'Vitória da Conquista', 'state' => 'BA'],

            ['name' => 'Recife', 'state' => 'PE'],
            ['name' => 'Petrolina', 'state' => 'PE'],
            ['name' => 'Caruaru', 'state' => 'PE'],
            ['name' => 'Olinda', 'state' => 'PE'],

            ['name' => 'São Paulo', 'state' => 'SP'],
            ['name' => 'Campinas', 'state' => 'SP'],
            ['name' => 'Santos', 'state' => 'SP'],
            ['name' => 'São José dos Campos', 'state' => 'SP'],
            ['name' => 'Ribeirão Preto', 'state' => 'SP'],

            ['name' => 'Rio de Janeiro', 'state' => 'RJ'],
            ['name' => 'Niterói', 'state' => 'RJ'],
            ['name' => 'São Gonçalo', 'state' => 'RJ'],
            ['name' => 'Duque de Caxias', 'state' => 'RJ'],
            ['name' => 'Volta Redonda', 'state' => 'RJ'],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                [
                    'name' => $city['name'], 
                    'state' => $city['state'],
                ]);
        }
    }
}
