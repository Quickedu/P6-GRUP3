<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Administrador',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ],
            [
                'id' => 2,
                'name' => 'Doctor',
                'email' => 'doctor@gmail.com',
                'role' => 'doctor',
                'password' => Hash::make('password123'),
            ],
            [
                'id' => 3,
                'name' => 'Secretari',
                'email' => 'secretari@gmail.com',
                'role' => 'secretary',
                'password' => Hash::make('password123'),
            ],
        ];

        DB::table('users')->insert($users);

        DB::table('workers')->insert([
            [
                'user_id' => 2,
                'nss' => 'NSS123456',
                'address' => 'Calle Doctor 123',
                'dni' => '12345678A',
                'phone' => 600000001,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'nss' => 'NSS654321',
                'address' => 'Calle Secretaria 456',
                'dni' => '87654321B',
                'phone' => 600000002,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('patients')->insert([
            'name' => 'Pacient',
            'nts' => 'NTS123456',
            'address' => 'Calle Paciente 789',
            'dni' => '11223344C',
            'phone' => 600000003,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
