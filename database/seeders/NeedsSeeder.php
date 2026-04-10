<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NeedsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('needs')->insert([
            [
                'name' => 'Claustrofòbia',
                'time' => 15
            ],
            [
                'name' => 'Mobilitat reduïda',
                'time' => 15
            ],
            [
                'name' => 'Pacient enllitat',
                'time' => 15
            ],
            [
                'name' => 'Pacient pediàtric',
                'time' => 15
            ],
            [
                'name' => 'Dolor agut',
                'time' => 10
            ],
            [
                'name' => 'Deteriorament cognitiu',
                'time' => 15
            ],
            [
                'name' => 'Discapacitat auditiva',
                'time' => 10
            ],
            [
                'name' => 'Discapacitat visual',
                'time' => 10
            ],
            [
                'name' => 'Obesitat',
                'time' => 10
            ],
            [
                'name' => 'Ansietat',
                'time' => 10
            ],
            [
                'name' => 'Contrast intravenós',
                'time' => 15
            ],
            [
                'name' => 'Antecedents d’al·lèrgia al contrast',
                'time' => 5
            ],
            [
                'name' => 'Dispositius mèdics',
                'time' => 10
            ],
            [
                'name' => 'Pacient no col·laborador',
                'time' => 20
            ],
            [
                'name' => 'Barrera idiomàtica',
                'time' => 10
            ],
            [
                'name' => 'Pacient amb oxigen o suport respiratori',
                'time' => 5
            ],
            [
                'name' => 'Pacient crític (UCI)',
                'time' => 20
            ],
            [
                'name' => 'Pacient amb infecció (aïllament)',
                'time' => 20
            ],
        ]);
    }
}
