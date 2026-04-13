<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('test_types')->insert([
            [
                'name' => 'Radiografia Simple',
                'time' => 15
            ],
            [
                'name' => 'Radiografia Tòrax',
                'time' => 5
            ],
            [
                'name' => 'Radiografia Pelvis',
                'time' => 8
            ],
            [
                'name' => 'Resonància Magnètica',
                'time' => 60
            ],
            [
                'name' => 'Resonància Magnètica Lumbar',
                'time' => 20
            ],
            [
                'name' => 'Resonància Magnètica Cranial',
                'time' => 25
            ],
            [
                'name' => 'Ecografia',
                'time' => 15
            ],
            [
                'name' => 'Ecografia Renal',
                'time' => 10
            ],
            [
                'name' => 'Ecografia Cardíaca',
                'time' => 15
            ], 
            [
                'name' => 'Mamografia',
                'time' => 10
            ],
            [
                'name' => 'Mamografia Craneocaudal',
                'time' => 5
            ],
            [
                'name' => 'Mamografia Obliqua',
                'time' => 5
            ],
            [
                'name' => 'Tomografía axial computada (TAC)',
                'time' => 30
            ],
            [
                'name' => 'Tomografía axial computada (TAC) Abdomen',
                'time' => 10
            ],
            [
                'name' => 'Tomografía axial computada (TAC) Crani',
                'time' => 10
            ],
        ]);
    }
}
