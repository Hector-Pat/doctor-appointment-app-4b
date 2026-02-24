<?php

namespace Database\Seeders;

use App\Models\Speciality;
use Illuminate\Database\Seeder;

class SpecialitySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Cardiología',
            'Pediatría',
            'Dermatología',
            'Neurología',
            'Ginecología',
            'Ortopedia',
            'Oftalmología',
            'Psiquiatría',
        ];

        foreach ($items as $name) {
            Speciality::firstOrCreate(['name' => $name]);
        }
    }
}
