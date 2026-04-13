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
                'name' => 'Pacient',
                'email' => 'pacient@gmail.com',
                'role' => 'user',
            ],
            [
                'id' => 2,
                'name' => 'Administrador',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
            ],
            [
                'id' => 3,
                'name' => 'Doctor',
                'email' => 'doctor@gmail.com',
                'role' => 'worker',
            ],
            [
                'id' => 4,
                'name' => 'Secretari',
                'email' => 'secretari@gmail.com',
                'role' => 'worker',
            ],
        ];

        DB::table('users')->insert($users);

        DB::table('admins')->insert([
            'user_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('workers')->insert([
            [
                'user_id' => 3,
                'nss' => 'NSS123456',
                'adress' => 'Calle Doctor 123',
                'dni' => '12345678A',
                'phone' => 600000001,
                'password' => Hash::make('password123'),
                'worker_role' => 'doctor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'nss' => 'NSS654321',
                'adress' => 'Calle Secretaria 456',
                'dni' => '87654321B',
                'phone' => 600000002,
                'password' => Hash::make('password123'),
                'worker_role' => 'secretary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('patients')->insert([
            'user_id' => 1,
            'nts' => 'NTS123456',
            'adress' => 'Calle Usuario 789',
            'dni' => '11223344C',
            'phone' => 600000003,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}